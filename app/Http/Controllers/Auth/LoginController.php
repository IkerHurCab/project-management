<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function create()
    {
        return Inertia::render('Auth/Login');
    }

    public function store(Request $request) {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
           
            return Inertia::render('Login', [
                
                'errors' => $validator->errors(),
            ]);
        }

        if (Auth::attempt([
            'email' => $request->email, 
            'password' => $request->password
        ])) {
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }
        
      
        return Inertia::render('Auth/Login', [
            'errors' => 'The provided credentials do not match our records.',
        ]);


    }

    public function destroy(Request $request) {
        Auth::logout();
    
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    
        return redirect('/login');
    }
    
}
