<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\Department;

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

    public function showSingle($id){
        $department = Department::find($id);
        return Inertia::render('Users/SingleDepartment', [
            'user' => request()->user(),
            'department' => $department,
            'users' => $department->users()->orderBy('name', 'asc')->get(),
        ]);
    }
}
