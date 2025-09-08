<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

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
        'general_status',
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
    ];

    /* =====================
       Relaciones
       ===================== */
    public function building()
    {
        return $this->belongsTo(Building::class, 'building_id');
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    public function drafterPhase1()
    {
        return $this->belongsTo(Drafter::class, 'phase1_drafter_id');
    }

    public function drafterFullset()
    {
        return $this->belongsTo(Drafter::class, 'fullset_drafter_id');
    }

   
    public function comments()
    {
        return $this->hasMany(ProjectComment::class)->latest();
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
    public function scopeSearch($q, ?string $term)
    {
        if (!$term) return $q;

        return $q->where('project_name', 'like', "%{$term}%")
                 ->orWhereHas('building', function ($b) use ($term) {
                     $b->where('name_building', 'like', "%{$term}%");
                 });
    }

    public function scopeOfSeller($q, ?int $sellerId)
    {
        return $sellerId ? $q->where('seller_id', $sellerId) : $q;
    }

    public function scopeOfBuilding($q, ?int $buildingId)
    {
        return $buildingId ? $q->where('building_id', $buildingId) : $q;
    }

    public function scopeOfDrafterPhase1($q, ?int $drafterId)
    {
        return $drafterId ? $q->where('phase1_drafter_id', $drafterId) : $q;
    }

    public function scopeOfDrafterFullset($q, ?int $drafterId)
    {
        return $drafterId ? $q->where('fullset_drafter_id', $drafterId) : $q;
    }
}
