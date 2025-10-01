<?php

namespace App\Livewire\Settings;

use Livewire\Component;

class RbacContainer extends Component
{
    public string $tab = 'roles'; // roles | permissions | assign

    public function mount(): void
    {
        abort_unless(auth()->check() && auth()->user()->can('roles.manage'), 403);
        $tab = request()->query('tab', 'roles');
        $this->tab = in_array($tab, ['roles','permissions','assign'], true) ? $tab : 'roles';
    }

    public function updatedTab(): void
    {
        // Sync con la URL para navegación fluida
        $this->dispatch('navigate', url: route('settings.rbac', ['tab' => $this->tab]));
    }

    public function render()
    {
        return view('livewire.settings.rbac-container');
    }
}
