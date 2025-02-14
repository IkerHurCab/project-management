<?php

namespace App\Models\ProjectManagement;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProjectManagement\Project;
use App\Models\User;
use App\Models\ProjectManagement\Comment;

class Task extends Model
{
    protected $table = 'task';

    protected $fillable = [
        'name',
        'description',
        'estimated_hours',
        'completed_hours',
        'status',
        'priority',
        'start_date',
        'end_date',
        'project_id',
        'user_id',
        'attachments'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
