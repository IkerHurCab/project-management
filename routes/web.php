<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Http\Request;

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return Inertia::render('Home', [
            'user' => request()->user(),
        ]);
    })->name('home');

    //Cambio de estado del usuario (online, away, offline...)
    Route::post('/update-status', function (Request $request) {
        $user = auth()->user(); 
        $user->status = $request->status; 
        $user->save(); 

        return Inertia::render('');
    });

});

Route::get('/login',[LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store']);