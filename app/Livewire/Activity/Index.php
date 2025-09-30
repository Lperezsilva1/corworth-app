<?php

namespace App\Livewire\Activity;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ProjectComment;

// 👇 NUEVO: imports de catálogos que mapearemos
use App\Models\Drafter;
use App\Models\Building;
use App\Models\Seller;

class Index extends Component
{
    use WithPagination;

    public string $paginationMode = 'cursor';

    public string $search = '';
    public int $perPage = 15;
    public ?string $from = null;
    public ?string $to   = null;

    protected $queryString = [
        'search'  => ['except' => ''],
        'perPage' => ['except' => 15],
        'from'    => ['except' => null],
        'to'      => ['except' => null],
    ];

    // 👇 NUEVO: cachés locales para mapear ids → nombres
    protected array $drafterNames  = [];
    protected array $buildingNames = [];
    protected array $sellerNames   = [];

    public function updatingSearch()  { $this->resetPage(); }
    public function updatingPerPage() { $this->resetPage(); }
    public function updatingFrom()    { $this->resetPage(); }
    public function updatingTo()      { $this->resetPage(); }

    // 👇 NUEVO: helpers de nombre con caché
    protected function drafterName(?int $id): ?string
    {
        if (!$id) return null;
        if (!array_key_exists($id, $this->drafterNames)) {
            $this->drafterNames[$id] = Drafter::find($id)?->name_drafter;
        }
        return $this->drafterNames[$id];
    }

    protected function buildingName(?int $id): ?string
    {
        if (!$id) return null;
        if (!array_key_exists($id, $this->buildingNames)) {
            $this->buildingNames[$id] = Building::find($id)?->name_building;
        }
        return $this->buildingNames[$id];
    }

    protected function sellerName(?int $id): ?string
    {
        if (!$id) return null;
        if (!array_key_exists($id, $this->sellerNames)) {
            $this->sellerNames[$id] = Seller::find($id)?->name_seller;
        }
        return $this->sellerNames[$id];
    }

    /**
     * 👇 NUEVO: “presentador” para el body de la actividad.
     * Convierte líneas tipo:
     *   - phase1_drafter_id: — → 3
     * en:
     *   - phase1_drafter: — → Juan Pérez
     */
    public function presentActivityBody(ProjectComment $c): ?string
    {
        $body = (string) ($c->body ?? '');
        if ($body === '') return null;

        // Solo si parece un bloque de "Updated fields:"
        if (!str_contains($body, 'Updated fields')) {
            return $body;
        }

        $lines = preg_split('/\R/u', $body) ?: [];
        $out   = [];

        foreach ($lines as $line) {
            $trim = trim($line);

            // Intentamos capturar "- field: old → new"
            if (preg_match('/^-+\s*([a-z0-9_]+)\s*:\s*(.+?)\s*→\s*(.+)\s*$/i', $trim, $m)) {
                $field = $m[1];
                $old   = trim($m[2]);
                $new   = trim($m[3]);

                $label = $field;

                // Campos a “humanizar”
                if (in_array($field, ['phase1_drafter_id','fullset_drafter_id'], true)) {
                    $label = str_replace('_id', '', $field);
                    $oldH  = is_numeric($old) ? ($this->drafterName((int)$old) ?? $old) : $old;
                    $newH  = is_numeric($new) ? ($this->drafterName((int)$new) ?? $new) : $new;
                    $out[] = "- {$label}: {$oldH} → {$newH}";
                    continue;
                }

                if ($field === 'building_id') {
                    $label = 'building';
                    $oldH  = is_numeric($old) ? ($this->buildingName((int)$old) ?? $old) : $old;
                    $newH  = is_numeric($new) ? ($this->buildingName((int)$new) ?? $new) : $new;
                    $out[] = "- {$label}: {$oldH} → {$newH}";
                    continue;
                }

                if ($field === 'seller_id') {
                    $label = 'seller';
                    $oldH  = is_numeric($old) ? ($this->sellerName((int)$old) ?? $old) : $old;
                    $newH  = is_numeric($new) ? ($this->sellerName((int)$new) ?? $new) : $new;
                    $out[] = "- {$label}: {$oldH} → {$newH}";
                    continue;
                }

                // Status genéricos: limpia sufijo _id si viene así
                if (str_ends_with($field, '_status_id')) {
                    $label = preg_replace('/_id$/', '', $field) ?? $field;
                }

                // Por defecto, deja la línea tal cual
                $out[] = "- {$label}: {$old} → {$new}";
            } else {
                // Otras líneas: se copian sin cambio
                $out[] = $line;
            }
        }

        return implode("\n", $out);
    }

    public function render()
    {
        $base = ProjectComment::query()
            ->with(['project:id,project_name', 'user:id,name'])
            ->when($this->from, fn ($q) => $q->whereDate('created_at', '>=', $this->from))
            ->when($this->to,   fn ($q) => $q->whereDate('created_at', '<=', $this->to))
            ->when($this->search, function ($q) {
                $term = "%{$this->search}%";
                $q->where(function ($sub) use ($term) {
                    $sub->where('title', 'like', $term)
                        ->orWhere('body', 'like', $term)
                        ->orWhereHas('project', fn ($p) => $p->where('project_name', 'like', $term));
                });
            })
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        $activities = match ($this->paginationMode) {
            'page'   => $base->paginate($this->perPage)->withQueryString(),
            'simple' => $base->simplePaginate($this->perPage)->withQueryString(),
            default  => $base->cursorPaginate($this->perPage)->withQueryString(),
        };

        return view('livewire.activity.index', compact('activities'));
    }
}
