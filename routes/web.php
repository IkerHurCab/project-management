<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProjectManagement\ProjectController;

Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');



Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    Route::get('/projects', function () {
        return Inertia::render('ProjectManagement/Projects');
    })->name('projects');
    Route::get('logout', function () {
        Auth::logout();
        return redirect()->route('login');
    })->name('logout');
    Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('projects/{projects}',  [ProjectController::class, 'show'])->name('projects.show');  
});
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});
