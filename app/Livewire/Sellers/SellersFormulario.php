<?php

namespace App\Livewire\Sellers;

use Livewire\Component;
use App\Models\Seller;

class SellersFormulario extends Component
{
    public ?int $sellerId = null;
    public bool $showBreadcrumbs = true;

    public string $name_seller = '';
    public ?string $description_seller = null;
    public ?string $email = null;
    public int $status = 1;

    public function mount(?int $sellerId = null, bool $showBreadcrumbs = true): void
    {
        $this->sellerId = $sellerId;
        $this->showBreadcrumbs = $showBreadcrumbs;

        if ($this->sellerId) {
            $s = Seller::withTrashed()->findOrFail($this->sellerId);
            $this->name_seller        = (string) $s->name_seller;
            $this->description_seller = $s->description_seller;
            $this->email              = $s->email;
            $this->status             = (int) $s->status;
        }
    }

    protected function rules(): array
    {
        $uniqueName = $this->sellerId
            ? 'unique:sellers,name_seller,' . $this->sellerId
            : 'unique:sellers,name_seller';

        $uniqueEmail = $this->sellerId
            ? 'nullable|email|unique:sellers,email,' . $this->sellerId
            : 'nullable|email|unique:sellers,email';

        return [
            'name_seller'        => 'required|string|max:255|' . $uniqueName,
            'description_seller' => 'nullable|string',
            'email'              => $uniqueEmail,
            'status'             => 'required|in:0,1',
        ];
    }

    public function save(): void
    {
        $this->validate();

        if ($this->sellerId) {
            Seller::withTrashed()->findOrFail($this->sellerId)->update([
                'name_seller'        => $this->name_seller,
                'description_seller' => $this->description_seller,
                'email'              => $this->email,
                'status'             => $this->status,
            ]);
            session()->flash('success', 'Seller updated successfully.');
        } else {
            Seller::create([
                'name_seller'        => $this->name_seller,
                'description_seller' => $this->description_seller,
                'email'              => $this->email,
                'status'             => $this->status,
            ]);
            session()->flash('success', 'Seller created successfully.');
        }

        $this->dispatch('seller-saved');                 // cerrar modal en contenedor
        $this->dispatch('pg:eventRefresh-sellers-table'); // refrescar PowerGrid
    }

    public function render()
    {
        return view('livewire.sellers.sellers-formulario');
    }
}
