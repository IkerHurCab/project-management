<?php
namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Users\Organization;
use Inertia\Inertia;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;



class OrganizationController extends Controller
{
    public function changeOrganization(Request $request)
    {
        $user = Auth::user();
        $organization = Organization::find($request->organization_id);
    
        if ($organization && $user->organizations->contains($organization)) {
            // Desmarcar todas las organizaciones actuales del usuario
            DB::table('organization_user')
                ->where('user_id', $user->id)
                ->update(['is_current' => false]);
    
            // Marcar la nueva organización como actual
            DB::table('organization_user')
                ->where('user_id', $user->id)
                ->where('organization_id', $organization->id)
                ->update(['is_current' => true]);
    
            return redirect()->back()->with([
                'success' => 'Organización cambiada correctamente',
                'auth' => [
                    'current_organization' => $organization
                ]
            ]);
        }
    
        return redirect()->back()->withErrors(['error' => 'No tienes acceso a esta organización']);
    }

    public function openCreateMenu(){
        return Inertia::render('Users/CreateOrganization', 
        [
            'users' => User::all(),
        ]);
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:20',
            'description' => 'required|string|max:100',
            'organization_head' => 'required|exists:users,id',
            'organization_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        $logoPath = null;
        if ($request->hasFile('organization_logo')) {
            $logoPath = $request->file('organization_logo');
            $image = ImageManager::imagick()->read($logoPath);
            $image->resize(250, 250);
            $image = (string) $image->toWebp();
            $filename = 'logos/' . uniqid() . '.webp';
            Storage::disk('public')->put($filename, $image);
            $logoPath = $filename;
        }

        $organization = Organization::create([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'organization_head' => $validatedData['organization_head'],
            'created_at' => NOW(),
            'updated_at' => NOW(),
            'organization_logo' => $logoPath, 
        ]);

        $userId = Auth::id();
        $organizationHeadId = $validatedData['organization_head'];

        // Eliminar cualquier organización actual del usuario creador
        DB::table('organization_user')
            ->where('user_id', $userId)
            ->where ('is_current', true)
            ->update(['is_current' => false]);
    
        // Añadir el creador con is_current = true
        DB::table('organization_user')->insert([
            'organization_id' => $organization->id,
            'user_id' => $userId,
            'is_current' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    
        // Si el organization_head es diferente al creador, añadirlo con is_current = false
        if ($organizationHeadId != $userId) {
            DB::table('organization_user')->insert([
                'organization_id' => $organization->id,
                'user_id' => $organizationHeadId,
                'is_current' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        

        // Redirigir con mensaje de éxito
        return redirect()->back()->with('success', 'Organization created successfully!');
    }
}
