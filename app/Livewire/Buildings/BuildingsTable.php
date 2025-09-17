<?php

namespace App\Livewire\Buildings;

use App\Models\Building;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class BuildingsTable extends PowerGridComponent
{
    public string $tableName = 'buildings-table-k9q2f0-table';

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::header()->showSearchInput(),
            PowerGrid::footer()->showPerPage()->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Building::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name_building')
            ->add('description_building')
            // Campo visual (HTML)
            ->add('status_text', fn ($building) => $building->status
                ? '<span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">Enabled</span>'
                : '<span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">Disabled</span>')
            // Campo real de BD
            ->add('status')
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'id')
                ->index()
                ->bodyAttribute('text-center'),

            Column::make('Name', 'name_building')
                ->sortable()
                ->searchable(),

            Column::make('Description', 'description_building')
                ->sortable()
                ->searchable(),

            // 👇 mostramos HTML pero ordenamos por el campo real 'status'
            Column::make('Status', 'status_text', 'status')
                ->sortable()
                ->bodyAttribute('text-center'),

            Column::make('Created At', 'created_at')
                ->sortable(),

            Column::action('Action'),
        ];
    }

    public function filters(): array
    {
        return []; // Nada de filtros en tu versión
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert('.$rowId.')');
    }

    public function actions(Building $row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit')
                ->id()
                ->class('btn btn-sm btn-soft')
                ->dispatch('open-building-modal', ['buildingId' => $row->id]),

            Button::add('delete')
                ->slot('🗑 Delete')
                ->id()
                ->class('btn btn-sm btn-error')
                ->dispatch('delete-building', ['buildingId' => $row->id]),
        ];
    }
}
