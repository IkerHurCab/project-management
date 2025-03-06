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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

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
            // Load project with all related data
            $project = Project::with([
                'tasks.user', 
                'tasks.comments', 
                'tasks.logs', 
                'leader', 
                'users', 
                'departments'
            ])->findOrFail($this->projectId);

            // Calcular tareas completadas y total
            $totalTasks = $project->tasks->count();
            $completedTasks = $project->tasks->where('status', 'done')->count();
            
            // Agrupar tareas por estado
            $tasksByStatus = $this->getTasksByStatus($project->tasks);

         
          

            // Generate PDF
            $pdf = PDF::loadView('pdf.project-report', [
                'project' => $project,
                'tasksByStatus' => $tasksByStatus,
                'totalTasks' => $totalTasks,
                'completedTasks' => $completedTasks,
              
                'statusClass' => $this->getStatusClass($project->status),
                'statusLabel' => $this->getStatusLabel($project->status),
                'priorityClass' => $this->getPriorityClass($project->priority),
                'priorityLabel' => $this->getPriorityLabel($project->priority)
            ]);
            
            // Set PDF options for better quality
            $pdf->setPaper('a4', 'portrait');
            $pdf->setOptions([
                'dpi' => 150,
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true
            ]);
            
            // Nombre del archivo único
            $filename = 'project_report_' . $project->id . '_' . time() . '.pdf';

            // Guardar el PDF en caché (60 minutos)
            Cache::put('export_pdf_' . $this->userId . '_' . $this->projectId, [
                'filename' => $filename,
                'content' => base64_encode($pdf->output()) // Guardar en base64 para evitar problemas binarios
            ], now()->addMinutes(60));
            
            Log::info("📤 Exporting PDF for filename " . $filename);
            
           
        } catch (\Exception $e) {
            Log::error("❌ Error exporting PDF for project ID: " . $this->projectId);
            Log::error($e->getMessage());
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
    
    /**
     * Get status class from status value
     */
    private function getStatusClass($status)
    {
        $classes = [
            'to_do' => 'badge-todo',
            'in_progress' => 'badge-progress',
            'review' => 'badge-review',
            'done' => 'badge-done'
        ];
        
        return $classes[$status] ?? 'badge-todo';
    }
    
    /**
     * Get status label from status value
     */
    private function getStatusLabel($status)
    {
        $labels = [
            'to_do' => 'To Do',
            'in_progress' => 'In Progress',
            'review' => 'Review',
            'done' => 'Done'
        ];
        
        return $labels[$status] ?? 'Unknown';
    }
    
    /**
     * Get priority class from priority value
     */
    private function getPriorityClass($priority)
    {
        $classes = [
            1 => 'priority-low',
            2 => 'priority-medium',
            3 => 'priority-high',
            4 => 'priority-urgent'
        ];
        
        return $classes[$priority] ?? 'priority-medium';
    }
    
    /**
     * Get priority label from priority value
     */
    private function getPriorityLabel($priority)
    {
        $labels = [
            1 => 'Low',
            2 => 'Medium',
            3 => 'High',
            4 => 'Urgent'
        ];
        
        return $labels[$priority] ?? 'Unknown';
    }
    
}