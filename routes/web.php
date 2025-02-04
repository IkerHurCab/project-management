<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HeaderController;


Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return Inertia::render('Home', [
            'user' => request()->user(),
        ]);
    })->name('home');


    Route::post('/update-status', [HeaderController::class, 'changeStatus']);

});

Route::get('/login',[LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store']);