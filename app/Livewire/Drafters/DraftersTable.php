<?php

namespace App\Livewire\Drafters;

use App\Models\Drafter;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\Themes\Tailwind;


final class DraftersTable extends PowerGridComponent
{
    use WithExport;

    public string $tableName = 'drafters-table-oxhn9b-table';


    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            
            // Export (XLS/CSV) sin HTML:
            PowerGrid::exportable('export')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),

            PowerGrid::header()->showSearchInput(),
            PowerGrid::footer()->showPerPage()->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Drafter::query();
        // Si usas SoftDeletes: ->whereNull('deleted_at')
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

            // Badge HTML para mostrar (no exportar)
            ->add('status_text', fn ($d) => $d->status
                ? '<span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">Enabled</span>'
                : '<span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">Disabled</span>')

            // Campo REAL (orden/filtrado)
            ->add('status')

            // Solo para exportar como texto (sin HTML)
            ->add('status_export', fn ($d) => $d->status ? 'Enabled' : 'Disabled')

            // Fecha real + formateada
            ->add('created_at')
            ->add('created_at_formatted', fn ($d) => optional($d->created_at)->format('Y-m-d H:i'));
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'id')
                ->index()
                ->bodyAttribute('text-center'),

            Column::make('Name', 'name_drafter')
                ->sortable()
                ->searchable(),

            Column::make('Description', 'description_drafter')
                ->sortable()
                ->searchable(),

            // Mostrar HTML pero ordenar por 'status'
            Column::make('Status', 'status_text', 'status')
                ->sortable()
                ->bodyAttribute('text-center')
                ->visibleInExport(false), // ← evita HTML en export

            // Columna solo para exportar texto plano del status
            Column::make('Status', 'status_export')
                ->hidden()
                ->visibleInExport(true),

            // Mostrar formateado, ordenar por fecha real
            Column::make('Created At', 'created_at_formatted', 'created_at')
                ->sortable(),
            
            Column::action('Action'),
        ];
    }

    public function filters(): array
    {
        return []; // sin filtros (compat con tu versión)
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
                ->slot('Edit')
                ->id()
                ->class('btn btn-sm btn-soft')
                ->dispatch('open-drafter-modal', ['drafterId' => $row->id]),

            Button::add('delete')
                ->slot('🗑 Delete')
                ->id()
                ->class('btn btn-sm btn-error')
                ->dispatch('delete-drafter', ['drafterId' => $row->id]),
        ];
    }
}
