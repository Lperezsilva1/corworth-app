<?php

namespace App\Livewire\Projects;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\{Project, Building, Seller, Drafter, ProjectComment, Status};
use Illuminate\Validation\Rule;

class ProjectsShow extends Component
{
    /** Modelo inyectado por Route Model Binding */
    public Project $project;

    /** Toggle de modo edición */
    public bool $editing = false;

    /** Listas para selects */
    public $buildings = [];
    public $sellers   = [];
    public $drafters  = [];
    public $statuses  = []; // catálogo de estados (id, label, key)

    /** Campos editables existentes */
    public $building_id = null;
    public $seller_id   = null;

    public $phase1_drafter_id = null;
    public ?int $phase1_status_id = null;        // FK int
    public ?string $phase1_start_date = null;    // Y-m-d
    public ?string $phase1_end_date   = null;

    public $fullset_drafter_id = null;
    public ?int $fullset_status_id = null;       // FK int
    public ?string $fullset_start_date = null;   // Y-m-d
    public ?string $fullset_end_date   = null;

    public ?int $general_status = null; // FK (int)
    public ?string $notes = null;

    /** 7 ítems (6 fijos + Otro) */
    // OK flags
    public ?bool $seller_door_ok = null;
    public ?bool $seller_accessories_ok = null;
    public ?bool $seller_exterior_finish_ok = null;
    public ?bool $seller_plumbing_fixture_ok = null;
    public ?bool $seller_utility_direction_ok = null;
    public ?bool $seller_electrical_ok = null;
    public ?bool $other_ok = null;

    // Notas
    public ?string $seller_door_notes = null;
    public ?string $seller_accessories_notes = null;
    public ?string $seller_exterior_finish_notes = null;
    public ?string $seller_plumbing_fixture_notes = null;
    public ?string $seller_utility_direction_notes = null;
    public ?string $seller_electrical_notes = null;

    // “Otro”
    public ?string $other_label = null;
    public ?string $other_notes = null;

    /** Para refrescar comments si los usas */
    public int $commentsVersion = 0;

    public function mount(Project $project): void
    {
        // Cargar relaciones necesarias para la vista (incluye status de general y fases)
        $this->project = $project->load([
            'building','seller','drafterPhase1','drafterFullset','status','phase1Status','fullsetStatus'
        ]);

        $this->buildings = Building::orderBy('name_building')->get(['id','name_building']);
        $this->sellers   = Seller::orderBy('name_seller')->get(['id','name_seller']);
        $this->drafters  = Drafter::orderBy('name_drafter')->get(['id','name_drafter']);
        $this->statuses  = Status::active()->ordered()->get(['id','label','key'])->toArray();

        // Hidratar campos
        $p = $this->project;
        $this->building_id         = $p->building_id;
        $this->seller_id           = $p->seller_id;

        $this->phase1_drafter_id   = $p->phase1_drafter_id;
        $this->phase1_status_id    = $p->phase1_status_id; // ID
        $this->phase1_start_date   = optional($p->phase1_start_date)?->format('Y-m-d');
        $this->phase1_end_date     = optional($p->phase1_end_date)?->format('Y-m-d');

        $this->fullset_drafter_id  = $p->fullset_drafter_id;
        $this->fullset_status_id   = $p->fullset_status_id; // ID
        $this->fullset_start_date  = optional($p->fullset_start_date)?->format('Y-m-d');
        $this->fullset_end_date    = optional($p->fullset_end_date)?->format('Y-m-d');

        $this->general_status      = $p->general_status; // ID de status general
        $this->notes               = $p->notes;

        // 7 ítems
        $this->seller_door_ok              = $p->seller_door_ok;
        $this->seller_accessories_ok       = $p->seller_accessories_ok;
        $this->seller_exterior_finish_ok   = $p->seller_exterior_finish_ok;
        $this->seller_plumbing_fixture_ok  = $p->seller_plumbing_fixture_ok;
        $this->seller_utility_direction_ok = $p->seller_utility_direction_ok;
        $this->seller_electrical_ok        = $p->seller_electrical_ok;
        $this->other_ok                    = $p->other_ok;

        $this->seller_door_notes              = $p->seller_door_notes;
        $this->seller_accessories_notes       = $p->seller_accessories_notes;
        $this->seller_exterior_finish_notes   = $p->seller_exterior_finish_notes;
        $this->seller_plumbing_fixture_notes  = $p->seller_plumbing_fixture_notes;
        $this->seller_utility_direction_notes = $p->seller_utility_direction_notes;
        $this->seller_electrical_notes        = $p->seller_electrical_notes;

        $this->other_label = $p->other_label;
        $this->other_notes = $p->other_notes;
    }

