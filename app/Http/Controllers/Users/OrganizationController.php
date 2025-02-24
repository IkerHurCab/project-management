<?php
namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Users\Organization;
use Inertia\Inertia;

class OrganizationController extends Controller
{
    public function changeOrganization(Request $request)
    {
        $user = Auth::user();
        $organization = Organization::find($request->organization_id);

        if ($organization && $user->organizations->contains($organization)) {
            // Actualiza la organización actual
            $user->organizations()->updateExistingPivot($organization->id, ['is_current' => true]);
            
            // Desmarcar las demás
            $user->organizations()->where('id', '!=', $organization->id)
                ->update(['is_current' => false]);
        }

        return redirect()->back();
    }
}
