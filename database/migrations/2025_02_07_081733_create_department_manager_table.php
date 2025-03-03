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
        Schema::create('department_manager', function (Blueprint $table) {
            $table->foreignId('manager_id')->constrained()->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('department_id')->constrained()->references('id')->on('department')->onDelete('cascade');
            $table->primary(['manager_id', 'department_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('department_manager');
    }
};
