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
    ];

    protected $casts = [
        'phase1_start_date'  => 'date',
        'phase1_end_date'    => 'date',
        'fullset_start_date' => 'date',
        'fullset_end_date'   => 'date',
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
