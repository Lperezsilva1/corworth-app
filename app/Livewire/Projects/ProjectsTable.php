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
        // Incluye relaciones de status por fase y general
        return Project::query()->with([
            'building',
            'seller',
            'drafterPhase1',
            'drafterFullset',
            'status',           // general
            'phase1Status',     // fase 1
            'fullsetStatus',    // full set
        ]);
    }

    public function relationSearch(): array
    {
        return [
            'building'       => ['name_building'],
            'seller'         => ['name_seller'],
            'drafterPhase1'  => ['name_drafter'],
            'drafterFullset' => ['name_drafter'],
            // opcional: búsqueda por label de estados
            'phase1Status'   => ['label'],
            'fullsetStatus'  => ['label'],
            'status'         => ['label'],
        ];
    }

    public function fields(): PowerGridFields
    {
        // Paleta para chip de GENERAL
        $palette = [
            'pending'           => 'bg-zinc-50 text-zinc-700 ring-zinc-200',
            'working'           => 'bg-sky-50 text-sky-700 ring-sky-200',
            'complete'          => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            'awaiting_approval' => 'bg-amber-50 text-amber-700 ring-amber-200',
            'approved'          => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            'cancelled'         => 'bg-rose-50 text-rose-700 ring-rose-200',
        ];
        $badge = 'inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset shadow-sm';

        // Tono simple (solo texto) para columnas de fase
        $toneText = [
            'pending'  => 'text-zinc-600',
            'working'  => 'text-amber-600',
            'complete' => 'text-emerald-600',
        ];
        $icon = [
            'pending'  => '•',
            'working'  => '⏳',
            'complete' => '✅',
        ];

        return PowerGrid::fields()
            ->add('id')

            // Proyecto + Building
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

            // Phase 1: texto compacto (sin badge)
            ->add('phase1_column', function ($p) use ($toneText, $icon) {
                $label  = $p->phase1Status?->label;   // "Pending" / "Working" / "Complete"
                $key    = $p->phase1Status?->key;     // 'pending' | 'working' | 'complete'
                $tone   = $toneText[$key] ?? 'text-gray-500';
                $glyph  = $icon[$key] ?? '•';
                $drafter = e($p->drafterPhase1?->name_drafter ?? '—');

                $statusLine = $label
                    ? '<span class="text-xs '.$tone.'">'.$glyph.' '.e($label).'</span>'
                    : '<span class="text-xs text-gray-400">—</span>';

                return '<div class="flex flex-col items-start">
                          <span class="font-medium text-gray-700">'.$drafter.'</span>
                          '.$statusLine.'
                        </div>';
            })

            // Full Set: texto compacto (sin badge)
            ->add('fullset_column', function ($p) use ($toneText, $icon) {
                $label  = $p->fullsetStatus?->label;
                $key    = $p->fullsetStatus?->key;
                $tone   = $toneText[$key] ?? 'text-gray-500';
                $glyph  = $icon[$key] ?? '•';
                $drafter = e($p->drafterFullset?->name_drafter ?? '—');

                $statusLine = $label
                    ? '<span class="text-xs '.$tone.'">'.$glyph.' '.e($label).'</span>'
                    : '<span class="text-xs text-gray-400">—</span>';

                return '<div class="flex flex-col items-start">
                          <span class="font-medium text-gray-700">'.$drafter.'</span>
                          '.$statusLine.'
                        </div>';
            })

            // General: mantiene chip/badge
            ->add('general_status_chip', function ($p) use ($palette, $badge) {
                $key  = $p->status?->key;
                $tone = $palette[$key] ?? 'bg-gray-50 text-gray-700 ring-gray-200';

                return $p->status
                    ? '<span class="'.$badge.' '.$tone.'"><span class="h-1.5 w-1.5 rounded-full bg-current"></span>'.e($p->general_status_label).'</span>'
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
