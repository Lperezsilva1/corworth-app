<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

/**
 * @property string $sortField
 * @property string $sortDirection
 */
final class ProjectsTable extends PowerGridComponent
{
    use WithExport;

    public string $tableName = 'projects-table-main';

    public function setUp(): array
    {
          $this->sortField     = 'projects.id';   // o 'projects.created_at'
            $this->sortDirection = 'desc';
        // Orden por defecto solo si aún no hay uno activo/persistido
        

        $this->showCheckBox();

        return [
            PowerGrid::exportable('export')->striped()->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            PowerGrid::header()->showSearchInput(),
            PowerGrid::footer()->showPerPage()->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        $q = Project::query()
            ->select('projects.*')

            // Alias de seller para ordenar/buscar
            ->selectSub(
                'SELECT name_seller FROM sellers WHERE sellers.id = projects.seller_id',
                'seller_name'
            )

            // JOINs necesarios
            ->leftJoin('buildings', 'buildings.id', '=', 'projects.building_id')
            ->leftJoin('drafters as d1', 'd1.id', '=', 'projects.phase1_drafter_id')   // Phase 1 drafter
            ->leftJoin('drafters as d2', 'd2.id', '=', 'projects.fullset_drafter_id') // Full Set drafter
            ->leftJoin('statuses as s1', 's1.id', '=', 'projects.phase1_status_id')   // Phase 1 status
            ->leftJoin('statuses as s2', 's2.id', '=', 'projects.fullset_status_id')  // Fullset status
            ->leftJoin('statuses as sg', 'sg.id', '=', 'projects.general_status')     // General status

            // UI Project + Building
            ->addSelect(DB::raw(
                "CONCAT(projects.project_name, ' ', COALESCE(buildings.name_building, '')) AS project_with_building"
            ))

            // Aliases de texto (búsqueda/mostrar)
            ->addSelect([
                DB::raw("COALESCE(d1.name_drafter, '') AS phase1_drafter_name"),
                DB::raw("COALESCE(d2.name_drafter, '') AS fullset_drafter_name"),
                DB::raw("COALESCE(s1.label, '') AS phase1_status_label_sql"),
                DB::raw("COALESCE(s2.label, '') AS fullset_status_label_sql"),
                DB::raw("COALESCE(sg.label, '') AS general_status_label_sql"),
                DB::raw("COALESCE(d1.name_drafter, '') AS phase1_search_sql"),
                DB::raw("COALESCE(d2.name_drafter, '') AS fullset_search_sql"),
                DB::raw("COALESCE(sg.label, '')       AS general_search_sql"),
            ])

            // Eager loads para UI
            ->with([
                'building',
                'seller',
                'drafterPhase1',
                'drafterFullset',
                'status',
                'phase1Status',
                'fullsetStatus',
            ]);

        /* ===== Índice global según el ORDEN ACTUAL (MySQL 8+) ===== */
        $allowed = [
            'projects.id'           => 'projects.id',
            'projects.created_at'   => 'projects.created_at',
            'projects.project_name' => 'projects.project_name',
            'seller_name'           => 'seller_name',
            's1.label'              => 's1.label',
            's2.label'              => 's2.label',
            'sg.label'              => 'sg.label',
        ];
        $field = $allowed[$this->sortField] ?? 'projects.id';
        $dir   = strtolower($this->sortDirection) === 'asc' ? 'asc' : 'desc';

        $q->addSelect(DB::raw("ROW_NUMBER() OVER (ORDER BY {$field} {$dir}) AS row_num"));

        return $q;
    }

    public function relationSearch(): array
    {
        return [
            'building'       => ['name_building'],
            'seller'         => ['name_seller'],
            'drafterPhase1'  => ['name_drafter'],
            'drafterFullset' => ['name_drafter'],
            'phase1Status'   => ['label'],
            'fullsetStatus'  => ['label'],
            'status'         => ['label'],
        ];
    }

    public function fields(): PowerGridFields
    {
        $palette = [
            'pending'           => 'bg-zinc-50 text-zinc-700 ring-zinc-200',
            'working'           => 'bg-sky-50 text-sky-700 ring-sky-200',
            'complete'          => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            'awaiting_approval' => 'bg-amber-50 text-amber-700 ring-amber-200',
            'approved'          => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            'cancelled'         => 'bg-rose-50 text-rose-700 ring-rose-200',
            'deviated'          => 'bg-amber-100 text-amber-800 ring-amber-300',
        ];
        $badge = 'inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset shadow-sm';

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
            ->add('row_num') // índice global
            ->add('id')
            ->add('project_name_text', fn($p) => $p->project_name)

            // UI (HTML)
            ->add('project_with_building', fn($p) =>
                '<div class="flex flex-col">
                    <a wire:navigate href="'.e(route('projects.show', ['project' => $p->id])).'"
                       class="aaa hover:underline font-medium">'.e($p->project_name).'</a>
                    <span class="text-sm text-gray-500">'.e($p->building?->name_building ?? '—').'</span>
                </div>'
            )
            ->add('seller_name', fn($p) => $p->seller?->name_seller ?? '—')

            ->add('phase1_column', function ($p) use ($toneText, $icon) {
                $label   = $p->phase1Status?->label;
                $key     = $p->phase1Status?->key;
                $tone    = $toneText[$key] ?? 'text-gray-500';
                $glyph   = $icon[$key] ?? '•';
                $drafter = e($p->drafterPhase1?->name_drafter ?? '—');

                $statusLine = $label
                    ? '<span class="text-xs '.$tone.'">'.$glyph.' '.e($label).'</span>'
                    : '<span class="text-xs text-gray-400">—</span>';

                return '<div class="flex flex-col items-start">
                          <span class="font-medium text-gray-700">'.$drafter.'</span>
                          '.$statusLine.'
                        </div>';
            })
            ->add('fullset_column', function ($p) use ($toneText, $icon) {
                $label   = $p->fullsetStatus?->label;
                $key     = $p->fullsetStatus?->key;
                $tone    = $toneText[$key] ?? 'text-gray-500';
                $glyph   = $icon[$key] ?? '•';
                $drafter = e($p->drafterFullset?->name_drafter ?? '—');

                $statusLine = $label
                    ? '<span class="text-xs '.$tone.'">'.$glyph.' '.e($label).'</span>'
                    : '<span class="text-xs text-gray-400">—</span>';

                return '<div class="flex flex-col items-start">
                          <span class="font-medium text-gray-700">'.$drafter.'</span>
                          '.$statusLine.'
                        </div>';
            })
            ->add('general_status_chip', function ($p) use ($palette, $badge) {
                $key  = $p->status?->key;
                $tone = $palette[$key] ?? 'bg-gray-50 text-gray-700 ring-gray-200';

                return $p->status
                    ? '<span class="'.$badge.' '.$tone.'"><span class="h-1.5 w-1.5 rounded-full bg-current"></span>'.e($p->general_status_label).'</span>'
                    : '—';
            })

            // Para búsqueda global (solo texto)
            ->add('phase1_search', fn($p) => $p->phase1_drafter_name ?? '')
            ->add('fullset_search', fn($p) => $p->fullset_drafter_name ?? '')
            ->add('general_search', fn($p) => $p->general_status_label_sql ?? '')

            // EXPORT (texto plano)
            ->add('project_export', fn($p) => $p->project_name)
            ->add('building_export', fn($p) => $p->building?->name_building ?? '—')
            ->add('phase1_drafter_export', fn($p) => $p->drafterPhase1?->name_drafter ?? '—')
            ->add('phase1_status_export', fn($p) => $p->phase1Status?->label ?? '—')
            ->add('fullset_drafter_export', fn($p) => $p->drafterFullset?->name_drafter ?? '—')
            ->add('fullset_status_export', fn($p) => $p->fullsetStatus?->label ?? '—')
            ->add('general_status_export', fn($p) => $p->general_status_label ?? '—');
    }

    public function columns(): array
    {
        return [
            // Índice global (no reinicia por página)
            Column::make('#', 'id')
                ->sortable(false)
                
                ->bodyAttribute('text-center'),

            // Muestra HTML pero ordena por nombre real del proyecto
            Column::make('Project', 'project_with_building', 'projects.project_name')
                ->sortable()
                ->searchable(false)
                ->visibleInExport(false),

            // Búsqueda global por nombre (oculta)
            Column::make('ProjectNameSearch', 'project_name_text', 'projects.project_name')
                ->searchable()
                ->hidden(),

            // Seller: callback sort para export seguro
            Column::make('Seller', 'seller_name')
                ->sortable(fn (Builder $q, string $dir) => $q->orderBy('seller_name', $dir))
                ->searchable(),

            // Orden por columnas reales de las JOINs
            Column::make('Phase 1', 'phase1_column', 's1.label')
                ->sortable()
                ->searchable(false)
                ->visibleInExport(false),

            Column::make('Full Set', 'fullset_column', 's2.label')
                ->sortable()
                ->searchable(false)
                ->visibleInExport(false),

            Column::make('General', 'general_status_chip', 'sg.label')
                ->sortable()
                ->searchable(false)
                ->visibleInExport(false),

            // Columnas ocultas para búsqueda global
            Column::make('Phase1Search', 'phase1_search', 'phase1_search_sql')->searchable()->hidden(),
            Column::make('FullsetSearch', 'fullset_search', 'fullset_search_sql')->searchable()->hidden(),
            Column::make('GeneralSearch', 'general_search', 'general_search_sql')->searchable()->hidden(),

            // Export-only (texto plano)
            Column::make('Project', 'project_export')->hidden()->visibleInExport(true),
            Column::make('Building', 'building_export')->hidden()->visibleInExport(true),
            Column::make('Phase 1 Drafter', 'phase1_drafter_export')->hidden()->visibleInExport(true),
            Column::make('Phase 1 Status', 'phase1_status_export')->hidden()->visibleInExport(true),
            Column::make('Full Set Drafter', 'fullset_drafter_export')->hidden()->visibleInExport(true),
            Column::make('Full Set Status', 'fullset_status_export')->hidden()->visibleInExport(true),
            Column::make('General Status', 'general_status_export')->hidden()->visibleInExport(true),

            Column::action('Actions')->visibleInExport(false),
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
                ->class('btn btn-sm')
                ->dispatch('ask-delete-project', ['projectId' => $row->id]),
        ];
    }
}
