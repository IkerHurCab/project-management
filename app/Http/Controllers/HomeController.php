<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Users\Department;
use Exception;

class HomeController extends Controller
{
    public function show(Request $request) {

        $userDepartments = Department::withCount('users')
        ->whereHas('users', function($query) use ($request) {
            $query->where('users.id', $request->user()->id);
        })->get();

        try {
            $organization = $request->user()->currentOrganization()->first();
        
            if (!$organization) {
                throw new Exception("No current organization found.");
            }
        
            $organization_departments = $organization->departments()->get();
        } catch (Exception $e) {
            $organization_departments = collect();
        }     
           return Inertia::render('Home', [
            'organization_departments' => $organization_departments,
            'user_departments' => $userDepartments
        ]);
    }
}
