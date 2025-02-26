<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('project_documentation', function (Blueprint $table) {
            $table->id();  // ID auto incrementable
            $table->foreignId('project_id')->constrained('project')->onDelete('cascade');  // Relación con la tabla project
            $table->string('title');  // Título de la documentación
            $table->text('summary');  // Título de la documentación
            $table->text('content');  // Contenido en Markdown
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');  // Usuario que creó la documentación
            $table->timestamps();  // created_at, updated_at
            $table->boolean('is_public')->default(false);  // Para controlar si la documentación es pública
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_documentation');
    }
};
