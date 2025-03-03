<?php

namespace App\Models\ProjectManagement;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProjectManagement\Task;
use App\Models\User;

class Comment extends Model
{
    protected $table = 'comment';

    protected $fillable = [
        'user_id', 'task_id', 'content',
    ];

    public function tasks()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
        
    }
}
