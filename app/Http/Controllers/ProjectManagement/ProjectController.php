<?php

namespace App\Http\Controllers\ProjectManagement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\ProjectManagement\Project;
use App\Models\ProjectManagement\Task;
use App\Models\User;
use Spatie\Permission\Models\Role;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::all();

        return Inertia::render('ProjectManagement/Projects', [
            'projects' => $projects,
            'user' => request()->user(),
        ]);
    }   

    public function show($id)
    {
    
        $project = Project::with(['tasks', 'users'])->findOrFail($id);
        $project->load(['tasks.user']);
        
    if (!$project) {
       
        abort(404, 'Project not found');
    }

        return Inertia::render('ProjectManagement/SingleProject', [
            'project' => $project,
            'user' => request()->user(),
        ]);
    }

    public function create()
    {
        $users = User::all();
        $departmentHead = User::role('department_head')
        ->select ('id', 'name')
        ->get();
    
        return Inertia::render('ProjectManagement/CreateProject', [
            'users' => $users,
            'departmentHead' => $departmentHead,
            'user' => request()->user(),
        ]);
    }
}
