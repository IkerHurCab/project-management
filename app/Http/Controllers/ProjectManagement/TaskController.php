<?php

namespace App\Http\Controllers\ProjectManagement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use App\Models\ProjectManagement\Task;
use App\Models\ProjectManagement\Project;
use Illuminate\Support\Facades\Http;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;




class TaskController extends Controller
{
    
    public function show(Request $request, $projectId, $taskId)
    {
        
        $task = Task::where('id', $taskId)
                    ->where('project_id', $projectId)
                    ->with('project')
                    ->first();
                    $task->load(['user']);
                 
        $task->load(['comments.user']);

        if(!$task)
        {
            abort(404, 'Task not found'); 
        }   
        $taskLogs = $task->logs()->orderBy('log_date')->get();
    

        $relatedTasks = $task->project->tasks()->limit(3)->get();

        return Inertia::render('ProjectManagement/Task/SingleTask', [
            'project' => $task->project,
            'task' => $task->toArray(),
            'user' => request()->user(),
            'comments' => $task->comments,
            'taskLogs' => $taskLogs,
            'relatedTasks' => $relatedTasks,
        
        ]);

    }

    public function update(Request $request, $projectId, $taskId)
    {
       
      
 
        $task = Task::find($taskId);

        if ($request->hasFile('attachments')) {
            $files = $request->file('attachments');
            if(!is_array($files))
            {
                $files = [$files];
            }

            $folderPath = 'projects' . DIRECTORY_SEPARATOR . $task->project->name .DIRECTORY_SEPARATOR  . 'attachments';

            Storage::makeDirectory($folderPath);

            $currentAttachments = $task->attachments ? json_decode($task->attachments) : [];


            foreach ($files as $file) {
            
                if ($file->isValid()) {
                    // Obtener la extensión original
                    $extension = $file->getClientOriginalExtension();
        
                    // Crear el nombre del archivo con la extensión correcta
                    $filename = uniqid() . '.' . $extension;
        
                    // Almacenar el archivo con el nombre y la extensión correcta
                    $path = $file->storeAs($folderPath, $filename, 'local');
        
                    // Reemplazar las barras invertidas con barras normales
                    $path = str_replace('\\', '/', $path);
        
                    $currentAttachments[] = $path;
                }
            }
            

            $task->attachments = json_encode($currentAttachments);   
            $task->save();

        } else {

            $task->update([
                'name' => $request['name'],
                'description' => $request['description'],
                'estimated_hours' => $request['estimated_hours'],
                'priority' => $request['priority'],
                'status' => $request['status'],
                'start_date' => $request['start_date'],
                'end_date' => $request['end_date'],
            ]);

        }
       
    

       
    }
    

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

 

        return Inertia::render('ProjectManagement/Project/SingleProject', [
            'tasks' => $task,
        ]);


    }

    public function destroy($projectId, $taskId)
{
    $task = Task::find($taskId);
    
    if (!$task) {
        return redirect()->route('projects.show', ['id' => $projectId])->with('error', 'Task not found');
    }

    $task->delete();

    return redirect()->route('projects.show', ['projects' => $projectId]);

}

public function getPendingTasksByProject($projectId)
    {
  
        $tasks = Task::where('project_id', $projectId)
        ->where('status', 'to_do')
        ->get();

$tasksList = $tasks->map(function($task) {
return [
   'name' => $task->name,
   'priority' => $task->priority,
   'start_date' => Carbon::parse($task->start_date),  
   'end_date' => Carbon::parse($task->end_date),     
   'description' => $task->description,
];
})->sortBy([
['priority', 'asc'], 
['start_date', 'asc'],
['end_date', 'asc'],   
]);


    $tasksList = $tasksList->map(function($task) {
        return $task['name'] . ' (Priority: ' . $task['priority'] . ', Start: ' . $task['start_date']->format('Y-m-d') . ', End: ' . $task['end_date']->format('Y-m-d') . ', Description: ' . $task['description'] . ')';
    })->implode(', ');
   

           
            $result = Gemini::geminiPro()->generateContent("Here is a list of pending tasks: $tasksList. Please categorize each task according to its priority: 'Urgent', 'High', 'Medium', or 'Low'. 
            For each task, provide the title followed by the priority label and a brief explanation of why it was classified in that category. Each task should be presented in the following format, with each task on a new line:
            
            - Task Title -> Priority Label: Explanation.
            
            For example:
            - Organize Workspace -> Urgent: This task has a high priority due to its impact on productivity.
            - Research New Technologies -> High: This task is important but not as urgent.
            
            Please provide the list of tasks as shown above.
 
            ");
            $text = $result->text();
            $recommendationsArray = explode("\n", $text);
            
            $recomendationsArray =  array_filter($recommendationsArray, function($line){
                return !empty(trim($line));
            });

            $recomendationsArray = array_values($recommendationsArray);
           
  
        $project = Project::find($projectId);
        $activeTab = 'tasks';
        $recommendationData = $text;
        
        return redirect()->route('projects.show', ['projects' => $projectId])
        ->with([
            'activeTab' => $activeTab,
            'recommendationData' => $recommendationsArray,
        ]);
       
        
    }




}
    