<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Users\Department;

class CheckDepartmentAccess
{
    public function handle(Request $request, Closure $next)
    {
        $departmentId = $request->route('id');

        if (!is_numeric($departmentId)) {
            return redirect()->route('departments');
        }

        $user = Auth::user();
        $currentOrg = $user->currentOrganization()->first();

        if (!$currentOrg) {
            return redirect()->route('departments');
        }

        $hasAccess = Department::where('id', $departmentId)
            ->where('organization_id', $currentOrg->id)
            ->whereHas('users', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->exists();

        if (!$hasAccess) {
            return redirect()->route('departments');
        }

        return $next($request);
    }
}
