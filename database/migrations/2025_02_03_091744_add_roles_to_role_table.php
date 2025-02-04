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
        DB::table('role')->insert([
            ['name' => 'Admin'],
            ['name' => 'Company Boss'],
            ['name' => 'Department Head'],
            ['name' => 'Employee'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('role')->whereIn('name', ['Admin', 'Company Boss', 'Department Head', 'Employee'])->delete();
    }
};
