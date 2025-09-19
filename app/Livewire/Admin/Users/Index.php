<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class Index extends PowerGridComponent
{
    public string $tableName = 'admin-users-table';

    #[On('admin-users-table')] // el evento que disparas desde el formulario
    public function refreshTable(): void
    {
        $this->dispatch('$refresh'); // re-render
        // $this->resetPage(); // opcional si quieres volver a la primera página
    }

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
        // No pongas orderBy fijo aquí para no bloquear el sort por cabecera
        return User::query()->with('roles');
    }

    public function relationSearch(): array
    {
        // Si quieres que la búsqueda global encuentre por roles.name:
        return [
            'roles' => ['name'],
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('email')

            // Texto plano para buscar/exportar
            ->add('roles_text', fn (User $u) => $u->roles->pluck('name')->join(', '))

            // Badges HTML para mostrar en la tabla
            ->add('roles_badges', fn (User $u) =>
                $u->roles->map(fn ($r) =>
                    '<span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-zinc-100 text-zinc-700">'.
                    e($r->name).'</span>'
                )->implode(' ')
            )

            ->add('created_at')
            ->add('created_at_formatted', fn (User $u) => optional($u->created_at)->format('Y-m-d H:i'));
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'id')->index()->sortable(false)->bodyAttribute('text-center'),

            Column::make('Name', 'name')->sortable()->searchable(),
            Column::make('Email', 'email')->sortable()->searchable(),

            // Muestra badges (HTML) pero no lo exportes ni lo hagas searchable
            Column::make('Roles', 'roles_badges')
                ->sortable(false)
                ->searchable(false)
                ->visibleInExport(false),

            // Columna oculta para búsqueda/export por texto plano
            Column::make('RolesText', 'roles_text')
                ->searchable()
                ->hidden(),

            // Mostrar formateado, ordenar por created_at real
            Column::make('Created', 'created_at_formatted', 'created_at')->sortable(),

            Column::action('Actions'),
        ];
    }

    public function actions(User $row): array
    {
        return [
        Button::add('edit')
            ->slot('Edit')
            ->id()
            ->class('btn btn-sm btn-soft')
           ->dispatch('open-user-modal', ['userId' => $row->id]), // abre modal
    ];
    }

    public function filters(): array
    {
        return [];
    }
    
}
