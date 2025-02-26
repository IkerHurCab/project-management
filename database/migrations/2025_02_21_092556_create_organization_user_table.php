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
        Schema::create('organization_user', function (Blueprint $table) {
            $table->foreignId('organization_id')->constrained()->references('id')->on('organization')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->references('id')->on('users')->onDelete('cascade');
            $table->primary(['organization_id', 'user_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_user');
    }
};
