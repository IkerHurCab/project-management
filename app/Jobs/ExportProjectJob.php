<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ProjectManagement\Project;
use App\Models\ProjectManagement\Task;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Illuminate\Support\Facades\Storage;

class ExportProjectJob implements ShouldQueue
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
        $project = Project::with([
            'tasks.user', 
            'tasks.comments', 
            'tasks.logs', 
            'leader', 
            'users', 
            'departments'
        ])->findOrFail($this->projectId);
   

        // Create new spreadsheet
        $spreadsheet = new Spreadsheet();
        
        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('Project Management System')
            ->setLastModifiedBy('Project Management System')
            ->setTitle('Project Report - ' . $project->name)
            ->setSubject('Project Report')
            ->setDescription('Detailed report for project ' . $project->name);

        // Create Project Overview sheet
        $this->createProjectOverviewSheet($spreadsheet, $project);
        
        // Create Task sheets
        $this->createTaskSheets($spreadsheet, $project->tasks);
        
        // Save the spreadsheet to a temporary file
        $writer = new Xlsx($spreadsheet);
        $filename = 'project_report_' . $project->id . '_' . time() . '.xlsx';
        $tempPath = storage_path('app/temp/' . $filename);
        
        // Ensure the directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        
        $writer->save($tempPath);
        
        $storagePath = 'private/exports/' . $filename;
        Storage::put($storagePath, file_get_contents($tempPath));
        
        
        // Clean up temp file
        unlink($tempPath);
        
        // Store the file path for retrieval
        \Cache::put('project_export_' . $this->userId . '_' . $this->projectId, $filename, now()->addDay());
  
        } catch (\Exception $e) {
            dump("Error: " . $e->getMessage());
        }
    }

    /**
     * Create the project overview sheet
     */
    private function createProjectOverviewSheet($spreadsheet, $project)
    {
        // Set active sheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Project Overview');
        
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        
        // Add header
        $sheet->setCellValue('A1', 'PROJECT REPORT');
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Project details
        $sheet->setCellValue('A3', 'Project Name:');
        $sheet->setCellValue('B3', $project->name);
        $sheet->setCellValue('A4', 'Status:');
        $sheet->setCellValue('B4', ucfirst($project->status));
        $sheet->setCellValue('A5', 'Priority:');
        $sheet->setCellValue('B5', $this->getPriorityLabel($project->priority));
        $sheet->setCellValue('A6', 'Start Date:');
        $sheet->setCellValue('B6', $project->start_date);
        $sheet->setCellValue('A7', 'End Date:');
        $sheet->setCellValue('B7', $project->end_date);
        $sheet->setCellValue('A8', 'Assigned Hours:');
        $sheet->setCellValue('B8', $project->assigned_hours);
        $sheet->setCellValue('A9', 'Project Leader:');
        $sheet->setCellValue('B9', $project->leader->name);
        $sheet->setCellValue('A10', 'Description:');
        $sheet->setCellValue('B10', $project->description);
        
        // Style the headers
        $sheet->getStyle('A3:A10')->getFont()->setBold(true);
        
        // Departments
        $sheet->setCellValue('A12', 'DEPARTMENTS');
        $sheet->mergeCells('A12:D12');
        $sheet->getStyle('A12')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $sheet->setCellValue('A13', 'Department Name');
        $sheet->getStyle('A13')->getFont()->setBold(true);
        
        $row = 14;
        foreach ($project->departments as $department) {
            $sheet->setCellValue('A' . $row, $department->name);
            $row++;
        }
        
        // Team members
        $row += 2;
        $sheet->setCellValue('A' . $row, 'TEAM MEMBERS');
        $sheet->mergeCells('A' . $row . ':D' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $row++;
        $sheet->setCellValue('A' . $row, 'Name');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        
        $row++;
        foreach ($project->users as $user) {
            $sheet->setCellValue('A' . $row, $user->name);
            $row++;
        }
        
        // Tasks summary
        $row += 2;
        $sheet->setCellValue('A' . $row, 'TASKS SUMMARY');
        $sheet->mergeCells('A' . $row . ':D' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $row++;
        $sheet->setCellValue('A' . $row, 'Status');
        $sheet->setCellValue('B' . $row, 'Count');
        $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true);
        
        $row++;
        $tasksByStatus = [
            'to_do' => 0,
            'in_progress' => 0,
            'review' => 0,
            'done' => 0
        ];
        
        foreach ($project->tasks as $task) {
            if (isset($tasksByStatus[$task->status])) {
                $tasksByStatus[$task->status]++;
            }
        }
        
        foreach ($tasksByStatus as $status => $count) {
            $sheet->setCellValue('A' . $row, ucfirst(str_replace('_', ' ', $status)));
            $sheet->setCellValue('B' . $row, $count);
            $row++;
        }
        
        // Total tasks
        $sheet->setCellValue('A' . $row, 'Total Tasks:');
        $sheet->setCellValue('B' . $row, count($project->tasks));
        $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true);
        
        // Apply borders to all cells
        $lastRow = $row;
        $sheet->getStyle('A1:D' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    }

    /**
     * Create individual sheets for each task
     */
    private function createTaskSheets($spreadsheet, $tasks)
    {
        foreach ($tasks as $index => $task) {
            // Create a new sheet for each task
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle('Task ' . ($index + 1));
            
            // Set column widths
            $sheet->getColumnDimension('A')->setWidth(20);
            $sheet->getColumnDimension('B')->setWidth(40);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(20);
            
            // Add header
            $sheet->setCellValue('A1', 'TASK DETAILS: ' . $task->name);
            $sheet->mergeCells('A1:D1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            // Task details
            $sheet->setCellValue('A3', 'Task Name:');
            $sheet->setCellValue('B3', $task->name);
            $sheet->setCellValue('A4', 'Status:');
            $sheet->setCellValue('B4', ucfirst($task->status));
            $sheet->setCellValue('A5', 'Priority:');
            $sheet->setCellValue('B5', $this->getPriorityLabel($task->priority));
            $sheet->setCellValue('A6', 'Start Date:');
            $sheet->setCellValue('B6', $task->start_date);
            $sheet->setCellValue('A7', 'End Date:');
            $sheet->setCellValue('B7', $task->end_date);
            $sheet->setCellValue('A8', 'Estimated Hours:');
            $sheet->setCellValue('B8', $task->estimated_hours);
            $sheet->setCellValue('A9', 'Completed Hours:');
            $sheet->setCellValue('B9', $task->completed_hours);
            $sheet->setCellValue('A10', 'Assigned To:');
            $sheet->setCellValue('B10', $task->user ? $task->user->name : 'Unassigned');
            $sheet->setCellValue('A11', 'Description:');
            $sheet->setCellValue('B11', $task->description);
            
            // Style the headers
            $sheet->getStyle('A3:A11')->getFont()->setBold(true);
            
            // Time logs
            $row = 13;
            $sheet->setCellValue('A' . $row, 'TIME LOGS');
            $sheet->mergeCells('A' . $row . ':D' . $row);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $row++;
            $sheet->setCellValue('A' . $row, 'Date');
            $sheet->setCellValue('B' . $row, 'Hours');
            $sheet->setCellValue('C' . $row, 'Description');
            $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
            
            $row++;
            if (count($task->logs) > 0) {
                foreach ($task->logs as $log) {
                    $sheet->setCellValue('A' . $row, $log->log_date);
                    $sheet->setCellValue('B' . $row, $log->log_time);
                    $sheet->setCellValue('C' . $row, $log->description);
                    $row++;
                }
            } else {
                $sheet->setCellValue('A' . $row, 'No time logs available');
                $sheet->mergeCells('A' . $row . ':C' . $row);
                $row++;
            }
            
            // Comments
            $row += 2;
            $sheet->setCellValue('A' . $row, 'COMMENTS');
            $sheet->mergeCells('A' . $row . ':D' . $row);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $row++;
            $sheet->setCellValue('A' . $row, 'User');
            $sheet->setCellValue('B' . $row, 'Date');
            $sheet->setCellValue('C' . $row, 'Comment');
            $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
            
            $row++;
            if (count($task->comments) > 0) {
                foreach ($task->comments as $comment) {
                    $sheet->setCellValue('A' . $row, $comment->user->name);
                    $sheet->setCellValue('B' . $row, $comment->created_at);
                    $sheet->setCellValue('C' . $row, $comment->content);
                    $row++;
                }
            } else {
                $sheet->setCellValue('A' . $row, 'No comments available');
                $sheet->mergeCells('A' . $row . ':C' . $row);
                $row++;
            }
            
            // Apply borders to all cells
            $lastRow = $row;
            $sheet->getStyle('A1:D' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }
    }

    /**
     * Get priority label from priority value
     */
    private function getPriorityLabel($priority)
    {
        $labels = ['Low', 'Medium', 'High', 'Urgent'];
        return $labels[$priority - 1] ?? 'Unknown';
    }
}

