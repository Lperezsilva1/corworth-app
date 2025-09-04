<?php

namespace App\Livewire\Sellers;

use App\Models\Seller;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;

final class SellersTable extends PowerGridComponent
{
    // Úsalo para refrescar: pg:eventRefresh-sellers-table
    public string $tableName = 'sellers-table';

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
        return Seller::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name_seller')
            ->add('email')
            ->add('description_seller')
            // Etiqueta legible para el estado (sin HTML)
            ->add('status_text', fn ($s) => $s->status 
                     ? '<span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">Enabled</span>'
                     : '<span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">Disabled</span>')
            // Fecha formateada para mostrar
            ->add('created_at_formatted', fn ($s) => optional($s->created_at)->format('Y-m-d H:i'));
    }

    public function columns(): array
    {
        return [
            // Índice de fila (numera 1,2,3… con paginación)
            Column::make('#', 'id')
                ->index()                      // ← índice de fila
                ->bodyAttribute('text-center'),

            Column::make('Name', 'name_seller')
                ->sortable()
                ->searchable(),

            Column::make('Email', 'email')
                ->sortable()
                ->searchable(),

             // Columna Status (booleano)
            Column::make('Status', 'status_text')
                    ->sortable()
                    ->searchable()
                    ->bodyAttribute('text-center'),

            Column::make('Created At', 'created_at_formatted')
                ->sortable(),

            Column::action('Action'),
        ];
    }

    public function filters(): array
    {
        return [
            // Ejemplos si luego quieres activar:
            // Filter::boolean('status', 'status'),
            // Filter::inputText('name_seller')->operators(['contains']),
        ];
    }

    public function actions(Seller $row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit')
                ->id()
                ->class('btn btn-sm btn-soft')
                ->dispatch('open-seller-modal', ['sellerId' => $row->id]),

            Button::add('delete')
                ->slot('🗑 Delete')
                ->id()
                ->class('btn btn-sm btn-error')
                ->dispatch('delete-seller', ['sellerId' => $row->id]),
        ];
    }
}
