<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Insertar los roles con el guard_name 'web'
        DB::table('roles')->insert([
            ['name' => 'admin', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'company_boss', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'department_head', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'employee', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        // Eliminar los roles insertados en caso de rollback
        DB::table('roles')->whereIn('name', ['admin', 'company_boss', 'department_head', 'employee'])->delete();
    }
};
