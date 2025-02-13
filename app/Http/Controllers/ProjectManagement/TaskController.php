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
}

    public function store(Request $request, $projectId)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'estimated_hours' => 'nullable|numeric',
            'status' => 'required|string',
            'priority' => 'required|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'user_id' => 'nullable|integer',
            'attachments' => 'nullable|array',
          
        ]);
        $task = Task::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? '',
            'estimated_hours' => $validated['estimated_hours'] ?? 0,
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'project_id' => $projectId,  
            'user_id' => $validated['user_id'],
            'attachments' => json_encode($validated['attachments']), 
        ]);

 

        return Inertia::render('ProjectManagement/SingleProject', [
            'tasks' => $task,
        ]);


    }



}
