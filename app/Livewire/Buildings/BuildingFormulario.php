<?php

namespace App\Livewire\Buildings;

use Livewire\Component;
use App\Models\Building;

class BuildingFormulario extends Component
{
    // Contexto
    public ?int $buildingId = null;
    public bool $showBreadcrumbs = true; // true en página, false en modal

    // Campos
    public string $name_building = '';
    public ?string $description_building = '';
    public int $status = 1;

    public function mount(?int $buildingId = null, bool $showBreadcrumbs = true): void
    {
        $this->buildingId = $buildingId;
        $this->showBreadcrumbs = $showBreadcrumbs;

        if ($this->buildingId) {
            $b = Building::findOrFail($this->buildingId);
            $this->name_building        = (string) $b->name_building;
            $this->description_building = $b->description_building;
            $this->status               = (int) $b->status;
        }
    }

    protected function rules(): array
    {
        // Si necesitas unique:
        // $unique = $this->buildingId
        //     ? 'unique:buildings,name_building,' . $this->buildingId
        //     : 'unique:buildings,name_building';

        return [
            'name_building'        => 'required|string|max:255', // . '|' . $unique
            'description_building' => 'nullable|string',
            'status'               => 'required|in:0,1',
        ];
    }

    public function save(): void
    {
        $this->validate();

        if ($this->buildingId) {
            Building::findOrFail($this->buildingId)->update([
                'name_building'        => $this->name_building,
                'description_building' => $this->description_building,
                'status'               => $this->status,
            ]);
         
        } else {
            Building::create([
                'name_building'        => $this->name_building,
                'description_building' => $this->description_building,
                'status'               => $this->status,
            ]);
            
            $this->dispatch('notify', 'Building created successfully');
            $this->reset(['name_building', 'description_building', 'status']);
        }

        // Notificar: cerrar modal y refrescar PowerGrid (ajusta el tableName a tu componente real)
        $this->dispatch('building-saved'); // el contenedor cierra el modal
        $this->dispatch('pg:eventRefresh-buildings-table-k9q2f0-table'); // PowerGrid refresh
    }

    public function render()
    {
        // Tu vista actual: resources/views/livewire/buildings/building-formulario.blade.php
        return view('livewire.buildings.building-formulario');
    }
}
