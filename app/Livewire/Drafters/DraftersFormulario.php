<?php

namespace App\Livewire\Drafters;

use Livewire\Component;
use App\Models\Drafter; //

class DraftersFormulario extends Component
{
    public $name_drafter = '';
    public $description_drafter = '';
    public $status = 1; // por defecto Habilitado

    protected $rules = [
        'name_drafter'       => 'required|string|max:255',
        'description_drafter'=> 'nullable|string',
        'status'             => 'required|boolean',
    ];

    public function save()
    {
        $this->validate();

        Drafter::create([
            'name_drafter'        => $this->name_drafter,
            'description_drafter' => $this->description_drafter,
            'status'              => (bool) $this->status,
        ]);

        // limpia el formulario
        $this->reset(['name_drafter', 'description_drafter', 'status']);
        $this->status = 1;

        // notificación simple
        session()->flash('success', 'Drafter created successfully ');

        // refresca la tabla de PowerGrid
        // Usa el mismo $tableName que definiste en DraftersTable:
        // public string $tableName = 'drafters-table-oxhn9b-table';
      //  $this->dispatch('pg:eventRefresh-drafters-table-oxhn9b-table');
        $this->dispatch('notify', 'Drafter created successfully ');
        // si cambiaste el tableName, actualiza el string arriba.
    }

    public function render()
    {
        return view('livewire.drafters.drafters-formulario');
    }
}
