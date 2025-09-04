<?php

namespace App\Livewire\Buildings;

use Livewire\Component;
use App\Models\Building;

class Buildings extends Component
{
    public bool $modalOpen = false;
    public ?int $buildingId = null;
    public ?int $confirmingDeleteId = null;

    #[\Livewire\Attributes\On('open-building-modal')]
    public function openBuildingModal(int $buildingId): void
    {
        $this->buildingId = $buildingId;
        $this->modalOpen = true;
    }

    #[\Livewire\Attributes\On('delete-building')]
    public function deleteBuilding(int $buildingId): void
    {
        Building::findOrFail($buildingId)->delete();
        $this->dispatch('pg:eventRefresh-buildings-table-k9q2f0-table'); // refrescar PowerGrid
        session()->flash('success', 'Building deleted successfully.');
    }

    public function closeModal(): void
    {
        $this->modalOpen = false;
        $this->buildingId = null;
    }

    public function render()
    {
        return view('livewire.buildings.buildings');
    }

    #[\Livewire\Attributes\On('delete-building')]
    public function askDelete(int $buildingId): void
    {
        $this->confirmingDeleteId = $buildingId;
    }

    public function confirmDelete(): void
    {
        Building::findOrFail($this->confirmingDeleteId)->delete();
        $this->confirmingDeleteId = null;
        $this->dispatch('pg:eventRefresh-buildings-table-k9q2f0-table');
        session()->flash('success', 'Building deleted successfully.');
    }
}
