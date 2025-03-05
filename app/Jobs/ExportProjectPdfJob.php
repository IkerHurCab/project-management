<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ProjectManagement\Project;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class ExportProjectPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $projectId;
    protected $userId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($projectId, $userId)
    {
        $this->projectId = $projectId;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
{
    try {
        // Cargar el proyecto con los datos relacionados
        $project = Project::with([
            'tasks.user', 
            'tasks.comments', 
            'tasks.logs', 
            'leader', 
            'users', 
            'departments'
        ])->findOrFail($this->projectId);

        // Preparar las variables de clase y etiqueta para cada tarea
        $tasksWithStatus = $project->tasks->map(function ($task) {
            $task->statusClass = $this->getStatusClass($task->status);
            $task->statusLabel = $this->getStatusLabel($task->status);
            return $task;
        });
        $statusClass = $this->getStatusClass($project->status); // Asumiendo que el proyecto tiene un campo 'status'
        $statusLabel = $this->getStatusLabel($project->status);
        $priorityClass = $this->getPriorityClass($project->priority); // Asumiendo que 'priority' es un número entre 1 y 4
        $priorityLabel = $this->getPriorityLabel($project->priority);
    


        $pdf = PDF::loadView('pdf.project-report', [
            'project' => $project,
            'tasks' => $tasksWithStatus,  // Pasamos las tareas con las nuevas variables
            'tasksByStatus' => $this->getTasksByStatus($project->tasks),
            'statusClass' => $statusClass,
            'statusLabel' => $statusLabel,
            'priorityClass' => $priorityClass,
            'priorityLabel' => $priorityLabel,
            'totalTasks' => count($project->tasks),
            'completedTasks' => $project->tasks->where('status', 'done')->count(),
        ]);
        
        // Configurar opciones del PDF
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOptions([
            'dpi' => 150,
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true
        ]);
        
        // Generar el nombre del archivo
        $filename = 'project_report_' . $project->id . '_' . time() . '.pdf';
        
        // Guardar el PDF en almacenamiento
        $storagePath = 'private/exports/' . $filename;
        Storage::put($storagePath, $pdf->output());
        
        // Guardar la ruta del archivo para su recuperación
        \Cache::put('project_export_' . $this->userId . '_' . $this->projectId, $filename, now()->addDay());
    } catch (\Exception $e) {
        dump("Error exporting project: " . $e->getMessage());
    }
}


    /**
     * Group tasks by status
     */
    private function getTasksByStatus($tasks)
    {
        return [
            'to_do' => $tasks->where('status', 'to_do')->values(),
            'in_progress' => $tasks->where('status', 'in_progress')->values(),
            'review' => $tasks->where('status', 'review')->values(),
            'done' => $tasks->where('status', 'done')->values()
        ];
    }

    private function getStatusClass($status)
{
    switch ($status) {
        case 'to_do':
            return 'badge-to-do';
        case 'in_progress':
            return 'badge-in-progress';
        case 'review':
            return 'badge-review';
        case 'done':
            return 'badge-done';
        default:
            return 'badge-default';
    }
}

private function getStatusLabel($status)
{
    switch ($status) {
        case 'to_do':
            return 'To Do';
        case 'in_progress':
            return 'In Progress';
        case 'review':
            return 'In Review';
        case 'done':
            return 'Done';
        default:
            return 'Unknown';
    }
}


// Función para obtener la clase CSS según el valor de la prioridad
private function getPriorityClass($priority)
{
    $colors = [
        'bg-green-500', // Prioridad 1: Baja
        'bg-yellow-500', // Prioridad 2: Media
        'bg-orange-500', // Prioridad 3: Alta
        'bg-red-500' // Prioridad 4: Urgente
    ];

    return $colors[$priority - 1] ?? 'bg-gray-500'; // Devuelve la clase o gris si el valor no es válido
}

// Función para obtener la etiqueta de la prioridad según el valor
private function getPriorityLabel($priority)
{
    $labels = [
        'Low', // Prioridad 1: Baja
        'Medium', // Prioridad 2: Media
        'High', // Prioridad 3: Alta
        'Urgent' // Prioridad 4: Urgente
    ];

    return $labels[$priority - 1] ?? 'Unknown'; // Devuelve la etiqueta o "Unknown" si el valor no es válido
}


}
