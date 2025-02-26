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
            // Actualiza la organización actual
            $user->organizations()->updateExistingPivot($organization->id, ['is_current' => true]);
            
            // Desmarcar las demás
            $user->organizations()->where('id', '!=', $organization->id)
                ->update(['is_current' => false]);
        }

        return redirect()->back();
    }

    public function openCreateMenu(){
        return Inertia::render('Users/CreateOrganization', 
        [
            'users' => User::all(),
        ]);
    }

    public function create(Request $request)
    {
        // Validación de los datos
        $validatedData = $request->validate([
            'name' => 'required|string|max:20',
            'description' => 'required|string|max:100',
            'organization_head' => 'required|exists:users,id',
            'organization_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        // Manejo del logo si se ha subido
        $logoPath = null;
        if ($request->hasFile('organization_logo')) {
            $logoPath = $request->file('organization_logo');//->store('logos', 'public');
            $image = ImageManager::imagick()->read($logoPath);
            $image->resize(150, 150);
            $image = (string) $image->toPng();
            $filename = 'logos/' . uniqid() . '.png';
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
