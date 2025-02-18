<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProjectManagement\Project;
use App\Models\User;

class Department extends Model
{
    use HasFactory;

    protected $table = 'department';
    protected $fillable = ['name', 'description'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_department');
    }

    public function managers()
    {
        return $this->belongsToMany(User::class, 'department_manager', 'department_id', 'manager_id');
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'department_project', 'department_id', 'project_id');
    }
    
}
