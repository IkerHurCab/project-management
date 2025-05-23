<?php

namespace App\Models\ProjectManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectDocumentation extends Model
{
    use HasFactory;

    // Nombre de la tabla asociada a este modelo
    protected $table = 'project_documentation';

    // Los campos que pueden ser asignados masivamente
    protected $fillable = [
        'project_id',
        'title',
        'summary',
        'content',
        'created_by',
        'is_public',
    ];

   
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    

   
}
