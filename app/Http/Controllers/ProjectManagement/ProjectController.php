<?php

namespace App\Http\Controllers\ProjectManagement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\ProjectManagement\Project;
use App\Models\ProjectManagement\Task;
use App\Models\User;

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
}
