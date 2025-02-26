<?php

namespace App\Http\Controllers\ProjectManagement;

use Illuminate\Http\Request;
use App\Models\ProjectManagement\TaskLog;
use App\Http\Controllers\Controller;
use App\Models\ProjectManagement\Task;
use App\Models\ProjectManagement\Project;

class TaskLogController extends Controller
{
    public function store(Request $request, $projectId, $taskId) {
        
            $validated = $request->validate([
                'hours' => 'required|numeric|min:0',
            'description' => 'required|string',
            'date' => 'required|date',
            ]);


            $task = Task::findOrFail($taskId);

            $taskLog = TaskLog::create([
                'task_id' => $task->id,
                'user_id' => auth()->id(),
                'log_time' => $validated['hours'],
                'description' => $validated['description'],
                'log_date' => $validated['date'],
            ]);

            $task->update([
                'completed_hours' => $task->completed_hours + $validated['hours']
            ]);
        
            


    }
    
}
