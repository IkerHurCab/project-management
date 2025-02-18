<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HeaderController;
use App\Http\Controllers\DepartmentController;
use App\Http\Middleware\CheckDepartmentAccess;


Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return Inertia::render('Home', [
            'user' => request()->user(),
        ]);
    })->name('home');
    Route::post('/update-status', [HeaderController::class, 'changeStatus']);
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    Route::get('/projects', function () {
        return Inertia::render('ProjectManagement/Projects');
    })->name('projects');
    Route::get('/departments', [DepartmentController::class, 'show'])->name('departments');
    
    Route::get('/departments/{id}', [DepartmentController::class, 'showSingle'])->name('departments.showSingle')
    ->middleware(CheckDepartmentAccess::class);
});

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::get('logout' , function() {
    Auth::logout(); 

    return redirect('/login'); 
});