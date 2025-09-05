<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class ProjectsTable extends PowerGridComponent
{
    public string $tableName = 'projects-table-main';

    use WithExport;

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::exportable('export')
                ->striped()
                ->columnWidth([2 => 30])
                ->queues(2)
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            PowerGrid::header()->showSearchInput(),
            PowerGrid::footer()->showPerPage()->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Project::query()->with([
            'building', 'seller', 'drafterPhase1', 'drafterFullset',
        ]);
    }

    public function relationSearch(): array
    {
        return [
            'building'       => ['name_building'],
            'seller'         => ['name_seller'],
            'drafterPhase1'  => ['name_drafter'],
            'drafterFullset' => ['name_drafter'],
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id') // necesario para el reemplazo en ->route(['project' => 'id'])
            ->add('project_name_link', fn($p) =>
                '<a href="'.route('projects.show', ['project' => $p->id]).'" class="link hover:underline">'
                . e($p->project_name) .
                '</a>'
)
            ->add('building_name', fn($p) => $p->building?->name_building ?? '—')
            ->add('seller_name', fn($p) => $p->seller?->name_seller ?? '—')
            ->add('phase1_drafter_name', fn($p) => $p->drafterPhase1?->name_drafter ?? '—')
            ->add('fullset_drafter_name', fn($p) => $p->drafterFullset?->name_drafter ?? '—')
            ->add('phase1_status_badge', fn($p) =>
                $p->phase1_status === "Phase 1's Complete"
                    ? '<span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">'.$p->phase1_status.'</span>'
                    : ($p->phase1_status
                        ? '<span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">'.$p->phase1_status.'</span>'
                        : '—')
            )
            ->add('fullset_status_badge', fn($p) =>
                $p->fullset_status === "Full Set Complete"
                    ? '<span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">'.$p->fullset_status.'</span>'
                    : ($p->fullset_status
                        ? '<span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">'.$p->fullset_status.'</span>'
                        : '—')
            )
            ->add('general_status_badge', fn($p) => match ($p->general_status) {
                'Approved'     => '<span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">Approved</span>',
                'Cancelled'    => '<span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">Cancelled</span>',
                'Not Approved' => '<span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Not Approved</span>',
                default        => '—',
            });
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'id')->index()->bodyAttribute('text-center'),
            Column::make('Project', 'project_name_link')->sortable(false)->searchable(false)->headerAttribute('w-64')->bodyAttribute('w-64'),
            Column::make('Building', 'building_name')->sortable()->searchable(),
            Column::make('Seller', 'seller_name')->sortable()->searchable()->headerAttribute('w-44')->bodyAttribute('w-44'),
            Column::make('P1 Drafter', 'phase1_drafter_name')->sortable()->searchable(),
            Column::make('Phase 1 Status', 'phase1_status_badge')->sortable()->searchable()->bodyAttribute('text-center'),
            Column::make('Full Set Drafter', 'fullset_drafter_name')->sortable()->searchable(),
            Column::make('Full Set Status', 'fullset_status_badge')->sortable()->searchable()->bodyAttribute('text-center'),
            Column::make('General', 'general_status_badge')->sortable()->searchable()->bodyAttribute('text-center'),
            
            Column::action('Actions'),
        ];
    }

    public function filters(): array
    {
        return [];
    }

    public function actions(Project $row): array
    {
        return [
         
            Button::add('delete')
                ->slot('🗑 Delete')
                ->id()
                ->class('btn btn-sm btn-error')
                ->dispatch('delete-project', ['projectId' => $row->id]),
        ];
    }
}