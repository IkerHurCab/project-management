<?php

namespace App\Models\ProjectManagement;

use Illuminate\Database\Eloquent\Model;
use App\Models\Users\Department;

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

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'department_project', 'project_id', 'department_id');
    }

}
