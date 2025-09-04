<?php

namespace App\Livewire\Sellers;

use Livewire\Component;
use App\Models\Seller;

class Sellers extends Component
{
    public bool $modalOpen = false;
    public ?int $sellerId = null;
    public ?int $confirmingDeleteId = null;

    #[\Livewire\Attributes\On('open-seller-modal')]
    public function openSellerModal(int $sellerId): void
    {
        $this->sellerId = $sellerId;
        $this->modalOpen = true;
    }

    #[\Livewire\Attributes\On('seller-saved')]
    public function onSellerSaved(): void
    {
        $this->modalOpen = false;
        $this->sellerId = null;
        $this->dispatch('pg:eventRefresh-sellers-table'); // <- coincide con $tableName
        session()->flash('success', 'Seller saved successfully.');
    }

    #[\Livewire\Attributes\On('delete-seller')]
    public function askDelete(int $sellerId): void
    {
        $this->confirmingDeleteId = $sellerId;
    }

    public function confirmDelete(): void
    {
        Seller::findOrFail($this->confirmingDeleteId)->delete(); // Soft Delete
        $this->confirmingDeleteId = null;
        $this->dispatch('pg:eventRefresh-sellers-table');
        session()->flash('success', 'Seller deleted successfully.');
    }

    public function closeModal(): void
    {
        $this->modalOpen = false;
        $this->sellerId = null;
    }

    public function render()
    {
        return view('livewire.sellers.sellers');
    }
}
