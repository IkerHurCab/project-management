<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\ProjectManagement\Task;
use App\Models\ProjectManagement\Project;
use App\Models\User;
use App\Models\ProjectManagement\TaskLog;
use App\Models\Users\Department;

use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(){

        $user = auth()->user();

// Obtener tareas del usuario, con los proyectos asociados
        $tasks = Task::where('user_id', $user->id)->with('project')->get();
       
        $projects = $user->projects()->with(['tasks.project'])->get();

        $taskLogs = TaskLog::where('user_id', $user->id)
                    ->whereBetween('log_date', [
                        Carbon::now()->startOfWeek(),  
                        Carbon::now()->endOfWeek()    
                    ])
                    ->get();
        
        $allTimeLogs = TaskLog::with(['task', 'task.project']) 
                    ->where('user_id', $user->id)
                    ->get();

     
   

                    
        if($user->hasRole('admin')){
            return Inertia::render('Dashboard/AdminDashboard', [
                'user' => $user,
                'tasks' => $tasks,
                'myProjects' => $projects
            ]);
        }   
        else if($user->hasRole('department_head')){
            $leaderProjects = Project::with(['tasks', 'users'])->where('project_leader_id', $user->id)->get();

            $leaderProjectTasks = Task::whereIn('project_id', $leaderProjects->pluck('id'))->get();
            

            $teamMembers = $user->departments()
            ->with('users.projects') 
            ->with('users.tasks')
            ->get()
            ->flatMap(function ($department) {
                return $department->users; 
            });

        
        
    
        
            



            $leaderProjects = $leaderProjects->map(function ($project) {
                $totalTasks = $project->tasks->count();
                $completedTasks = $project->tasks->where('status', 'done')->count();
                $progress = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;
                
                // Agregar el campo 'progress' al proyecto
                $project->progress = $progress;
        
                return $project;
            });
            
            

            return Inertia::render('Dashboard/DepartmentHeadDashboard', [
                'user' => $user,
                'tasks' => $leaderProjectTasks,
                'projects' => $leaderProjects,
                'teamMembers' => $teamMembers,
            ]);
        }
        else if($user->hasRole('employee')){
            return Inertia::render('Dashboard/EmployeeDashboard', [
                'user' => $user,
                'myTasks' => $tasks,
                'myProjects' => $projects,
                'taskLogs' => $taskLogs,
                'allTimeLogs' => $allTimeLogs
            ]);
        }

        return redirect('/projects');

    }
}