    public function rules(): array
    {
        return [
            // existentes
            'building_id'        => ['nullable','integer','exists:buildings,id'],
            'seller_id'          => ['nullable','integer','exists:sellers,id'],

            'phase1_drafter_id'  => ['nullable','integer','exists:drafters,id'],
            'phase1_status_id'   => ['nullable','integer','exists:statuses,id'], // ID
            'phase1_start_date'  => ['nullable','date'],
            'phase1_end_date'    => ['nullable','date','after_or_equal:phase1_start_date'],

            'fullset_drafter_id' => ['nullable','integer','exists:drafters,id'],
            'fullset_status_id'  => ['nullable','integer','exists:statuses,id'], // ID
            'fullset_start_date' => ['nullable','date'],
            'fullset_end_date'   => ['nullable','date','after_or_equal:fullset_start_date'],

            // FK a statuses.id
            'general_status'     => ['nullable','integer','exists:statuses,id'],
            'notes'              => ['nullable','string'],

            // 7 ítems
            'seller_door_ok'              => ['nullable','boolean'],
            'seller_accessories_ok'       => ['nullable','boolean'],
            'seller_exterior_finish_ok'   => ['nullable','boolean'],
            'seller_plumbing_fixture_ok'  => ['nullable','boolean'],
            'seller_utility_direction_ok' => ['nullable','boolean'],
            'seller_electrical_ok'        => ['nullable','boolean'],
            'other_ok'                    => ['nullable','boolean'],

            // Notas condicionales
            'seller_door_notes' => ['nullable','string','max:2000', function($attr,$val,$fail){ if($this->seller_door_ok === false && trim((string)$val)==='') $fail('Describe qué falta en Puerta.'); }],
            'seller_accessories_notes' => ['nullable','string','max:2000', function($attr,$val,$fail){ if($this->seller_accessories_ok === false && trim((string)$val)==='') $fail('Describe qué falta en Accesorios.'); }],
            'seller_exterior_finish_notes' => ['nullable','string','max:2000', function($attr,$val,$fail){ if($this->seller_exterior_finish_ok === false && trim((string)$val)==='') $fail('Describe qué falta en Exterior Finish.'); }],
            'seller_plumbing_fixture_notes' => ['nullable','string','max:2000', function($attr,$val,$fail){ if($this->seller_plumbing_fixture_ok === false && trim((string)$val)==='') $fail('Describe qué falta en Plumbing Fixture.'); }],
            'seller_utility_direction_notes' => ['nullable','string','max:2000', function($attr,$val,$fail){ if($this->seller_utility_direction_ok === false && trim((string)$val)==='') $fail('Describe qué falta en Utility Direction.'); }],
            'seller_electrical_notes' => ['nullable','string','max:2000', function($attr,$val,$fail){ if($this->seller_electrical_ok === false && trim((string)$val)==='') $fail('Describe qué falta en Eléctrica.'); }],

            // Otro
            'other_label' => ['nullable','string','max:120'],
            'other_notes' => ['nullable','string','max:2000', function($attr,$val,$fail){ if($this->other_ok === false && trim((string)$val)==='') $fail('Describe qué falta en “Otro”.'); }],
        ];
    }

