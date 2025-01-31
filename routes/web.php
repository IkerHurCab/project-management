<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');

Route::get('/login',[LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store']);