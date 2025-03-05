<?php

namespace App\Http\Controllers\ProjectManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jobs\ExportProjectPdfJob;
use App\Models\ProjectManagement\Project;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProjectExportController extends Controller
{
    /**
     * Export project data to PDF
     */
    public function exportProject($projectId)
    {
        // Check if project exists and user has access
        $project = Project::findOrFail($projectId);
        
        // Dispatch the job
        ExportProjectPdfJob::dispatch($projectId, auth()->id());
        
        // Return response for Inertia
        return back()->with([
            'message' => 'PDF export has been queued. You will be notified when it is ready for download.',
            'exportStatus' => 'processing'
        ]);
    }
    
    /**
     * Check export status
     */
    public function checkExportStatus($projectId)
    {
        $filename = \Cache::get('project_export_' . auth()->id() . '_' . $projectId);
        
        if ($filename) {
            return response()->json([
                'status' => 'completed',
                'download_url' => route('projects.download-export', ['filename' => $filename])
            ]);
        }
        
        return response()->json([
            'status' => 'processing'
        ]);
    }
    
    /**
     * Download the exported file
     */
    public function downloadExport($filename)
    {
        $path = 'private/exports/' . $filename;
        
        if (Storage::exists($path)) {
            return Storage::download($path, 'project_report.pdf');
        }
        
        abort(404, 'Export file not found');
    }
}

