<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status extends Model
{
    protected $fillable = ['key','label','ui_class','display_order','is_final','is_active','description'];

    protected $casts = [
        'is_final'  => 'boolean',
        'is_active' => 'boolean',
    ];

    public function projects(): HasMany
    {
        // FK está en projects.general_status
        return $this->hasMany(Project::class, 'general_status');
    }

    /* Scopes útiles */
    public function scopeActive($q)  { return $q->where('is_active', true); }
    public function scopeOrdered($q) { return $q->orderBy('display_order'); }
}
