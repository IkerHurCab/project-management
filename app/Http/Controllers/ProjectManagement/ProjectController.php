<?php

namespace App\Http\Controllers\ProjectManagement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\ProjectManagement\Project;
use App\Models\ProjectManagement\Task;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Inertia\Response;


class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'user', 'dateRange']);
    
        $projects = Project::with('leader:id,name')
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where('name', 'ILIKE', "%{$search}%");
            })
            ->when($filters['status'] ?? null, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($filters['user'] ?? null, function ($query, $userId) {
                $query->where('project_leader_id', $userId);
            })
            ->when($filters['dateRange'] ?? null, function ($query, $dateRange) {
                if (!empty($dateRange['start'])) {
                    $query->where('start_date', '>=', $dateRange['start']);
                }
                if (!empty($dateRange['end'])) {
                    $query->where('end_date', '<=', $dateRange['end']);
                }
            })
            ->get();
    
        $departmentHeads = User::whereHas('roles', function ($query) {
            $query->where('name', 'department_head');
        })->get(['id', 'name']);
    
        $statuses = Project::distinct()->pluck('status');
    
        return Inertia::render('ProjectManagement/Projects', [
            'projects' => $projects,
            'filters' => $filters,
            'user' => request()->user(),
            'projectsUrl' => route('projects.index'),
            'departmentHeads' => $departmentHeads,
            'statuses' => $statuses,
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
        ->select('id', 'name')
        ->get()
        ->map(function ($user) {
            return [
                'value' => $user->id, 
                'label' => $user->name, 
            ];
        });
        return Inertia::render('ProjectManagement/CreateProject', [
            'users' => $users,
            'departmentHead' => $departmentHead,
            'user' => request()->user(),
        ]);
    }


    public function store(Request $request)
    {
        // Validación de los datos del formulario
        $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'project_leader_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'assigned_hours' => 'nullable|numeric',
            'status' => 'required|string',
            'is_public' => 'required|boolean',
            'priority' => 'required|integer',
            'description' => 'nullable|string',
            'attachments' => 'nullable|array',
        ]);
     
        $project = Project::create([
            'name' => $request->name,
            'company' => $request->company,
            'project_leader_id' => $request->input('project_leader_id'),
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'assigned_hours' => $request->assigned_hours,
            'status' => $request->status,
            'is_public' => $request->is_public,
            'priority' => $request->priority,
            'description' => $request->description,
            'attachments' => json_encode($request->attachments), // Asegúrate de manejar los archivos correctamente
        ]);


        return redirect()->route('projects.index'); 
    }

}
