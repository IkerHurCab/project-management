<?php

namespace App\Http\Controllers\ProjectManagement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\ProjectManagement\Project;
use App\Models\ProjectManagement\Task;
use App\Models\ProjectManagement\ProjectDocumentation;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Inertia\Response;



class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'user', 'dateRange', 'my_projects']);
$currentUser = $request->user();
$projects = Project::with('leader:id,name', 'users:id,name')
    ->when($filters['search'] ?? null, function ($query, $search) {
        $query->where('name', 'ILIKE', "%{$search}%");
    })
    ->when($filters['status'] ?? null, function ($query, $status) {
        $query->where('status', $status);
    })
    ->when($filters['user'] ?? null, function ($query, $userName) {
        $query->whereHas('leader', function ($query) use ($userName) {
            $query->where('name', 'ILIKE', "%{$userName}%");
        });
    })
    ->when($filters['dateRange'] ?? null, function ($query, $dateRange) {
        if (!empty($dateRange['start'])) {
            $query->whereDate('start_date', '>=', $dateRange['start']);
        }
        if (!empty($dateRange['end'])) {
            $query->whereDate('end_date', '<=', $dateRange['end']);
        }
    })
    
    
    ->when($filters['my_projects'] ?? null, function ($query) use ($currentUser) {
        // Solo incluir proyectos en los que el usuario actual está involucrado
        $query->whereHas('users', function ($query) use ($currentUser) {
            $query->where('user_id', $currentUser->id);
        });
    })
    ->get();


     
    
        $departmentHeads = User::whereHas('roles', function ($query) {
            $query->where('name', 'department_head');
        })->get(['id', 'name']);
    
        $statuses = Project::distinct()->pluck('status');
        
      

        return Inertia::render('ProjectManagement/Project/Projects', [
            'projects' => $projects,
            'filters' => $filters,
            'user' => request()->user(),
            'projectsUrl' => route('projects.index'),
            'departmentHeads' => $departmentHeads,
            'statuses' => $statuses,
        ]);
    }

    public function show($id, Request $request)
    {
    
        $searchMember = $request->query('searchMember');
         
        $project = Project::with(['tasks', 'users', 'leader'])->findOrFail($id);
        $project->load(['tasks.user']);

        $membersQuery = $project->users()->newQuery();  

    if ($searchMember) {
        $membersQuery->where('name', 'LIKE', "%$searchMember%");
    }

    $members = $membersQuery->get();

    if (!$project) { 
        abort(404, 'Project not found');
    }
    
    
    $personalTasks = $project->tasks->filter(function ($task) {
        return $task->user_id === auth()->id() && $task->status !== 'done';
    });

    // pending of change to all users FROM THE SAME DEPARTMENT
    
    $allUsers = User::whereNotIn('id', $members->pluck('id'))->get();

    $totalCompletedTasks = $project->tasks->filter(function ($task) {
        return $task->status === 'done';
    })->count();

    $documentation = ProjectDocumentation::where('project_id', $id)->get();

    $activeTab = $request->session()->get('activeTab', '');
    $openSingleDoc = $request->session()->get('openSingleDoc', false);
    $createDoc = $request->session()->get('createDoc', false);

    $user = request()->user();

    if($user->id === $project->project_leader_id) {
        $tasks = $project->tasks()->with('user')->get();  // Carga las tareas y los usuarios asociados
    } else {
        $tasks = $project->tasks()->with('user')->where('user_id', $user->id)->get();
    }

    $projectLeaderId = $project->project_leader_id;

    $isProjectLeader = ($projectLeaderId === $user->id);



    return Inertia::render('ProjectManagement/Project/SingleProject', [
        'project' => $project,
        'user' => $user,
        'tasks' => $tasks,
        'personalTasks' => $personalTasks,
        'employees' => $members,
        'searchQuery' => $request->input('searchMember', ''),
        'allUsers' => $allUsers,
        'tasksCompleted' => $totalCompletedTasks,
        'documentations' => $documentation,
        'activeTab' => $activeTab,
        'openSingleDoc' => $openSingleDoc,  
        'createDoc' => $createDoc, 
        'isProjectLeader' => $isProjectLeader,
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
        return Inertia::render('ProjectManagement/Project/CreateProject', [
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
            'attachments' => json_encode($request->attachments), 
        ]);


        return redirect()->route('projects.index'); 
    }

    public function storeMember(Request $request, $projectId)
    {
       
        $user = $request->input('users');
      
        $usersId = array_map(function($user){
            return $user['id'];
        }, $request->input('users'));

    
        $project = Project::findOrFail($projectId); 
    
        
        $project->users()->attach($usersId); 
    
      
    }

    public function update(Request $request, $projectId){

        $project = Project::find($projectId);
        
        if (!$project) {
            return response()->json(['error' => 'Proyecto no encontrado'], 404);
        }

        $project->update([
            'name' => $request['projectName'],
            'company' => $request['company'],
            'projectLeader' => $request['projectLeader'],
            'priority' => $request['priority'],
            'start_date' => $request['startDate'],
            'end_date' => $request['endDate'],
            'assigned_hours' => $request['assignedHours'],
            'description' => $request['description'],
            'attachments' => $request['attachments'],
            'is_public' => $request['isPublic'],
        ]);

    }

    public function destroy($projectId)
{
    
    $project = Project::find($projectId);

    // Verificar si el proyecto existe
    if (!$project) {
        return redirect()->route('projects.index')->with('error', 'Proyecto no encontrado');
    }

    // Eliminar el proyecto
    $project->delete();

    // Redirigir con mensaje de éxito
    return redirect()->route('projects.index')->with('success', 'Proyecto eliminado con éxito');
}


    

}
