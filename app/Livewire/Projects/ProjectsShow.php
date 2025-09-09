<?php

namespace App\Livewire\Projects;

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
    public $statuses  = []; // ← NUEVO: catálogo de estados (id, label, key)

    /** Campos editables existentes */
    public $building_id = null;
    public $seller_id   = null;

    public $phase1_drafter_id = null;
    public ?string $phase1_status = null;
    public ?string $phase1_start_date = null;   // Y-m-d
    public ?string $phase1_end_date   = null;

    public $fullset_drafter_id = null;
    public ?string $fullset_status = null;
    public ?string $fullset_start_date = null;  // Y-m-d
    public ?string $fullset_end_date   = null;

    public ?int $general_status = null; // ← CAMBIO: ahora es FK (int)
    public ?string $notes = null;

    /** Opciones de estado por fase (se quedan como estaban) */
    public array $phase1StatusOptions  = ['Not started','In progress','Blocked',"Phase 1's Complete"];
    public array $fullsetStatusOptions = ['Not started','In progress','Blocked','Full Set Complete'];

    /** ← NUEVO: 7 ítems (6 fijos + Otro) */
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
        // Cargamos también 'status' para tener el label actual
        $this->project = $project->load(['building','seller','drafterPhase1','drafterFullset','status']);

        $this->buildings = Building::orderBy('name_building')->get(['id','name_building']);
        $this->sellers   = Seller::orderBy('name_seller')->get(['id','name_seller']);
        $this->drafters  = Drafter::orderBy('name_drafter')->get(['id','name_drafter']);
        $this->statuses  = Status::active()->ordered()->get(['id','label','key'])->toArray(); // ← catálogo

        // Hidratar campos
        $p = $this->project;
        $this->building_id         = $p->building_id;
        $this->seller_id           = $p->seller_id;

        $this->phase1_drafter_id   = $p->phase1_drafter_id;
        $this->phase1_status       = $p->phase1_status;
        $this->phase1_start_date   = optional($p->phase1_start_date)?->format('Y-m-d');
        $this->phase1_end_date     = optional($p->phase1_end_date)?->format('Y-m-d');

        $this->fullset_drafter_id  = $p->fullset_drafter_id;
        $this->fullset_status      = $p->fullset_status;
        $this->fullset_start_date  = optional($p->fullset_start_date)?->format('Y-m-d');
        $this->fullset_end_date    = optional($p->fullset_end_date)?->format('Y-m-d');

        $this->general_status      = $p->general_status; // ← ID del status
        $this->notes               = $p->notes;

        // ← NUEVO: 7 ítems
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
            'phase1_status'      => ['nullable','string', Rule::in($this->phase1StatusOptions)],
            'phase1_start_date'  => ['nullable','date'],
            'phase1_end_date'    => ['nullable','date','after_or_equal:phase1_start_date'],

            'fullset_drafter_id' => ['nullable','integer','exists:drafters,id'],
            'fullset_status'     => ['nullable','string', Rule::in($this->fullsetStatusOptions)],
            'fullset_start_date' => ['nullable','date'],
            'fullset_end_date'   => ['nullable','date','after_or_equal:fullset_start_date'],

            // ← CAMBIO: valida FK a statuses.id
            'general_status'     => ['nullable','integer','exists:statuses,id'],
            'notes'              => ['nullable','string'],

            // ← NUEVO: 7 ítems
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
            'phase1_status','fullset_status','general_status',
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

        // Campos a auditar (incluye los 7 ítems)
        $track = [
            'building_id','seller_id',
            'phase1_drafter_id','phase1_status','phase1_start_date','phase1_end_date',
            'fullset_drafter_id','fullset_status','fullset_start_date','fullset_end_date',
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

        // Guardar
        $this->project->update($data);

        $after = $this->project->fresh()->only($track);

        // Mapa de labels para general_status (evita mostrar IDs)
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
        ];

        foreach ($after as $k => $v) {
            $prev = $before[$k] ?? null;

            $fmt = function($val, $key) use ($statusLabelById) {
                // Booleans
                if (in_array($key, [
                    'seller_door_ok','seller_accessories_ok','seller_exterior_finish_ok',
                    'seller_plumbing_fixture_ok','seller_utility_direction_ok','seller_electrical_ok','other_ok'
                ], true)) {
                    if ($val === null) return '—';
                    return $val ? 'Completo' : 'Pendiente';
                }
                // General status (FK → label)
                if ($key === 'general_status') {
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
                'user_id'    => auth()->id(),
                'title'      => 'Auto update',
                'body'       => "Updated fields:\n- " . implode("\n- ", $changes),
                'is_system'  => true,
                'source'     => 'auto_diff',
            ]);
        }

        // Refrescar UI
        $this->commentsVersion++;
        $this->project->refresh()->load(['building','seller','drafterPhase1','drafterFullset','status']);
        $this->editing = false;
        session()->flash('success', 'Project updated.');
        $this->dispatch('comment-added'); // si usas el scroll en comments
    }

    public function startEdit(): void { $this->editing = true; }

    public function cancelEdit(): void
    {
        $this->mount($this->project);
        $this->editing = false;
    }

    public function render()
    {
        return view('livewire.projects.projects-show');
    }
}
