<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status extends Model
{
    protected $fillable = [
        'key','label','ui_class','display_order','is_final','is_active','description'
    ];

    protected $casts = [
        'is_final'      => 'boolean',
        'is_active'     => 'boolean',
        'display_order' => 'integer',
    ];

    // Si tu tabla NO tiene created_at/updated_at, descomenta:
    public $timestamps = false;

    /* ========= Constantes (evitan typos) ========= */
    public const PENDING           = 'pending';
    public const WORKING           = 'working';
    public const COMPLETE          = 'complete';           // solo fases
    public const AWAITING_APPROVAL = 'awaiting_approval';
    public const APPROVED          = 'approved';
    public const CANCELLED         = 'cancelled';

    /* ========= Relaciones ========= */
    // FK está en projects.general_status
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'general_status');
    }

    // Inversas por fases (útiles para reportes)
    public function projectsPhase1(): HasMany
    {
        return $this->hasMany(Project::class, 'phase1_status_id');
    }

    public function projectsFullset(): HasMany
    {
        return $this->hasMany(Project::class, 'fullset_status_id');
    }

    /* ========= Scopes útiles ========= */
    public function scopeActive($q)  { return $q->where('is_active', true); }
    public function scopeOrdered($q) { return $q->orderBy('display_order'); }

    // Estados permitidos para el status GENERAL
    public function scopeForGeneral($q)
    {
        return $q->whereIn('key', [
            self::PENDING, self::WORKING, self::AWAITING_APPROVAL, self::APPROVED, self::CANCELLED,
        ]);
    }

    // Estados permitidos para las FASES
    public function scopeForPhase($q)
    {
        return $q->whereIn('key', [
            self::PENDING, self::WORKING, self::COMPLETE,
        ]);
    }

    /* ========= Helpers (cache simple) ========= */
    public static function idByKey(string $key): ?int
    {
        static $cache = [];
        return $cache[$key] ??= self::where('key', $key)->value('id');
    }

    public static function keyById(?int $id): ?string
    {
        static $cache = [];
        if (!$id) return null;
        return $cache[$id] ??= optional(self::find($id))->key;
    }
}
