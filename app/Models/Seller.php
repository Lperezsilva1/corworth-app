<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Seller extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sellers';

  protected $fillable = [
    'name_seller',
    'description_seller',
    'email',
    'status',
];

    protected $casts = [
        'status' => 'boolean',
    ];

    // Scope para traer solo activos
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
