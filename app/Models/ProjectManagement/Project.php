<?php

namespace App\Models\ProjectManagement;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProjectManagement\Task;
use App\Models\User;

class Project extends Model
{
    protected $table = 'project';

    protected $fillable = [
        'name',
        'description',
        'assigned_hours',
        'start_date',
        'end_date',
        'status',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class, 'project_id');
    }

    public function users()
{
    return $this->belongsToMany(User::class);
}

}
