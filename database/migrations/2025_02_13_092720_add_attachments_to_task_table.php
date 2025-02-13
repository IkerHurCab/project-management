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
        Schema::table('task', function (Blueprint $table) {
            $table->json('attachments')->nullable()->after('end_date'); // O ajusta la posición según sea necesario
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('task', function (Blueprint $table) {
            $table->dropColumn('attachments');
        });
    }
};
