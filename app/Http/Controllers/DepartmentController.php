<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    public function show(){
        return Inertia::render('Users/Departments', [
            'user' => request()->user(),
        ]);
    }
}
