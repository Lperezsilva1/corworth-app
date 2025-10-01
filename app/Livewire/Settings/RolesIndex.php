<?php
// app/Livewire/Settings/RolesIndex.php
namespace App\Livewire\Settings;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;

class RolesIndex extends Component
{
    public string $name = '';
    public ?int $editingId = null;

    /** Roles protegidos que NO se pueden borrar */
    public array $protected = ['Admin','Manager','Operations','Viewer'];

    protected function rules(): array
    {
        $ignore = $this->editingId ?: 'NULL';
        return [
            'name' => "required|string|min:3|max:50|unique:roles,name,{$ignore}",
        ];
    }

    public function create(): void
    {
        $this->authorizeAccess();
        $this->validate();
        Role::create(['name' => $this->name, 'guard_name' => 'web']);
        $this->reset(['name']);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        session()->flash('ok','Role created');
    }

    public function edit(int $id): void
    {
        $this->authorizeAccess();
        $r = Role::findOrFail($id);
        $this->editingId = $r->id;
        $this->name = $r->name;
    }

    public function update(): void
    {
        $this->authorizeAccess();
        $this->validate();
        $r = Role::findOrFail($this->editingId);
        if (in_array($r->name, $this->protected, true)) {
            session()->flash('err','This role is protected and cannot be renamed.');
            return;
        }
        $r->name = $this->name;
        $r->save();

        $this->reset(['name','editingId']);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        session()->flash('ok','Role updated');
    }

    public function delete(int $id): void
    {
        $this->authorizeAccess();
        $r = Role::findOrFail($id);
        if (in_array($r->name, $this->protected, true)) {
            session()->flash('err','This role is protected and cannot be deleted.');
            return;
        }

        // Quitar el rol de los usuarios y limpiar permisos
        $users = User::role($r->name)->get();
        foreach ($users as $u) { $u->removeRole($r); }
        $r->syncPermissions([]);
        $r->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        session()->flash('ok','Role deleted');
    }

    protected function authorizeAccess(): void
    {
        abort_unless(auth()->check() && auth()->user()->can('roles.manage'), 403);
    }

    public function render()
    {
        return view('livewire.settings.roles-index', [
            'roles' => Role::orderBy('name')->get(),
        ]);
    }
}
