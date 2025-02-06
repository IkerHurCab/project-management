<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Department;

class CheckDepartmentAccess
{
    public function handle(Request $request, Closure $next)
    {
        $departmentId = $request->route('id');

        if (!is_numeric($departmentId)) {
            return redirect()->route('departments');
        }
        $user = Auth::user();

        $hasAccess = Department::where('id', $departmentId)
            ->whereHas('users', function($query) use ($user) {
                $query->where('users.id', $user->id);
            })->exists();

        if (!$hasAccess) {
            return redirect()->route('departments');
        }

        return $next($request);
    }
}
