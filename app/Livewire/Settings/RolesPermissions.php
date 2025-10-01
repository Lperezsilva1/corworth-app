<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesPermissions extends Component
{
    public ?int $roleId = null;

    /** Lista de permisos seleccionados (strings) */
    public array $selectedPerms = [];

    /** Permisos agrupados para pintar en la vista */
    public array $groups = [];

    public function mount(): void
    {
        $this->authorizeAccess();

        $first = Role::query()->orderBy('name')->first();
        $this->roleId = $first?->id;
        $this->loadForRole();
    }

    public function updatedRoleId(): void
    {
        $this->authorizeAccess();
        $this->loadForRole();
    }

    public function toggleAll(string $group, bool $checked): void
    {
        $this->authorizeAccess();

        $list = $this->groups[$group] ?? [];
        if ($checked) {
            // unir y deduplicar
            $this->selectedPerms = array_values(array_unique(array_merge($this->selectedPerms, $list)));
        } else {
            // remover los del grupo
            $this->selectedPerms = array_values(array_diff($this->selectedPerms, $list));
        }
    }

    public function save(): void
    {
        $this->authorizeAccess();

        $role = Role::findOrFail($this->roleId);

        // Guardamos exactamente lo que está marcado
        $toAssign = $this->selectedPerms;

        // Regla opcional: solo Admin puede activar user.create
        if (!auth()->user()->hasRole('Admin')) {
            $toAssign = array_values(array_diff($toAssign, ['user.create']));
        }

        $role->syncPermissions($toAssign);

        // refrescar caché de Spatie
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        session()->flash('ok', 'Permissions updated.');
        $this->dispatch('$refresh');
    }

    protected function loadForRole(): void
    {
        $this->selectedPerms = [];
        $this->groups = [];

        if (!$this->roleId) return;

        $role = Role::find($this->roleId);
        if (!$role) return;

        // permisos actuales del rol
        $current = $role->permissions()->pluck('name')->all();
        $this->selectedPerms = $current;

        // todas las opciones, agrupadas por prefijo
        $all = Permission::orderBy('name')->pluck('name')->all();
        $this->groups = [
            'Project'  => [],
            'Activity' => [],
            'Building' => [],
            'Seller'   => [],
            'Drafter'  => [],
            'Users'    => [],
            'Other'    => [],
        ];

        foreach ($all as $p) {
            if (str_starts_with($p, 'project.'))                    $this->groups['Project'][]  = $p;
            elseif ($p === 'activity.view')                         $this->groups['Activity'][] = $p;
            elseif (str_starts_with($p, 'building.'))               $this->groups['Building'][] = $p;
            elseif (str_starts_with($p, 'seller.'))                 $this->groups['Seller'][]   = $p;
            elseif (str_starts_with($p, 'drafter.'))                $this->groups['Drafter'][]  = $p;
            elseif (str_starts_with($p, 'user.') || $p==='roles.manage') $this->groups['Users'][] = $p;
            else                                                    $this->groups['Other'][]   = $p;
        }
    }

    protected function authorizeAccess(): void
    {
        abort_unless(auth()->check() && auth()->user()->can('roles.manage'), 403);
    }

    public function render()
    {
        return view('livewire.settings.roles-permissions', [
            'roles'  => Role::orderBy('name')->get(['id','name']),
            'groups' => $this->groups,
        ]);
    }
}
