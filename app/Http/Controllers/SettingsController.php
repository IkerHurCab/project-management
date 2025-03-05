<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index() {
        return Inertia::render('Settings');
    }

    public function changePassword() {
        return Inertia::render('ChangePassword');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ]);
    
        // Check if current password matches
        if (!Hash::check($request->current_password, auth()->user()->password)) {
            // For Inertia requests, we need to return a JSON response with a 422 status code
            return response()->json([
                'errors' => [
                    'current_password' => ['The provided password does not match your current password.']
                ]
            ], 422);
        }
    
        // Update the password
        $user = auth()->user();
        $user->password = Hash::make($request->password);
        $user->save();
    
        // Return a successful response for Inertia
        return response()->json([
            'message' => 'Password updated successfully'
        ]);
    }
}
