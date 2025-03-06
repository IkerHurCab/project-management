<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Crea una nueva notificación para un usuario.
     *
     * @param User $user
     * @param string $type
     * @param string $message
     * @param mixed $notifiable
     * @return Notification
     */
    public function createNotification(User $user, string $type, string $message, $notifiable)
    {
        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'message' => $message,
            'notifiable_type' => get_class($notifiable),
            'notifiable_id' => $notifiable->id,
            'is_read' => false,
        ]);
    }

    /**
     * Notifica cuando un usuario ha sido asignado a un proyecto.
     */
    public function notifyAssignedToProject(User $user, $project)
    {
        $message = "Se te ha asignado al proyecto: {$project->name}.";
        $this->createNotification($user, 'assigned_to_project', $message, $project);
    }

    /**
     * Notifica cuando una tarea ha sido asignada a un usuario.
     */
    public function notifyAssignedToTask(User $user, $task)
    {
        $message = "Se te ha asignado una nueva tarea: {$task->name}.";
        $this->createNotification($user, 'assigned_to_task', $message, $task);
    }

    /**
     * Notifica cuando el estado de una tarea ha cambiado.
     */
    public function notifyTaskStatusChanged(User $user, $task)
    {
        $message = "El estado de la tarea '{$task->name}' ha cambiado a: {$task->status}.";
        $this->createNotification($user, 'task_status_changed', $message, $task);
    }

    /**
     * Notifica cuando se ha añadido nueva documentación a un proyecto.
     */
    public function notifyNewDocumentation(User $user, $project)
    {
        $message = "Se ha añadido nueva documentación al proyecto: {$project->name}.";
        $this->createNotification($user, 'new_documentation', $message, $project);
    }
}
