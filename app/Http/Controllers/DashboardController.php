<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\ProjectManagement\Task;
use App\Models\ProjectManagement\Project;
use App\Models\ProjectManagement\TaskLog;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(){

        $user = auth()->user();

        $tasks = Task::where('user_id', $user->id)->get();
        // Obtener los proyectos del usuario con las relaciones 'tasks' y 'tasks.project'
        $projects = $user->projects()->with(['tasks' => function($query) {
            $query->selectRaw('sum(completed_hours) as total_completed_hours');
        }])->get();
     

        $taskLogs = TaskLog::where('user_id', $user->id)
                    ->whereBetween('log_date', [
                        Carbon::now()->startOfWeek(),  // El inicio de la semana pasada
                        Carbon::now()->endOfWeek()     // El final de la semana pasada
                    ])
                    ->get();
        
                    $allTimeLogs = TaskLog::with(['task', 'task.project']) // Carga la relación task y project de cada task
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
            return Inertia::render('Dashboard/DepartmentHeadDashboard', [
                'user' => $user,
                'tasks' => $tasks,
                'projects' => $projects
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
