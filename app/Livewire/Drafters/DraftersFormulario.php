<?php

namespace App\Livewire\Drafters;

use Livewire\Component;
use App\Models\Drafter;

class DraftersFormulario extends Component
{
    // Contexto
    public ?int $drafterId = null;
    public bool $showBreadcrumbs = true; // true en página, false en modal

    // Campos (el partial usa estos nombres)
    public string $name_drafter = '';
    public ?string $description_drafter = '';
    public int $status = 1;

    public function mount(?int $drafterId = null, bool $showBreadcrumbs = true): void
    {
        $this->drafterId = $drafterId;
        $this->showBreadcrumbs = $showBreadcrumbs;

        if ($this->drafterId) {
            $d = Drafter::findOrFail($this->drafterId);
            $this->name_drafter        = (string) $d->name_drafter;
            $this->description_drafter = $d->description_drafter;
            $this->status              = (int) $d->status;
        }
    }

    protected function rules(): array
    {
        // Si necesitas unique:
        // $unique = $this->drafterId ? 'unique:drafters,name_drafter,' . $this->drafterId : 'unique:drafters,name_drafter';
        return [
            'name_drafter'        => 'required|string|max:255', // . '|' . $unique
            'description_drafter' => 'nullable|string',
            'status'              => 'required|in:0,1',
        ];
    }

    public function save(): void
    {
        $this->validate();

        if ($this->drafterId) {
            Drafter::findOrFail($this->drafterId)->update([
                'name_drafter'        => $this->name_drafter,
                'description_drafter' => $this->description_drafter,
                'status'              => $this->status,
            ]);
            session()->flash('success', 'Updated successfully.');
            
           
        } else {
            Drafter::create([
                'name_drafter'        => $this->name_drafter,
                'description_drafter' => $this->description_drafter,
                'status'              => $this->status,
            ]);
            session()->flash('success', 'Created successfully.');
             $this->dispatch('notify', 'Drafter created successfully ');
             $this->reset(['name_drafter', 'description_drafter', 'status']);
        }

        // Notificar: cerrar modal y refrescar PowerGrid (usa tu tableName real)
        $this->dispatch('drafter-saved'); // el contenedor cierra el modal
        $this->dispatch('pg:eventRefresh-drafters-table-oxhn9b-table'); // PowerGrid refresh
        
    }

    public function render()
    {
        // Vista plural: resources/views/livewire/drafters/drafters-formulario.blade.php
        return view('livewire.drafters.drafters-formulario');
    }
}
