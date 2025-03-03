<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Users\Department;

class HomeController extends Controller
{
    public function show(Request $request) {

        $userDepartments = Department::withCount('users')
        ->whereHas('users', function($query) use ($request) {
            $query->where('users.id', $request->user()->id);
        })->get();

        $organization_departments = $request->user()->currentOrganization()->first()->departments()->get();
        return Inertia::render('Home', [
            'organization_departments' => $organization_departments,
            'user_departments' => $userDepartments
        ]);
    }
}
