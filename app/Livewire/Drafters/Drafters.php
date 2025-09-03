<?php

namespace App\Livewire\Drafters;

use Livewire\Component;
use App\Models\Drafter;

class Drafters extends Component
{
    public bool $modalOpen = false;
    public ?int $drafterId = null;
    public ?int $confirmingDeleteId = null;

    #[\Livewire\Attributes\On('open-drafter-modal')]
    public function openDrafterModal(int $drafterId): void
    {
        $this->drafterId = $drafterId;
        $this->modalOpen = true;
    }

    #[\Livewire\Attributes\On('delete-drafter')]
    public function deleteDrafter(int $drafterId): void
    {
        Drafter::findOrFail($drafterId)->delete(); // 👈 ahora es Soft Delete
        $this->dispatch('pg:eventRefresh-drafters-table-oxhn9b-table'); // refrescar PowerGrid
        session()->flash('success', 'Drafter deleted successfully.');
    }

    public function closeModal(): void
    {
        $this->modalOpen = false;
        $this->drafterId = null;
    }

    public function render()
    {
        return view('livewire.drafters.drafters');
    }

    #[\Livewire\Attributes\On('delete-drafter')]
    public function askDelete(int $drafterId): void
    {
        $this->confirmingDeleteId = $drafterId;
    }

    public function confirmDelete(): void
    {
        Drafter::findOrFail($this->confirmingDeleteId)->delete();
        $this->confirmingDeleteId = null;
        $this->dispatch('pg:eventRefresh-drafters-table-oxhn9b-table');
        session()->flash('success', 'Drafter deleted successfully.');
    }
}