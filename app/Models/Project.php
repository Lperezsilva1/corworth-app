<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;              // 👈 IMPORTANTE
use Illuminate\Database\Eloquent\Builder;
use App\Models\Status;                                     // 👈 IMPORTANTE

class Project extends Model
{
    use HasFactory, SoftDeletes; // 👈 activar

    protected $table = 'projects';

    protected $fillable = [
        'project_name',
        // Relaciones
        'building_id',
        'seller_id',
        // Phase 1
        'phase1_drafter_id',
        'phase1_status',
        'phase1_start_date',
        'phase1_end_date',
        // Full Set
        'fullset_drafter_id',
        'fullset_status',
        'fullset_start_date',
        'fullset_end_date',
        // General
        'general_status', // ahora FK a statuses.id
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
        'general_status'              => 'integer', // FK entero
        'deleted_at'                  => 'datetime',
    ];

    /** Default a pending (1) si no viene nada al crear (opcional, pero recomendable) */
    protected static function booted(): void
    {
        static::creating(function (Project $p) {
            if (is_null($p->general_status)) {
                $p->general_status = 1; // pending
            }
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
        // Nota: la FK está en projects.general_status
        return $this->belongsTo(Status::class, 'general_status');
    }

    /* =====================
       Helpers de duración
       ===================== */
    public function getPhase1DurationComputedAttribute(): ?int
    {
        return ($this->phase1_start_date && $this->phase1_end_date)
            ? $this->phase1_end_date->diffInDays($this->phase1_start_date) + 1
            : null;
    }

    public function getFullsetDurationComputedAttribute(): ?int
    {
        return ($this->fullset_start_date && $this->fullset_end_date)
            ? $this->fullset_end_date->diffInDays($this->fullset_start_date) + 1
            : null;
    }

    /* =====================
       Scopes para grids
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
       Scopes por estado (por key del catálogo)
       ===================== */
    public function scopeWithStatusKey(Builder $q, string $key): Builder
    {
        return $q->whereHas('status', fn($s) => $s->where('key', $key));
    }

    public function scopePending(Builder $q): Builder
    {   return $q->withStatusKey('pending'); }

    public function scopeWorking(Builder $q): Builder
    {   return $q->withStatusKey('working'); }

    public function scopeAwaitingApproval(Builder $q): Builder
    {   return $q->withStatusKey('awaiting_approval'); }

    public function scopeApproved(Builder $q): Builder
    {   return $q->withStatusKey('approved'); }

    public function scopeCancelled(Builder $q): Builder
    {   return $q->withStatusKey('cancelled'); }

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
}
