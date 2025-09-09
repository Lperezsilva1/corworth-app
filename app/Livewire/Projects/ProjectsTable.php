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
            'status', // 👈 importante: precargar catálogo de estados
        ]);
    }

    public function relationSearch(): array
    {
        return [
            'building'       => ['name_building'],
            'seller'         => ['name_seller'],
            'drafterPhase1'  => ['name_drafter'],
            'drafterFullset' => ['name_drafter'],
            // Si quisieras buscar por estado:
            // 'status' => ['label', 'key'],
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id') // necesario para route/acciones

            // Link del nombre (HTML desde fields)
            ->add('project_name_link', fn ($p) =>
                '<a href="'.route('projects.show', ['project' => $p->id]).'" class="link hover:underline">'
                . e($p->project_name) .
                '</a>'
            )

            ->add('building_name', fn ($p) => $p->building?->name_building ?? '—')
            ->add('seller_name', fn ($p) => $p->seller?->name_seller ?? '—')
            ->add('phase1_drafter_name', fn ($p) => $p->drafterPhase1?->name_drafter ?? '—')
            ->add('fullset_drafter_name', fn ($p) => $p->drafterFullset?->name_drafter ?? '—')

            // Badges Phase 1 / Full Set (tu lógica original)
            ->add('phase1_status_badge', fn ($p) =>
                $p->phase1_status === "Phase 1's Complete"
                    ? '<span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">'.e($p->phase1_status).'</span>'
                    : ($p->phase1_status
                        ? '<span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">'.e($p->phase1_status).'</span>'
                        : '—')
            )
            ->add('fullset_status_badge', fn ($p) =>
                $p->fullset_status === "Full Set Complete"
                    ? '<span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">'.e($p->fullset_status).'</span>'
                    : ($p->fullset_status
                        ? '<span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">'.e($p->fullset_status).'</span>'
                        : '—')
            )

            // ✅ General: chip HTML usando la relación `status`
            ->add('general_status_chip', function ($p) {
    // Colores por estado (usando Tailwind)
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

    // Puntito usando el color de texto actual (bg-current)
    return $p->status
        ? '<span class="'.$base.' '.$tone.'"><span class="h-1.5 w-1.5 rounded-full bg-current"></span>'.e($p->general_status_label).'</span>'
        : '—';
});
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'id')->index()->bodyAttribute('text-center'),

            // El HTML del link ya viene desde fields()
            Column::make('Project', 'project_name_link')
                ->sortable(false)->searchable(false)
                ->headerAttribute('w-64')->bodyAttribute('w-64'),

            Column::make('Building', 'building_name')->sortable()->searchable(),
            Column::make('Seller', 'seller_name')->sortable()->searchable()->headerAttribute('w-44')->bodyAttribute('w-44'),
            Column::make('P1 Drafter', 'phase1_drafter_name')->sortable()->searchable(),

            Column::make('Phase 1 Status', 'phase1_status_badge')
                ->sortable(false)->searchable(false)->bodyAttribute('text-center'),

            Column::make('Full Set Drafter', 'fullset_drafter_name')->sortable()->searchable(),

            Column::make('Full Set Status', 'fullset_status_badge')
                ->sortable(false)->searchable(false)->bodyAttribute('text-center'),

            // ✅ Usamos el campo HTML que creamos en fields()
            Column::make('General', 'general_status_chip')
                ->sortable(false)->searchable(false)->bodyAttribute('text-center'),

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
                 ->dispatch('ask-delete-project', ['projectId' => $row->id]), // 👈 abre modal
        ];
    }
}
