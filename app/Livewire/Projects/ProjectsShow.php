<?php

namespace App\Livewire\Projects;

use Livewire\Component;
use App\Models\ProjectComment;
use App\Models\{Project, Building, Seller, Drafter};
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

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

    /** Campos editables (coinciden con $fillable) */
    // Nota: mantenemos Project Name en solo lectura (como preferiste)
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

    public ?string $general_status = null;
    public ?string $notes = null;

    /** Opciones de estado */
    public array $phase1StatusOptions  = ['Not started','In progress','Blocked',"Phase 1's Complete"];
    public array $fullsetStatusOptions = ['Not started','In progress','Blocked','Full Set Complete'];
    public array $generalStatusOptions = ['Not Approved','Approved','Cancelled'];

    public int $commentsVersion = 0;

    public function mount(Project $project): void
    {
        // Modelo + relaciones
        $this->project = $project->load(['building','seller','drafterPhase1','drafterFullset']);

        // Listas
        $this->buildings = Building::orderBy('name_building')->get(['id','name_building']);
        $this->sellers   = Seller::orderBy('name_seller')->get(['id','name_seller']);
        $this->drafters  = Drafter::orderBy('name_drafter')->get(['id','name_drafter']);

        // Hidratar campos (para edición)
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

        $this->general_status      = $p->general_status;
        $this->notes               = $p->notes;
    }

    public function rules(): array
    {
        return [
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

            'general_status'     => ['nullable','string', Rule::in($this->generalStatusOptions)],
            'notes'              => ['nullable','string'],
        ];
    }

    protected function prepareForValidation($attributes)
    {
        // Normaliza selects vacíos a null
        foreach ([
            'building_id','seller_id','phase1_drafter_id','fullset_drafter_id',
            'phase1_status','fullset_status','general_status'
        ] as $key) {
            if (array_key_exists($key, $attributes) && $attributes[$key] === '') {
                $attributes[$key] = null;
            }
        }
        return $attributes;
    }

    /** Guardar todo desde los tabs */
    public function saveEdit(): void
    {
        $data = $this->validate();

        // 1) Campos que quieres auditar
        $track = [
            'building_id','seller_id',
            'phase1_drafter_id','phase1_status','phase1_start_date','phase1_end_date',
            'fullset_drafter_id','fullset_status','fullset_start_date','fullset_end_date',
            'general_status','notes',
        ];

        // 2) Tomar snapshot ANTES
        $before = $this->project->only($track);

        // 3) Guardar cambios
        $this->project->update($data);

        // 4) Tomar snapshot DESPUÉS
        $after = $this->project->fresh()->only($track);

        // 5) Armar diff legible (IDs -> nombres, fechas y labels bonitos)

        // Labels para mostrar
        $labels = [
            'building_id'        => 'Building',
            'seller_id'          => 'Seller',
            'phase1_drafter_id'  => 'Phase 1 Drafter',
            'phase1_status'      => "Phase 1 Status",
            'phase1_start_date'  => "Phase 1 Start",
            'phase1_end_date'    => "Phase 1 End",
            'fullset_drafter_id' => 'Fullset Drafter',
            'fullset_status'     => 'Fullset Status',
            'fullset_start_date' => "Fullset Start",
            'fullset_end_date'   => "Fullset End",
            'general_status'     => 'General Status',
            'notes'              => 'Notes',
        ];

        // Campos fecha
        $dateFields = [
            'phase1_start_date','phase1_end_date',
            'fullset_start_date','fullset_end_date',
        ];

        // Prepara IDs únicos para resolver nombres (evita N+1)
        $buildingIds = collect([$before['building_id'] ?? null, $after['building_id'] ?? null])
            ->filter()->unique()->values();
        $sellerIds   = collect([$before['seller_id'] ?? null, $after['seller_id'] ?? null])
            ->filter()->unique()->values();
        $drafterIds  = collect([
            $before['phase1_drafter_id'] ?? null, $after['phase1_drafter_id'] ?? null,
            $before['fullset_drafter_id'] ?? null, $after['fullset_drafter_id'] ?? null,
        ])->filter()->unique()->values();

        // Mapas id -> nombre
        $buildingMap = $buildingIds->isNotEmpty()
            ? Building::whereIn('id', $buildingIds)->pluck('name_building', 'id')
            : collect();
        $sellerMap   = $sellerIds->isNotEmpty()
            ? Seller::whereIn('id', $sellerIds)->pluck('name_seller', 'id')
            : collect();
        $drafterMap  = $drafterIds->isNotEmpty()
            ? Drafter::whereIn('id', $drafterIds)->pluck('name_drafter', 'id')
            : collect();

        // Formateador de valores bonitos por campo
        $pretty = function (string $field, $value) use ($dateFields, $buildingMap, $sellerMap, $drafterMap) {
            if ($value === null || $value === '') {
                return '—';
            }

            // IDs -> nombres
            if ($field === 'building_id')        return (string) ($buildingMap[$value] ?? $value);
            if ($field === 'seller_id')          return (string) ($sellerMap[$value]   ?? $value);
            if ($field === 'phase1_drafter_id')  return (string) ($drafterMap[$value]  ?? $value);
            if ($field === 'fullset_drafter_id') return (string) ($drafterMap[$value]  ?? $value);

            // Fechas -> formato legible
            if (in_array($field, $dateFields, true)) {
                try {
                    return Carbon::parse($value)->format('M d, Y');
                } catch (\Throwable $e) {
                    return (string) $value;
                }
            }

            // Status/strings directos
            return is_scalar($value) ? (string) $value : json_encode($value);
        };

        // Construye las líneas de cambio
        $prettyChanges = [];
        foreach ($after as $k => $v) {
            $prev = $before[$k] ?? null;
            if ($prev != $v) {
                $label = $labels[$k] ?? Str::of($k)->replace('_', ' ')->title();
                $old   = $pretty($k, $prev);
                $new   = $pretty($k, $v);
                $prettyChanges[] = "{$label}: {$old} → {$new}";
            }
        }

        if (!empty($prettyChanges)) {
            ProjectComment::create([
                'project_id' => $this->project->id,
                'user_id'    => auth()->id(),
                'title'      => 'Auto update',
                'body'       => "Updated fields:\n- " . implode("\n- ", $prettyChanges),
                'is_system'  => true,
                'source'     => 'auto_diff',
            ]);
        }

        // 6) Refrescar UI
        $this->commentsVersion++;
        $this->project->refresh()->load(['building','seller','drafterPhase1','drafterFullset']);
        $this->editing = false;
        session()->flash('success', 'Project updated.');
    }

    public function startEdit(): void
    {
        $this->editing = true;
    }

    public function cancelEdit(): void
    {
        // Rehidratamos campos para descartar cambios no guardados
        $this->mount($this->project);
        $this->editing = false;
    }

    public function render()
    {
        return view('livewire.projects.projects-show');
    }
}
