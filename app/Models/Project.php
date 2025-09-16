<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Models\Status;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'projects';

    protected $fillable = [
        'project_name',
        // Relaciones
        'building_id',
        'seller_id',
        // Phase 1
        'phase1_drafter_id',
        'phase1_status_id',      // FK a statuses.id
        'phase1_start_date',
        'phase1_end_date',
        // Full Set
        'fullset_drafter_id',
        'fullset_status_id',     // FK a statuses.id
        'fullset_start_date',
        'fullset_end_date',
        // General
        'general_status',        // FK a statuses.id
        'notes',
        // Front Client
        'seller_door_ok','seller_door_notes',
        'seller_accessories_ok','seller_accessories_notes',
        'seller_exterior_finish_ok','seller_exterior_finish_notes',
        'seller_plumbing_fixture_ok','seller_plumbing_fixture_notes',
        'seller_utility_direction_ok','seller_utility_direction_notes',
        'seller_electrical_ok','seller_electrical_notes',
        'other_ok','other_label','other_notes',
    ];

    protected $casts = [
        'phase1_start_date'  => 'date',
        'phase1_end_date'    => 'date',
        'fullset_start_date' => 'date',
        'fullset_end_date'   => 'date',
        'seller_door_ok'              => 'boolean',
        'seller_accessories_ok'       => 'boolean',
        'seller_exterior_finish_ok'   => 'boolean',
        'seller_plumbing_fixture_ok'  => 'boolean',
        'seller_utility_direction_ok' => 'boolean',
        'seller_electrical_ok'        => 'boolean',
        'other_ok'                    => 'boolean',
        'general_status'              => 'integer',
        'phase1_status_id'            => 'integer',
        'fullset_status_id'           => 'integer',
        'deleted_at'                  => 'datetime',
    ];

    /** Defaults seguros por key (no dependas de id=1) */
    protected static function booted(): void
    {
        static::creating(function (Project $p) {
            $pendingId = Status::where('key', 'pending')->value('id');
            if (is_null($p->general_status))     $p->general_status     = $pendingId;
            if (is_null($p->phase1_status_id))   $p->phase1_status_id   = $pendingId;
            if (is_null($p->fullset_status_id))  $p->fullset_status_id  = $pendingId;
        });
    }

    /* =====================
       Relaciones
       ===================== */
    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class, 'building_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    public function drafterPhase1(): BelongsTo
    {
        return $this->belongsTo(Drafter::class, 'phase1_drafter_id');
    }

    public function drafterFullset(): BelongsTo
    {
        return $this->belongsTo(Drafter::class, 'fullset_drafter_id');
    }

    public function comments()
    {
        return $this->hasMany(ProjectComment::class)->latest();
    }

    /** Status general (FK a statuses.id) */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'general_status');
    }

    /** Estados por fase (FKs) */
    public function phase1Status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'phase1_status_id');
    }

    public function fullsetStatus(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'fullset_status_id');
    }

    /* =====================
       Helpers de duración
       ===================== */
    public function getPhase1DurationAttribute(): ?int
    {
        $start = $this->phase1_start_date;
        $end   = $this->phase1_end_date ?? now();

        if (!$start) return null;
        if ($end->lt($start)) [$start, $end] = [$end, $start];

        return $start->diffInDays($end) + 1;
    }

    public function getFullsetDurationAttribute(): ?int
    {
        $start = $this->fullset_start_date;
        $end   = $this->fullset_end_date ?? now();

        if (!$start) return null;
        if ($end->lt($start)) [$start, $end] = [$end, $start];

        return $start->diffInDays($end) + 1;
    }

    /* =====================
       Scopes para grids (por estado general)
       ===================== */
    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        if (!$term) return $q;

        return $q->where('project_name', 'like', "%{$term}%")
                 ->orWhereHas('building', function ($b) use ($term) {
                     $b->where('name_building', 'like', "%{$term}%");
                 });
    }

    public function scopeOfSeller(Builder $q, ?int $sellerId): Builder
    {
        return $sellerId ? $q->where('seller_id', $sellerId) : $q;
    }

    public function scopeOfBuilding(Builder $q, ?int $buildingId): Builder
    {
        return $buildingId ? $q->where('building_id', $buildingId) : $q;
    }

    public function scopeOfDrafterPhase1(Builder $q, ?int $drafterId): Builder
    {
        return $drafterId ? $q->where('phase1_drafter_id', $drafterId) : $q;
    }

    public function scopeOfDrafterFullset(Builder $q, ?int $drafterId): Builder
    {
        return $drafterId ? $q->where('fullset_drafter_id', $drafterId) : $q;
    }

    /* =====================
       Scopes por estado general (por key del catálogo)
       ===================== */
    public function scopeWithStatusKey(Builder $q, string $key): Builder
    {
        return $q->whereHas('status', fn($s) => $s->where('key', $key));
    }

    public function scopePending(Builder $q): Builder        { return $q->withStatusKey('pending'); }
    public function scopeWorking(Builder $q): Builder        { return $q->withStatusKey('working'); }
    public function scopeAwaitingApproval(Builder $q): Builder{ return $q->withStatusKey('awaiting_approval'); }
    public function scopeApproved(Builder $q): Builder       { return $q->withStatusKey('approved'); }
    public function scopeCancelled(Builder $q): Builder      { return $q->withStatusKey('cancelled'); }
    public function scopeDeviated(Builder $q): Builder       { return $q->withStatusKey('deviated'); } // extra útil

    /* =====================
       Helpers UI (opcionales)
       ===================== */
    public function getGeneralStatusLabelAttribute(): string
    {
        return $this->status?->label ?? 'Unknown';
    }

    public function getGeneralStatusBadgeAttribute(): string
    {
        return $this->status?->ui_class ?? 'badge badge-ghost';
    }

    /* =====================
       Helpers Status (key <-> id)
       ===================== */
    public static function statusIdByKey(string $key): ?int
    {
        static $cache = [];
        return $cache[$key] ??= Status::where('key', $key)->value('id');
    }

    public static function statusKeyById(?int $id): ?string
    {
        static $cache = [];
        if (!$id) return null;
        return $cache[$id] ??= optional(Status::find($id))->key;
    }

    /** Comparar/poner estado general por key (helpers) */
    public function isGeneral(string $key): bool
    {
        return self::statusKeyById($this->general_status) === $key;
    }

    public function setGeneral(string $key, bool $save = true): bool
    {
        $id = self::statusIdByKey($key);
        if (!$id) return false;
        $this->general_status = $id;
        return $save ? (bool) $this->save() : true;
    }

    /* =====================
       Reglas de negocio
       ===================== */

    /**
     * Recalcula el estado general si NO está en estados terminales del flujo de revisión.
     * Flujo esperado:
     * pending -> working -> awaiting_approval -> (approved | deviated -> approved)
     */
    public function recalcGeneralStatus(bool $save = true): void
    {
        $currentGeneral = self::statusKeyById($this->general_status);

        // No tocar si está en estados terminales del flujo
        if (in_array($currentGeneral, ['approved','cancelled','awaiting_approval','deviated'], true)) {
            return;
        }

        $p1 = self::statusKeyById($this->phase1_status_id);
        $fs = self::statusKeyById($this->fullset_status_id);
        $hasAssignments = (bool) ($this->phase1_drafter_id || $this->fullset_drafter_id);

        $newKey =
            // ambas fases completas => awaiting_approval
            ($p1 === 'complete' && $fs === 'complete') ? 'awaiting_approval'
            // si alguna fase está working O hay drafters asignados => working
            : (($p1 === 'working' || $fs === 'working' || $hasAssignments) ? 'working'
            // si no, pending
            : 'pending');

        $newId = self::statusIdByKey($newKey);
        if ($newId && $this->general_status !== $newId) {
            $this->general_status = $newId;
            if ($save) $this->saveQuietly();
        }
    }

    /**
     * Aprueba si ambas fases están 'complete' y el general está en 'awaiting_approval' o 'deviated'
     */
    public function approve(): bool
    {
        $phase1Key  = self::statusKeyById($this->phase1_status_id);
        $fullsetKey = self::statusKeyById($this->fullset_status_id);

        $phasesOk = ($phase1Key === 'complete' && $fullsetKey === 'complete');
        $generalKey = self::statusKeyById($this->general_status);

        if ($phasesOk && in_array($generalKey, ['awaiting_approval', 'deviated'], true)) {
            return $this->setGeneral('approved');
        }
        return false;
    }

    /**
     * Pasa el proyecto a estado general "deviated" SOLO si está en 'awaiting_approval'
     * (no toca estados de fase; ya deben estar 'complete' para haber llegado aquí)
     */
    public function markAsDeviated(): bool
    {
        if (! $this->isGeneral('awaiting_approval')) {
            return false; // regla del flujo
        }
        return $this->setGeneral('deviated');
    }
}
