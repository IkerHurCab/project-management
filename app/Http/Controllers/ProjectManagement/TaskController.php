<?php

namespace App\Http\Controllers\ProjectManagement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\ProjectManagement\Task;

class TaskController extends Controller
{

   
    public function updateStatus(Request $request, $projectId, $taskId)
{
    $validated = $request->validate([
        'status' => ['required', 'in:to_do,in_progress,review,done'],
    ]);

    $task = Task::find($taskId);
    
    if($task){
        $task->status = $request->status;
        $task->save();
    }

   

    // Devuelves solo los datos necesarios para actualizar el frontend
    return Inertia::render('ProjectManagement/SingleProject', [
        'project' => $projectId,
        'tasks' => $task,  // Actualiza solo las tareas para 'TaskList'
    ]);
}



}
