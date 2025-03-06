<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    public function up()
    {
        Schema::create('notification', function (Blueprint $table) {
            $table->id(); // Id de la notificación
            $table->unsignedBigInteger('user_id'); // Usuario al que va destinada la notificación
            $table->string('type'); // Tipo de notificación (ej. 'assigned_to_project', 'task_status_changed', etc.)
            $table->text('message'); // Mensaje detallado de la notificación
            $table->morphs('notifiable'); // Esto permite que las notificaciones se puedan asociar con diferentes modelos (proyectos, tareas, etc.)
            $table->boolean('is_read')->default(false); // Si la notificación ha sido leída o no
            $table->timestamps(); // Created_at y updated_at
        });

        // Definir las relaciones
        Schema::table('notification', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notification');
    }
}

