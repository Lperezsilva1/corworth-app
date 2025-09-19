<?php

namespace App\Livewire\Admin\Users;

use Livewire\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsersFormulario extends Component
{
    public ?int $userId = null;
    public bool $showBreadcrumbs = true;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public array $availableRoles = [];
    public array $rolesSelected = [];

    /** Nombre de la tabla PowerGrid para refrescar */
    public string $pgRefreshEvent = 'admin-users-table';

    public function mount(?int $userId = null, bool $showBreadcrumbs = true): void
    {
        $this->showBreadcrumbs = $showBreadcrumbs;

        $this->availableRoles = Role::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->pluck('name')
            ->toArray();

        if ($userId) {
            $this->userId = $userId;
            $u = User::findOrFail($this->userId);
            $this->name  = (string) $u->name;
            $this->email = (string) $u->email;
            $this->rolesSelected = $u->roles()->pluck('name')->toArray();
        }
    }

    protected function rules(): array
    {
        return [
            'name'  => ['required','string','max:255'],
            'email' => ['required','email','max:255', Rule::unique('users','email')->ignore($this->userId)],
            'password' => [$this->userId ? 'nullable' : 'required','confirmed','min:8'],
            'password_confirmation' => [$this->userId ? 'nullable' : 'required_with:password'],
            'rolesSelected' => ['array'],
            'rolesSelected.*' => [Rule::in($this->availableRoles)],
        ];
    }

    public function save(): void
    {
        $this->validate();

        $attrs = ['name' => $this->name, 'email' => $this->email];
        if (!empty($this->password)) {
            $attrs['password'] = Hash::make($this->password);
        }

        $isEdit   = (bool) $this->userId;
        $selected = $this->rolesSelected ?? []; // ← guarda una copia ANTES de cualquier reset

        if ($isEdit) {
            // EDITAR
            $user = User::findOrFail($this->userId);
            $user->update($attrs);
            $msg = 'User updated successfully.';
        } else {
            // CREAR
            $user = User::create($attrs);
            $msg = 'User created successfully.';
        }

        // 🔐 Asignar roles por nombre (AHORA, antes de limpiar)
        $user->syncRoles($selected);

        // 🔔 Notificación (string, como en tus otros módulos)
        $this->dispatch('notify', $msg);

        // 🔁 Refrescar PowerGrid con el evento nativo (una sola vez)
        // tableName = 'admin-users-table'  →  pg:eventRefresh-admin-users-table
        $this->dispatch('pg:eventRefresh-' . $this->pgRefreshEvent);

        if ($isEdit) {
            // ✅ Cerrar modal del padre
            $this->dispatch('close-user-modal')->to(\App\Livewire\Admin\Users\Home::class);
        } else {
            // 🧹 Limpia el formulario SOLO en crear (después de syncRoles)
            $this->reset(['name','email','password','password_confirmation','rolesSelected']);
            $this->resetValidation();
        }

        // (opcional) desbloquear cualquier UI que hayas bloqueado
        $this->dispatch('modal-saving', saving: false);
    }


    public function render()
    {
        return view('livewire.admin.users.users-formulario', [
            'userId' => $this->userId,
        ]);
    }
}
