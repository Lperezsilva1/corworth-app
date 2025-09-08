<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage; // 👈 IMPORTA ESTO

class ProjectCommentAttachment extends Model
{
    protected $fillable = [
        'project_comment_id','user_id','original_name','disk','path','size','mime'
    ];

    public function comment()
    {
        return $this->belongsTo(ProjectComment::class, 'project_comment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accesor útil
    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }
}