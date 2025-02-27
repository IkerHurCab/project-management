<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProjectManagement\ProjectController;
use App\Http\Controllers\HeaderController;
use App\Http\Controllers\ProjectManagement\TaskController;
use App\Http\Controllers\ProjectManagement\TaskLogController;
use App\Http\Controllers\ProjectManagement\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectManagement\ProjectDocumentationController;
use App\Http\Controllers\DepartmentController;
use App\Http\Middleware\CheckDepartmentAccess;



Route::middleware(['auth'])->group(function () {
   
    Route::post('/update-status', [HeaderController::class, 'changeStatus']);

});



Route::group(['middleware' => 'auth'], function () {
   


    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    
    Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('projects/{projects}',  [ProjectController::class, 'show'])->name('projects.show');  
    Route::put('projects/{projectId}', [ProjectController::class, 'update'])->name('project.update');
    Route::delete('projects/{projectId}', [ProjectController::class, 'destroy'])->name('project.destroy');
    
    Route::post('projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('projects/{projectId}/search-member', [ProjectController::class, 'searchMember'])->name('projects.search-member');
    Route::post('projects/{projectId}/new-members', [ProjectController::class, 'storeMember'])->name('projects.new-member');
    Route::delete('/projects/{project}/members/{user}', [ProjectController::class, 'removeMember']);

    Route::post('projects/{projectId}/documentation', [ProjectDocumentationController::class, 'store'])->name('documentation.store');;
    Route::put('projects/{projectId}/documentation/{documentationId}', [ProjectDocumentationController::class, 'update'])->name('documentation.update');
    Route::delete('/projects/{projectId}/documentation/{documentId}', [ProjectDocumentationController::class, 'docoumentation.destroy']);

    Route::get('/departments', [DepartmentController::class, 'show'])->name('departments');
    Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
    
    Route::get('/departments/{id}', [DepartmentController::class, 'showSingle'])->name('departments.showSingle')
    ->middleware(CheckDepartmentAccess::class);

    Route::post('/departments/{id}/addUser', [DepartmentController::class, 'addUser']);

    
  
    
    Route::post('projects/{projectId}/tasks/{taskId}/update-status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');
    
    Route::post('projects/{projectId}/tasks/', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('projects/{projectId}/task/{taskId}', [TaskController::class, 'show'])->name('tasks.show');
    Route::post('projects/{projectId}/task/{taskId}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('projects/{projectId}/task/{taskId}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    Route::post('projects/{projectId}/task/{taskId}/task-log', [TaskLogController::class, 'store'])->name('task-log.store');

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