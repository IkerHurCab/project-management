<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HeaderController extends Controller
{
    public function changeStatus(Request $request) {
        $user = auth()->user(); 
        $user->status = $request->status; 
        $user->save(); 
    }
}
