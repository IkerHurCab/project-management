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
        Schema::create('project', function (Blueprint $table) {
           $table->id();
           $table->string('name');
           $table->text('description')->nullable();
           $table->float('assigned_hours')->nullable();
           $table->date('start_date');
           $table->date('end_date')->nullable();
           $table->string('status');
           $table->boolean('is_public')->default(false);
           $table->integer('priority')->default(1);
           $table->foreignId('project_leader_id')->constrained('users')->onDelete('set null');
           $table->json('attachments')->nullable();
        //$table->foreignId('client_company_id')->nullable()->constrained('companies')->onDelete('set null');
           $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project');
    }
};
