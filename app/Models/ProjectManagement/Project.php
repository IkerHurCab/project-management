<?php

namespace App\Models\ProjectManagement;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProjectManagement\Task;
use App\Models\Users\Department;
use App\Models\User;

class Project extends Model
{
    protected $table = 'project';

    protected $fillable = [
        'name',  'project_leader_id', 'start_date', 'end_date', 'assigned_hours', 'status', 'description', 'priority', 'is_public', 'attachments',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class, 'project_id');
    }

    public function users()
{
    return $this->belongsToMany(User::class);
}
    public function leader()
    {
        return $this->belongsTo(User::class, 'project_leader_id');
    }

    public function documentation()
    {
        return $this->hasMany(ProjectDocumentation::class);

    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'department_project', 'project_id', 'department_id');
    }

}
