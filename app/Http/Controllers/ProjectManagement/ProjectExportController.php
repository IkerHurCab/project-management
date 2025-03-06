<?php

namespace App\Http\Controllers\ProjectManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jobs\ExportProjectPdfJob;
use App\Models\ProjectManagement\Project;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ProjectExportController extends Controller
{
    /**
     * Export project data to PDF
     */
    public function exportProject($projectId)
{
    try {
    Log::info("📤 Dispatching job for project ID: " . $projectId);

    $project = Project::findOrFail($projectId);
    
    ExportProjectPdfJob::dispatch($projectId, auth()->id());

    Log::info("✅ Job dispatched successfully.");

    return back()->with([
        'message' => 'PDF export has been queued.',
        'exportStatus' => 'processing'
    ]);
} catch (\Exception $e) {
    Log::error("❌ Error exporting PDF for project ID: " . $projectId);
    Log::error($e->getMessage());

    return back()->with([
        'message' => 'An error occurred while exporting the PDF.',
        'exportStatus' => 'error'
    ]);
}}

    /**
     * Check export status
     */
    public function checkExportStatus($projectId)
{
    $cacheKey = 'export_pdf_' . auth()->id() . '_' . $projectId;
    $pdfData = Cache::get($cacheKey);

    Log::info("🔍 Checking cache for key: " . $cacheKey);
    
    if ($pdfData) {
        Log::info("✅ PDF found in cache for project ID: " . $projectId . "filename: " . $pdfData['filename']);
        return response()->json([
            'status' => 'completed',
            'download_url' => route('projects.download-export', ['projectId' => $projectId, 'filename' => $pdfData['filename']])
        ]);
    }

    Log::info("⏳ PDF still processing for project ID: " . $projectId);
    return response()->json(['status' => 'processing']);
}


public function downloadExport($projectId, $filename)
{
    Log::info("📥 Downloading PDF for project ID: " . $projectId . "filename: " . $filename);
    $cacheKey = 'export_pdf_' . auth()->id() . '_' . $projectId;
    $pdfData = Cache::get($cacheKey);

    if (!$pdfData) {
        return response()->json([
            'status' => 'error',
            'message' => 'El archivo no está listo o ha expirado.'
        ], 404);
    }

    // Decodificar el contenido del PDF
    $pdfContent = base64_decode($pdfData['content']);

    // Enviar el PDF al navegador como descarga
    return response()->streamDownload(function () use ($pdfContent) {
        echo $pdfContent;
    }, $pdfData['filename'], [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'attachment; filename="' . $pdfData['filename'] . '"'
    ]);
}

}

