<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
   use HasFactory;

    protected $table = 'sellers';

    protected $fillable = [
        'name_seller',
        'description_seller',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
