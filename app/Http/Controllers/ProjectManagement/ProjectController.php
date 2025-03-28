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
use Illuminate\Support\Facades\DB;
use App\Services\NotificationService;



class ProjectController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }



    public function index(Request $request)
    {
        // dd($request->user()->departments()->get());
        $filters = $request->only(['search', 'status', 'user', 'dateRange', 'my_projects']);
        $currentUser = $request->user();
    
        // Obtener la organización actual del usuario
        $currentOrganization = $currentUser->currentOrganization()->first();
    
        if (!$currentOrganization) {
            // Si el usuario no tiene una organización actual, devolver una lista vacía o un error
            return Inertia::render('ProjectManagement/Project/Projects', [
                'projects' => [],
                'filters' => $filters,
                'user' => $currentUser,
                'projectsUrl' => route('projects.index'),
                'departmentHeads' => [],
                'statuses' => [],
                'isAdminOrDepartmentHead' => false,
                'error' => 'No current organization set for the user.'
            ]);
        }
    
        // Obtener los IDs de los departamentos de la organización actual
        $departmentIds = $currentOrganization->departments()->pluck('id')->toArray();
    
        $projects = Project::with('leader:id,name', 'users:id,name', 'departments:id,name')
            ->whereHas('departments', function ($query) use ($departmentIds) {
                $query->whereIn('department.id', $departmentIds);
            })
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
                $query->where(function ($query) use ($currentUser) {
                    $query->whereHas('users', function ($query) use ($currentUser) {
                        $query->where('user_id', $currentUser->id);
                    })
                    ->orWhere('project_leader_id', $currentUser->id);
                });
            })
            ->where(function ($query) use ($currentUser) {
                $query->where('is_public', true)
                    ->orWhereHas('users', function ($query) use ($currentUser) {
                        $query->where('user_id', $currentUser->id);
                    })
                    ->orWhere('project_leader_id', $currentUser->id);
            })
            ->get();
   
     
        $isAdminOrDepartmentHead = $currentUser->hasRole('admin') || $currentUser->hasRole('department_head');
    
        $departmentHeads = User::whereHas('roles', function ($query) {
            $query->where('name', 'department_head');
        })->whereHas('departments', function ($query) use ($departmentIds) {
            $query->whereIn('department.id', $departmentIds);
        })->get(['id', 'name']);
    
        $statuses = Project::whereHas('departments', function ($query) use ($departmentIds) {
            $query->whereIn('department.id', $departmentIds);
        })->distinct()->pluck('status');
    
        return Inertia::render('ProjectManagement/Project/Projects', [
            'projects' => $projects,
            'filters' => $filters,
            'user' => $currentUser,
            'projectsUrl' => route('projects.index'),
            'departmentHeads' => $departmentHeads,
            'statuses' => $statuses,
            'isAdminOrDepartmentHead' => $isAdminOrDepartmentHead
        ]);
    }

    public function show($id, Request $request)
    {
    
        $searchMember = $request->query('searchMember');
         
        $project = Project::with(['tasks', 'users', 'leader', 'departments'])->findOrFail($id);
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

    $isUserInProject = $project->users->contains('id', $user->id) || $isProjectLeader;
    
    $departmentHead = User::role('department_head')
    ->select('id', 'name')
    ->get()
    ->map(function ($user) {
        return [
            'value' => $user->id, 
            'label' => $user->name, 
        ];
    });

    $departmentIds = $project->departments()->pluck('id')->toArray(); 
    
    $allUsers = User::whereHas('departments', function ($query) use ($departmentIds) {
        $query->whereIn('department.id', $departmentIds); // Asegurar el alias correcto
    })
    ->whereNotIn('id', $members->pluck('id')) // Excluir los usuarios ya en el proyecto
    ->get(['id', 'name']);


    
    $departments = $user->departments()
    ->where('organization_id', request()->user()->currentOrganization()->first()->id)
    ->get(['id', 'name']);


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
        'isUserInProject' => $isUserInProject,
        'departmentHead' => $departmentHead,
        'userDepartments' => $departments,
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

        $user = auth()->user();
        $departments = $user->departments()
        ->where('organization_id', request()->user()->currentOrganization()->first()->id)
        ->get(['id', 'name']);

 

        return Inertia::render('ProjectManagement/Project/CreateProject', [
            'users' => $users,
            'departmentHead' => $departmentHead,
            'user' => request()->user(),
            'userDepartments' => $departments,
        ]);
    }


    public function store(Request $request)
    {
    
        $request->validate([
            'name' => 'required|string|max:255',
            'project_leader_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'assigned_hours' => 'required|numeric|min:0',
            'status' => 'required|string',
            'is_public' => 'required|boolean',
            'priority' => 'required|integer',
            'description' => 'nullable|string',
            'attachments' => 'nullable|array',
            'department_ids' => 'required|array',
        ], [
            'name.required' => 'The project name is required',
            'project_leader_id.required' => 'The project leader is required',
            'start_date.required' => 'The start date is required',
            'end_date.required' => 'The end date is required',
            'status.required' => 'The status is required',
            'is_public.required' => 'The public status is required',
            'priority.required' => 'The priority is required',
            'project_leader_id.exists' => 'The project leader does not exist',
            'start_date.date' => 'The start date must be a valid date',
            'end_date.date' => 'The end date must be a valid date',
            'end_date.after_or_equal' => 'The end date must be after or equal to the start date',
            'assigned_hours.numeric' => 'The assigned hours must be a number',
            'attachments.array' => 'The attachments must be an array',
            'department_ids.required' => 'You have to select at least one department',
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

        if (isset($request->department_ids)) {
            $project->departments()->sync($request->department_ids);
        }
       


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

        $this->sendProjectAssignedNotifications($usersId, $project);

    }

    private function sendProjectAssignedNotifications($usersId, $project)
{
    $users = User::whereIn('id', $usersId)->get();

  
    foreach ($users as $user) {
       
        $this->notificationService->notifyAssignedToProject($user, $project);
    }
}


    public function update(Request $request, $projectId){

        $validatedData = $request->validate([
            'projectName' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'projectLeader' => 'required|exists:users,id',
            'startDate' => 'required|date',
            'endDate' => 'nullable|date|after_or_equal:startDate',
            'assignedHours' => 'nullable|numeric',
            'priority' => 'required|integer',
            'description' => 'nullable|string',
            'attachments' => 'nullable|array',
            'isPublic' => 'required|boolean',
            'department_ids' => 'required|array',
        ], [
            'name.required' => 'The project name is required',
            'project_leader_id.required' => 'The project leader is required',
            'start_date.required' => 'The start date is required',
            'end_date.required' => 'The end date is required',
            'department_ids.required' => 'You have to select at least one department',
            'project_leader_id.exists' => 'The project leader does not exist',
            'start_date.date' => 'The start date must be a valid date',
            'end_date.date' => 'The end date must be a valid date',
            'end_date.after_or_equal' => 'The end date must be after or equal to the start date',
            'assigned_hours.numeric' => 'The assigned hours must be a number',
            ]);


        $project = Project::find($projectId);
        
        if (!$project) {
            return response()->json(['error' => 'Proyecto no encontrado'], 404);
        }  
        
   

        $project->update([
            'name' => $request['projectName'],
            'company' => $request['company'],
            'project_leader_id' => $request['projectLeader'],
            'priority' => $request['priority'],
            'start_date' => $request['startDate'],
            'end_date' => $request['endDate'],
            'assigned_hours' => $request['assignedHours'],
            'description' => $request['description'],
            'attachments' => $request['attachments'],
            'is_public' => $request['isPublic'],
            'status' => $request['status'],
        ]);
  
        $project->departments()->sync($request->department_ids);
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

    public function removeMember($projectId, $userId)
    {
        $project = Project::findOrFail($projectId);
       

        $project->users()->detach($userId);
        
      
        return redirect()->back();    
    }
    
    

}
