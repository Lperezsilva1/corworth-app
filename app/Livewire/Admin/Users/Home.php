<?php

namespace App\Livewire\Admin\Users;

use Livewire\Component;
use Livewire\Attributes\On;

class Home extends Component
{
    public bool $showModal = false;
    public ?int $editingId = null;

    #[On('open-user-modal')]
    public function openUserModal(?int $userId = null): void
    {
        $this->editingId = $userId; // null => (no se usa aquí), id => editar
        $this->showModal = true;
    }

    #[On('close-user-modal')]
    public function closeUserModal(): void
    {
        $this->showModal = false;
        $this->editingId = null;
    }

    public function render()
    {
        return view('livewire.admin.users.home');
    }
}
