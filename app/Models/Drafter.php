<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Drafter extends Model
{

   use HasFactory; 
   protected $table = 'drafters';

    protected $fillable = [
        'name_drafter',
        'description_drafter',
        'status',
    ];

      protected $casts = [
        'status' => 'boolean',
    ];

    // Útil para listar solo activos: Drafter::active()->get()
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
    
}
