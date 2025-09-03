<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Drafter extends Model
{
    use HasFactory;
    use SoftDeletes; // 👈 habilita Soft Delete

    protected $table = 'drafters'; // tabla en BD

    protected $fillable = [
        'name_drafter',
        'description_drafter',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean', // para que siempre lo recibas como true/false
    ];

    // 👇 Ejemplo de scope: solo drafters activos
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}