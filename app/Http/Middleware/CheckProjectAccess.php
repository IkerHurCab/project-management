<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProjectManagement\Project;

class CheckProjectAccess
{
    public function handle(Request $request, Closure $next)
    {
        $projectId = $request->route('projects');


        $user = $request->user();
        $currentOrg = $user->currentOrganization()->first();

        if (!$currentOrg) {
            return redirect()->route('projects.index');
        }
        $project = Project::find($projectId);
                
        $hasAccess = Project::where('project.id', $projectId)
        ->whereHas('departments', function ($query) use ($currentOrg) {
            $query->where('department.organization_id', $currentOrg->id);
        })
        ->where(function ($query) use ($user) {
            $query->where('project.project_leader_id', $user->id)
                  ->orWhereHas('users', function ($query) use ($user) {
                      $query->where('users.id', $user->id);
                  });
        })
        ->exists();
    
    
    

        if (!$hasAccess) {
            return redirect()->route('projects.index');
        }

        return $next($request);
    }
}
