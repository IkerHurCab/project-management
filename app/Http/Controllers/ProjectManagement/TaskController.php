<?php

namespace App\Http\Controllers\ProjectManagement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use App\Models\ProjectManagement\Task;
use App\Services\NotificationService;
use App\Models\User;

class TaskController extends Controller
{

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

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
    
        $userId = $task->user_id;
        $relatedTasks = $task->project->tasks()
    ->where('user_id', $userId)  // Filtra por el usuario asignado
    ->where('id', '!=', $task->id)  // Excluye la tarea actual de los resultados
    ->limit(3)  // Limita el número de tareas relacionadas
    ->get();

    $projectLeaderId = $task->project->project_leader_id;

    $isProjectLeader = ($projectLeaderId === $request->user()->id); 

    $user = request()->user();
 
    $projectUsers = $task->project->users;

    $members = $projectUsers->map(function ($user) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];
    });


    

        return Inertia::render('ProjectManagement/Task/SingleTask', [
            'project' => $task->project,
            'task' => $task->toArray(),
            'user' => request()->user(),
            'comments' => $task->comments,
            'taskLogs' => $taskLogs,
            'relatedTasks' => $relatedTasks,
            'isProjectLeader' => $isProjectLeader,
            'employees' => $members,
        
        ]);

    }

    public function update(Request $request, $projectId, $taskId)
    {
       
    
 
        $task = Task::find($taskId);


        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'estimated_hours' => 'nullable|numeric|min:0',
            'user_id' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
       
        ], [
            'name.required' => 'The task name is required',
            'user_id.required' => 'You have to assign the task to a user',
            'end_date.after_or_equal' => 'The end date must be after or equal to the start date',
            'estimated_hours.min' => 'The estimated hours must be a positive number',

            
            
        ]);  

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
                    $originalName = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
        
                    // Crear el nombre del archivo con la extensión correcta
                    $filename = $originalName;
        
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

            $userIdHasChanged = $task->user_id != $request->input('user_id');
          
            $task->update([
                'name' => $request['name'],
                'description' => $request['description'],
                'estimated_hours' => $request['estimated_hours'],
                'priority' => $request['priority'],
                'status' => $request['status'],
                'start_date' => $request['start_date'],
                'end_date' => $request['end_date'],
                'user_id' => $request->input('user_id'),
            ]);
        

            if($userIdHasChanged)
            {
                $user = User::find($request->input('user_id'));
                $this->notificationService->notifyAssignedToTask($user, $task);  
            }
         



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
    $user = User::find($task->user_id);

    $this->notificationService->notifyTaskStatusChanged($user, $task);  
}

    public function store(Request $request, $projectId)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'estimated_hours' => 'nullable|numeric|min:0',
            'status' => 'required|string',
            'priority' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'user_id' => 'required|integer',
            'attachments' => 'nullable|array',
          
        ], [
            'name.required' => 'The task name is required',
            'user_id.required' => 'You have to assign the task to a user',
            'end_date.after_or_equal' => 'The end date must be after or equal to the start date',
            'estimated_hours.min' => 'The estimated hours must be a positive number',
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
        $user = User::find($validated['user_id']);
        $this->notificationService->notifyAssignedToTask($user, $task);  
 



    }

    public function destroy($projectId, $taskId)
{
    $task = Task::find($taskId);
    
    if (!$task) {
        return redirect()->route('projects.show', ['projectId' => $projectId])->with('error', 'Task not found');
    }

    $task->delete();

    return redirect()->route('projects.show', ['projectId' => $projectId]);

}


    public function downloadAttachment($projectId, $taskId, $attachmentIndex)
{
 
    $task = Task::find($taskId);
    $attachments = json_decode($task->attachments);

    if (!$attachments) {
        return redirect()->route('tasks.show', ['projectId' => $projectId, 'taskId' => $taskId])->with('error', 'Attachment not found');
    }

    $attachment = $attachments[$attachmentIndex];
    
    return Storage::download($attachment);

}



}
