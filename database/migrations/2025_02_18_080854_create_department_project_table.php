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
        Schema::create('department_project', function (Blueprint $table) {
            $table->foreignId('department_id')->constrained()->references('id')->on('department')->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->references('id')->on('project')->onDelete('cascade');
            $table->primary(['department_id', 'project_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('department_project');
    }
};