    protected function prepareForValidation($attributes)
    {
        // Normaliza selects/strings vacíos a null
        foreach ([
            'building_id','seller_id','phase1_drafter_id','fullset_drafter_id',
            'phase1_status_id','fullset_status_id','general_status',
            'seller_door_notes','seller_accessories_notes','seller_exterior_finish_notes',
            'seller_plumbing_fixture_notes','seller_utility_direction_notes','seller_electrical_notes',
            'other_label','other_notes'
        ] as $key) {
            if (array_key_exists($key, $attributes) && $attributes[$key] === '') {
                $attributes[$key] = null;
            }
        }
        return $attributes;
    }

    /** Botón “Quitar” en la sección Otro */
    public function clearOther(): void
    {
        $this->other_ok    = null;
        $this->other_label = null;
        $this->other_notes = null;
    }

    /** Guardar todo + auditoría */
    public function saveEdit(): void
    {
        $data = $this->validate();

        // Campos a auditar (incluye los 7 ítems y FKs de fase como IDs)
        $track = [
            'building_id','seller_id',
            'phase1_drafter_id','phase1_status_id','phase1_start_date','phase1_end_date',
            'fullset_drafter_id','fullset_status_id','fullset_start_date','fullset_end_date',
            'general_status','notes',

            'seller_door_ok','seller_door_notes',
            'seller_accessories_ok','seller_accessories_notes',
            'seller_exterior_finish_ok','seller_exterior_finish_notes',
            'seller_plumbing_fixture_ok','seller_plumbing_fixture_notes',
            'seller_utility_direction_ok','seller_utility_direction_notes',
            'seller_electrical_ok','seller_electrical_notes',
            'other_ok','other_label','other_notes',
        ];

        $before = $this->project->only($track);

        // === GUARDAR Y RECALCULAR GENERAL ===
        $this->project->update($data);
        // Asegúrate que el modelo tenga los últimos valores antes de recalcular
        $this->project->refresh();
        // Reglas: complete+complete => awaiting_approval; si hay drafters => working; sin drafters => pending; no baja si approved/deviated/awaiting_approval
        $this->project->recalcGeneralStatus(); // <- respeta flujo

        // Vuelve a leer después del recálculo para el diff
        $after = $this->project->fresh()->only($track);

        // Mapa de labels para IDs de status
        $statusLabelById = Status::pluck('label','id')->all();

        // Diff legible
        $changes = [];
        $labelMap = [
            'seller_door_ok' => 'Puerta',
            'seller_accessories_ok' => 'Accesorios',
            'seller_exterior_finish_ok' => 'Exterior Finish',
            'seller_plumbing_fixture_ok' => 'Plumbing Fixture',
            'seller_utility_direction_ok' => 'Utility Direction',
            'seller_electrical_ok' => 'Eléctrica',
            'seller_door_notes' => 'Puerta (nota)',
            'seller_accessories_notes' => 'Accesorios (nota)',
            'seller_exterior_finish_notes' => 'Exterior Finish (nota)',
            'seller_plumbing_fixture_notes' => 'Plumbing Fixture (nota)',
            'seller_utility_direction_notes' => 'Utility Direction (nota)',
            'seller_electrical_notes' => 'Eléctrica (nota)',
            'other_ok' => 'Otro',
            'other_label' => 'Otro (título)',
            'other_notes' => 'Otro (nota)',
            'general_status' => 'General Status',
            'phase1_status_id' => 'Phase 1 Status',
            'fullset_status_id' => 'Full Set Status',
        ];

        foreach ($after as $k => $v) {
            $prev = $before[$k] ?? null;

            $fmt = function($val, $key) use ($statusLabelById) {
                if (in_array($key, [
                    'seller_door_ok','seller_accessories_ok','seller_exterior_finish_ok',
                    'seller_plumbing_fixture_ok','seller_utility_direction_ok','seller_electrical_ok','other_ok'
                ], true)) {
                    if ($val === null) return '—';
                    return $val ? 'Completo' : 'Pendiente';
                }
                if (in_array($key, ['general_status','phase1_status_id','fullset_status_id'], true)) {
                    return $val ? ($statusLabelById[$val] ?? '—') : '—';
                }
                if ($val === null || $val === '') return '—';
                return (string)$val;
            };

            if ($prev != $v) {
                $name = $labelMap[$k] ?? $k;
                $changes[] = "{$name}: {$fmt($prev,$k)} → {$fmt($v,$k)}";
            }
        }

        if ($changes) {
            ProjectComment::create([
                'project_id' => $this->project->id,
                'user_id'    => Auth::id(),
                'title'      => 'Auto update',
                'body'       => "Updated fields:\n- " . implode("\n- ", $changes),
                'is_system'  => true,
                'source'     => 'auto_diff',
            ]);
        }

        // Refrescar UI
        $this->commentsVersion++;
        $this->project->refresh()->load(['building','seller','drafterPhase1','drafterFullset','status','phase1Status','fullsetStatus']);
        $this->editing = false;
        session()->flash('success', 'Project updated.');
        $this->dispatch('comment-added');
    }

