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

        $userDepartmentsQuery = Department::withCount('users')
        ->where('organization_id', request()->user()->currentOrganization()->first()->id)
        ->whereHas('users', function ($query) {
            $query->where('users.id', request()->user()->id);
        });
    

        if ($searchMyDepartments) {
            $userDepartmentsQuery->whereRaw('LOWER(name) like ?', ['%' . strtolower($searchMyDepartments) . '%']);
        }

        $userDepartments = $userDepartmentsQuery->get();

        $otherDepartmentsQuery = Department::withCount('users')
        ->where('organization_id', request()->user()->currentOrganization()->first()->id)
        ->whereDoesntHave('users', function ($query) {
            $query->where('users.id', request()->user()->id);
        });
    

        if ($searchOtherDepartments) {
            $otherDepartmentsQuery->whereRaw('LOWER(name) like ?', ['%' . strtolower($searchOtherDepartments) . '%']);
        }

        $otherDepartments = $otherDepartmentsQuery->get();
        return Inertia::render('Users/Departments', [
            'user_departments' => $userDepartments,
            'users' => User::all(),
            'other_departments' => $otherDepartments,
            'projects_count' => $projectsCountByDepartment
        ]);
    }

    public function store(Request $request){

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department_head' => 'required|exists:users,id',
        ]);

        $department = Department::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'organization_id' => $request->user()->currentOrganization()->first()->id,
        ]);
        // Asignar al usuario actual al departamento
        $department->users()->attach($request->user()->id);

        // Invitar al department_head al departamento
        $departmentHeadId = $request->input('department_head');
        if ($request->user()->id != $departmentHeadId) {
            $department->users()->attach($departmentHeadId);
        }

        \DB::table('department_manager')->updateOrInsert(
            [
                'manager_id' => $departmentHeadId,
                'department_id' => $department->id,
            ],
            []
        );

        $projectsCountByDepartment = Department::withCount(['projects'])->get()->mapWithKeys(function ($department) {
            return [$department->name => $department->projects_count];
        });

        $userDepartments = Department::withCount('users')
            ->whereHas('users', function($query) use ($request) {
                $query->where('users.id', $request->user()->id);
            })
            ->where('organization_id', $request->user()->currentOrganization()->first()->id)
            ->get();

        $otherDepartments = Department::withCount('users')
            ->whereDoesntHave('users', function($query) use ($request) {
                $query->where('users.id', $request->user()->id);
            })
            ->where('organization_id', $request->user()->currentOrganization()->first()->id)
            ->get();

        return Inertia::render('Users/Departments', [
            'users' => User::all(),
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
            'department' => $department,
            'users' => $users->items(),
            'totalUsers' => $department->users()->count(),
            'pagination' => $users,
            'department_managers' => $department->managers()->orderBy('name', 'asc')->get(),
            'projects' => $department->projects()->orderBy('name', 'asc')->get(),
            'filteredAvailableUsers' => $filteredAvailableUsers,
        ]);
    }
    

    public function addUser(Request $request, $id) {
        $department = Department::findOrFail($id);
        $user = User::findOrFail($request->input('user_id'));

        $department->users()->attach($user);

        $search = request()->input('search', '');
        $searchAvailableUsers = request()->input('searchAvailableUsers', '');

        $users = $department->users()
            ->whereRaw('LOWER(name) like ?', ['%' . strtolower($search) . '%'])
            ->orderBy('name', 'asc')
            ->paginate(10);

        $filteredAvailableUsers = User::whereNotIn('id', $department->users()->pluck('id'))
            ->whereRaw('LOWER(name) like ?', ['%' . strtolower($searchAvailableUsers) . '%'])
            ->orderBy('name', 'asc')
            ->get();

        return Inertia::render('Users/SingleDepartment', [
            'department' => $department,
            'users' => $users->items(),
            'totalUsers' => $department->users()->count(),
            'pagination' => $users,
            'department_managers' => $department->managers()->orderBy('name', 'asc')->get(),
            'projects' => $department->projects()->orderBy('name', 'asc')->get(),
            'filteredAvailableUsers' => $filteredAvailableUsers,
        ]);
    }

    public function kickMember(Request $request, Department $department)
{
    // Validate the request
    $validated = $request->validate([
        'user_id' => 'required|exists:users,id',
    ]);

    try {
        // Check if the user is in the department
        $userInDepartment = $department->users()->where('user_id', $validated['user_id'])->exists();
        
        if (!$userInDepartment) {
            return response()->json(['message' => 'User is not a member of this department'], 404);
        }

        // Remove the user from the department
        $department->users()->detach($validated['user_id']);
        return $this->showSingle($department->id);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Failed to remove user from department', 'error' => $e->getMessage()], 500);
    }
}

public function update(Request $request, $id)
{
    $department = Department::findOrFail($id);
    
    // Validate the request
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'department_head_ids' => 'required|array',
        'department_head_ids.*' => 'exists:users,id',
    ]);

    // Update department details
    $department->update([
        'name' => $request->input('name'),
        'description' => $request->input('description'),
    ]);

    // Update department managers
    // First, remove all current managers
    \DB::table('department_manager')->where('department_id', $department->id)->delete();
    
    // Then add the new managers
    foreach ($request->input('department_head_ids') as $managerId) {
        // Make sure the manager is a member of the department
        if (!$department->users()->where('user_id', $managerId)->exists()) {
            $department->users()->attach($managerId);
        }
        
        // Add as department manager
        \DB::table('department_manager')->updateOrInsert(
            [
                'manager_id' => $managerId,
                'department_id' => $department->id,
            ],
            []
        );
    }

    return $this->showSingle($department->id);
}


public function destroy($id)
{
    $department = Department::findOrFail($id);
    
    try {
        \DB::beginTransaction();
        \DB::table('department_manager')->where('department_id', $department->id)->delete();
        $department->users()->detach();
        $department->delete();
        \DB::commit();
        return redirect()->route('departments');
    } catch (\Exception $e) {
        \DB::rollBack();
        return response()->json(['message' => 'Failed to delete department', 'error' => $e->getMessage()], 500);
    }
}
    
}