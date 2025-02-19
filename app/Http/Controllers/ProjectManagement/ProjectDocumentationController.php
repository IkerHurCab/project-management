<?php

namespace App\Http\Controllers\ProjectManagement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\ProjectManagement\ProjectDocumentation;

class ProjectDocumentationController extends Controller
{
    public function show(Request $request, $documentId){

      

        return Inertia::render('ProjectManagement/Project/SingleDocumentation', [
               'user' => request()->user(),
        ]);
    }

}