    public function startEdit(): void { $this->editing = true; }

    public function cancelEdit(): void
    {
        $this->mount($this->project);
        $this->editing = false;
    }

    /** Botón: Aprobar (desde awaiting_approval o deviated) si ambas fases están complete */
    public function approveProject(): void
    {
        // Lee estado actual desde DB (evita usar caché en memoria)
        $p = $this->project->fresh(['phase1Status','fullsetStatus','status']);

        // Validar que ambas fases estén "complete"
        $p1Key = $p->phase1Status?->key;
        $fsKey = $p->fullsetStatus?->key;

        if ($p1Key !== 'complete' || $fsKey !== 'complete') {
            session()->flash('error', 'Both Phase 1 and Full Set must be complete before approval.');
            return;
        }

        // Evitar cambios si ya está finalizado
        $generalKey = $p->status?->key;
        if (in_array($generalKey, ['approved','cancelled'], true)) {
            session()->flash('success', 'Project is already finalized.');
            return;
        }

        // Regla de flujo: aprobar solo desde awaiting_approval o deviated
        if (! in_array($generalKey, ['awaiting_approval','deviated'], true)) {
            session()->flash('error', 'You can only approve from Awaiting Approval or Deviated.');
            return;
        }

        // Poner general_status = approved
        $approvedId = Status::where('key', 'approved')->value('id');
        if (!$approvedId) {
            session()->flash('error', "Status 'approved' not found.");
            return;
        }

        $p->general_status = $approvedId;
        $p->saveQuietly();

        // Auditar en comentarios
        ProjectComment::create([
            'project_id' => $p->id,
            'user_id'    => Auth::id(),
            'title'      => 'Approval',
            'body'       => 'Project marked as Approved.',
            'is_system'  => true,
            'source'     => 'approve_button',
        ]);

        session()->flash('success', 'Project approved.');

        // Refresca datos para la vista
        $this->project = $p->fresh([
            'building','seller','drafterPhase1','drafterFullset',
            'status','phase1Status','fullsetStatus'
        ]);
    }

    /** Botón: Desviar a "deviated" (solo desde awaiting_approval) */
    public function markAsDeviated(): void
    {
        // Cargar estado actual
        $p = $this->project->fresh(['status','phase1Status','fullsetStatus']);

        // Solo permitir desvío desde awaiting_approval
        if ($p->status?->key !== 'awaiting_approval') {
            session()->flash('error', 'You can only deviate from Awaiting Approval.');
            return;
        }

        // Cambiar general a deviated
        $deviatedId = Status::where('key', 'deviated')->value('id');
        if (!$deviatedId) {
            session()->flash('error', "Status 'deviated' not found.");
            return;
        }

        $p->general_status = $deviatedId;
        $p->saveQuietly();

        // Auditar
        ProjectComment::create([
            'project_id' => $p->id,
            'user_id'    => Auth::id(),
            'title'      => 'Deviation',
            'body'       => 'Project moved to Deviated (PFS returned for revisions).',
            'is_system'  => true,
            'source'     => 'deviated_button',
        ]);

        session()->flash('success', 'Project moved to Deviated.');

        // Refrescar
        $this->project = $p->fresh([
            'building','seller','drafterPhase1','drafterFullset',
            'status','phase1Status','fullsetStatus'
        ]);
    }

    public function render()
    {
        return view('livewire.projects.projects-show');
    }
}
