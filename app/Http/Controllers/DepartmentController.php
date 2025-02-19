<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\Users\Department;
use App\Models\ProjectManagement\Project;
use App\Models\User;


class DepartmentController extends Controller
{
    public function show(Request $request){
        $searchMyDepartments = $request->input('searchMyDepartments', '');
        $searchOtherDepartments = $request->input('searchOtherDepartments', '');

        $projectsCountByDepartment = Department::withCount(['projects'])->get()->mapWithKeys(function ($department) {
            return [$department->name => $department->projects_count];
        });

        $userDepartmentsQuery = Department::withCount('users')->whereHas('users', function($query) {
            $query->where('users.id', request()->user()->id);
        });

        if ($searchMyDepartments) {
            $userDepartmentsQuery->whereRaw('LOWER(name) like ?', ['%' . strtolower($searchMyDepartments) . '%']);
        }

        $userDepartments = $userDepartmentsQuery->get();

        $otherDepartmentsQuery = Department::withCount('users')->whereDoesntHave('users', function($query) {
            $query->where('users.id', request()->user()->id);
        });

        if ($searchOtherDepartments) {
            $otherDepartmentsQuery->whereRaw('LOWER(name) like ?', ['%' . strtolower($searchOtherDepartments) . '%']);
        }

        $otherDepartments = $otherDepartmentsQuery->get();
        return Inertia::render('Users/Departments', [
            'user' => request()->user(),
            'user_departments' => $userDepartments,
            'other_departments' => $otherDepartments,
            'projects_count' => $projectsCountByDepartment
        ]);
    }

    public function showSingle($id){
        $department = Department::findOrFail($id);
        
        $search = request()->input('search', '');
        $searchAvailableUsers = request()->input('searchAvailableUsers', '');
        
        // Filtrar usuarios del departamento
        $users = $department->users()
            ->whereRaw('LOWER(name) like ?', ['%' . strtolower($search) . '%'])
            ->orderBy('name', 'asc')
            ->paginate(10);
    
        // Filtrar usuarios disponibles para agregar (esto depende de tu lógica)
        $filteredAvailableUsers = User::whereNotIn('id', $department->users()->pluck('id'))
            ->whereRaw('LOWER(name) like ?', ['%' . strtolower($searchAvailableUsers) . '%'])
            ->orderBy('name', 'asc')
            ->get();
    
        return Inertia::render('Users/SingleDepartment', [
            'user' => request()->user(),
            'department' => $department,
            'users' => $users->items(),
            'totalUsers' => $department->users()->count(),
            'pagination' => $users,
            'department_managers' => $department->managers()->orderBy('name', 'asc')->get(),
            'projects' => $department->projects()->orderBy('name', 'asc')->get(),
            'filteredAvailableUsers' => $filteredAvailableUsers,
        ]);
    }
    
}
