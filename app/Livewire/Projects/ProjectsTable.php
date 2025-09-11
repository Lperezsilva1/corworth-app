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
            'building',
            'seller',
            'drafterPhase1',
            'drafterFullset',
            'status',
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
            ->add('id')

            // 🔹 Proyecto + Building en una sola columna
            ->add('project_with_building', fn($p) =>
                '<div class="flex flex-col">
                    <a href="'.route('projects.show', ['project' => $p->id]).'" 
                       class="link hover:underline font-medium">'
                        . e($p->project_name) .
                    '</a>
                    <span class="text-sm text-gray-500">'
                        . e($p->building?->name_building ?? '—') .
                    '</span>
                </div>'
            )

            ->add('seller_name', fn($p) => $p->seller?->name_seller ?? '—')

            // 🔹 Drafter + status Phase 1
            ->add('phase1_column', fn($p) =>
                '<div class="flex flex-col items-start">
                    <span class="font-medium text-gray-700">'
                        . e($p->drafterPhase1?->name_drafter ?? '—') .
                    '</span>
                    <span class="text-xs">'
                        . match ($p->phase1_status) {
                            "Phase 1's Complete" => '<span class="text-green-600">✅ Complete</span>',
                            "In Progress"        => '<span class="text-amber-600">⏳ In Progress</span>',
                            default              => '<span class="text-gray-400">—</span>',
                        } .
                    '</span>
                </div>'
            )

            // 🔹 Drafter + status Full Set
            ->add('fullset_column', fn($p) =>
                '<div class="flex flex-col items-start">
                    <span class="font-medium text-gray-700">'
                        . e($p->drafterFullset?->name_drafter ?? '—') .
                    '</span>
                    <span class="text-xs">'
                        . match ($p->fullset_status) {
                            "Full Set Complete" => '<span class="text-green-600">✅ Complete</span>',
                            "In Progress"       => '<span class="text-amber-600">⏳ In Progress</span>',
                            default             => '<span class="text-gray-400">—</span>',
                        } .
                    '</span>
                </div>'
            )

            // 🔹 General status con chip
            ->add('general_status_chip', function ($p) {
                $palette = [
                    'pending'           => 'bg-zinc-50 text-zinc-700 ring-zinc-200',
                    'working'           => 'bg-sky-50 text-sky-700 ring-sky-200',
                    'awaiting_approval' => 'bg-amber-50 text-amber-700 ring-amber-200',
                    'approved'          => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                    'cancelled'         => 'bg-rose-50 text-rose-700 ring-rose-200',
                ];

                $base  = 'inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset shadow-sm';
                $key   = $p->status?->key;
                $tone  = $palette[$key] ?? 'bg-gray-50 text-gray-700 ring-gray-200';

                return $p->status
                    ? '<span class="'.$base.' '.$tone.'"><span class="h-1.5 w-1.5 rounded-full bg-current"></span>'.e($p->general_status_label).'</span>'
                    : '—';
            });
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'id')->index()->bodyAttribute('text-center'),

            Column::make('Project', 'project_with_building')
                ->sortable(false)->searchable(false)
                ->headerAttribute('w-64')->bodyAttribute('w-64'),

            Column::make('Seller', 'seller_name')
                ->sortable()->searchable()
                ->headerAttribute('w-44')->bodyAttribute('w-44'),

            Column::make('Phase 1', 'phase1_column')
                ->sortable(false)->searchable(false)
                ->bodyAttribute('text-left'),

            Column::make('Full Set', 'fullset_column')
                ->sortable(false)->searchable(false)
                ->bodyAttribute('text-left'),

            Column::make('General', 'general_status_chip')
                ->sortable(false)->searchable(false)
                ->bodyAttribute('text-center'),

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
                ->dispatch('ask-delete-project', ['projectId' => $row->id]),
        ];
    }
}
