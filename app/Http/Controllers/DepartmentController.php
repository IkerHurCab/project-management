<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\Users\Department;
use App\Models\ProjectManagement\Project;

class DepartmentController extends Controller
{
    public function show(){
        
        return Inertia::render('Users/Departments', [
            'user' => request()->user(),
            'user_departments' => Department::withCount('users')->whereHas('users', function($query) {
                $query->where('users.id', request()->user()->id);
            })->get(),
            'other_departments' => Department::withCount('users')->whereDoesntHave('users', function($query) {
                $query->where('users.id', request()->user()->id);
            })->get(),

        ]);
    }

    public function showSingle($id, $search = ''){

        $department = Department::find($id);
        $search = request()->input('search', $search);
        $users = $department->users()->whereRaw('LOWER(name) like ?', ['%' . strtolower($search) . '%'])->orderBy('name', 'asc')->paginate(10);

        return Inertia::render('Users/SingleDepartment', [
            'user' => request()->user(),
            'department' => $department,
            'users' => $users->items(),
            'pagination' => $users,
            'department_managers' => $department->managers()->orderBy('name', 'asc')->get(),
            'projects' => $department->projects()->orderBy('name', 'asc')->get(),
        ]);
    }
}
