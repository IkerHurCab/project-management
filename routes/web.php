<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProjectManagement\ProjectController;
use App\Http\Controllers\HeaderController;
use App\Http\Controllers\ProjectManagement\TaskController;
use App\Http\Controllers\ProjectManagement\CommentController;



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
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    
    Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('projects/{projects}',  [ProjectController::class, 'show'])->name('projects.show');  
    Route::post('projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('projects/{projectId}/search-member', [ProjectController::class, 'searchMember'])->name('projects.search-member');

    Route::post('projects/{projectId}/tasks/{taskId}/update-status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');
    Route::post('projects/{projectId}/tasks/', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('projects/{projectId}/task/{taskId}', [TaskController::class, 'show'])->name('tasks.show');
    Route::post('projects/{projectId}/task/{taskId}', [TaskController::class, 'update'])->name('tasks.update');

    Route::post('projects/{projectId}/task/{taskId}/comment', [CommentController::class, 'store'])->name('comment.store');

    Route::get('logout', function () {
        Auth::logout();
        return redirect()->route('login');
    })->name('logout');
});
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::get('logout' , function() {
    Auth::logout(); 

    return redirect('/login'); 
});