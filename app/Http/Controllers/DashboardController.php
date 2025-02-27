<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\ProjectManagement\Task;
use App\Models\ProjectManagement\Project;

class DashboardController extends Controller
{
    public function index(){

        $user = auth()->user();

        $tasks = Task::where('user_id', $user->id)->get();
        $projects = $user->projects()->get();

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
                'myProjects' => $projects
            ]);
        }

        return redirect('/projects');

    }
}
