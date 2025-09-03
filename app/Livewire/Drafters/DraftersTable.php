<?php

namespace App\Livewire\Drafters;

use App\Models\Drafter;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class DraftersTable extends PowerGridComponent
{
    public string $tableName = 'drafters-table-oxhn9b-table';

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::header()
                ->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Drafter::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name_drafter')
            ->add('description_drafter')
             ->add('status_text', fn ($drafter) => $drafter->status 
                     ? '<span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">Enabled</span>'
                     : '<span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">Disabled</span>')
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'id')
                  ->index() // 👈 esto genera el contador
                  ->bodyAttribute('text-center'),

            Column::make('Name', 'name_drafter')
                ->sortable()
                ->searchable(),

            // Columna Description
            Column::make('Description', 'description_drafter')
                ->sortable()
                ->searchable(),

            // Columna Status (booleano)
            Column::make('Status', 'status_text')
                    ->sortable()
                    ->searchable()
                    ->bodyAttribute('text-center'),

            // Columna Created At
            Column::make('Created At', 'created_at')
                ->sortable(),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert('.$rowId.')');
    }

    public function actions(Drafter $row): array
{
    return [
        Button::add('edit')
            ->slot('Edit')       // Texto del botón
            ->id()                  // asigna el id del row
            ->class('btn btn-sm btn-soft')
            ->dispatch('open-drafter-modal', ['drafterId' => $row->id]),

            Button::add('delete')
            ->slot('🗑 Delete')
            ->id()
            ->class('btn btn-sm btn-error')
            ->dispatch('delete-drafter', ['drafterId' => $row->id]),
    ];
}

    /*
    public function actionRules($row): array
    {
       return [
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => $row->id === 1)
                ->hide(),
        ];
    }
    */
}