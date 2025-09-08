<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectComment extends Model
{
    protected $fillable = [
        'project_id',
        'user_id',
        'title',      // 👈 nuevo
        'body',
        'is_system',
        'source'
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    public function project(){ return $this->belongsTo(Project::class); }
    public function user(){ return $this->belongsTo(User::class); }

    public function attachments()
{
    return $this->hasMany(ProjectCommentAttachment::class);
}
}