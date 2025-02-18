<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('project', function (Blueprint $table) {
            $table->dropForeign(['department_id']); 
            $table->dropColumn('department_id'); 
        });
    }

    public function down()
    {
        Schema::table('project', function (Blueprint $table) {
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
        });
    }
};
