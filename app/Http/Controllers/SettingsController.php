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

    public function updateUser(Request $request)
{
    $user = auth()->user();
    
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        'allow_emails' => ['boolean'],
    ]);
    
    // Update user information
    $user->name = $validated['name'];
    
    // Check if email has changed
    if ($user->email !== $validated['email']) {
        $user->email = $validated['email'];
        // If you're using email verification, you might want to mark the email as unverified
        // $user->email_verified_at = null;
    }
    
    // Update email preferences if the field exists
    // if (isset($validated['allow_emails'])) {
    //     $user->allow_emails = $validated['allow_emails'];
    // }
    
    $user->save();
    
    // Return appropriate response based on request type
    if ($request->wantsJson()) {
        return response()->json([
            'message' => 'User information updated successfully',
            'user' => $user
        ]);
    }
    
    return back()->with('success', 'User information updated successfully');
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
