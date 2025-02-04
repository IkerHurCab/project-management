<?php

namespace App\Http\Controllers\ProjectManagement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\ProjectManagement\Project;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::all();

        return Inertia::render('ProjectManagement/Projects', [
            'projects' => $projects
        ]);
    }   

    public function show(Project $project)
    {
        return Inertia::render('ProjectManagement/Project', [
            'project' => $project
        ]);
    }
}
