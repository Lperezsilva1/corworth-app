<?php
// app/Livewire/Settings/AssignRoles.php
namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AssignRoles extends Component
{
    public ?int $userId = null;
    public array $selectedRoles = [];

    public function mount(): void
    {
        $this->authorizeAccess();
    }

    public function updatedUserId(): void
    {
        $this->authorizeAccess();

        $u = User::find($this->userId);
        $this->selectedRoles = $u ? $u->getRoleNames()->toArray() : [];
    }

    public function save(): void
    {
        $this->authorizeAccess();

        $u = User::findOrFail($this->userId);
        $u->syncRoles($this->selectedRoles);
        session()->flash('ok','User roles updated');
        $this->dispatch('$refresh');
    }

    protected function authorizeAccess(): void
    {
        abort_unless(auth()->check() && auth()->user()->can('roles.manage'), 403);
    }

    public function render()
    {
        return view('livewire.settings.assign-roles', [
            'users' => User::select('id','name','email')->orderBy('name')->get(),
            'roles' => Role::orderBy('name')->pluck('name')->all(),
        ]);
    }
}
