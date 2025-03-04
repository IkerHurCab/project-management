<?php

namespace App\Http\Controllers\ProjectManagement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\ProjectManagement\ProjectDocumentation;
use App\Models\ProjectManagement\Project;

class ProjectDocumentationController extends Controller
{
    public function show(Request $request){
        return Inertia::render('ProjectManagement/Project/SingleDocumentation', [
               'user' => request()->user(),
        ]);
    }

    public function store(Request $request, $projectId)
    {
        // Verificar si los datos llegan correctamente
        // dd($request->all());
    
        // Buscar el proyecto
        $project = Project::findOrFail($projectId);
    
        // Crear el nuevo documento
        $document = $project->documentation()->create([
            'title' => $request->input('title'),
            'summary' => $request->input('summary'),
            'content' => $request->input('content'),
            'is_public' => $request->input('is_public'),
            'created_by' => auth()->user()->id,
        ]);
    
        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Document created successfully',
        ]);

        return Inertia::location("/projects/$projectId");
    }
    



    public function update(Request $request, $projectId, $documentId)
{
    $project = Project::findOrFail($projectId);
    $document = $project->documentation()->findOrFail($documentId);

    $document->update([
        'title' => $request->input('title'),
        'summary' => $request->input('summary'),
        'content' => $request->input('content'),
        'is_public' => $request->input('is_public'),
    ]);

    return Inertia::location("/projects/$projectId");

    
}
    
public function destroy($projectId, $documentId)
{

    $project = Project::findOrFail($projectId);


    $document = $project->documentation()->findOrFail($documentId);

    // Eliminar el documento
    $document->delete();
 
    return Inertia::location("/projects/$projectId");
}




}
