<?php

namespace App\Services;

use App\Models\Notification;
use Inertia\Inertia;
use App\Models\User;

class NotificationService
{
    /**
     * Creates a new notification for a user.
     *
     * @param User $user
     * @param string $type
     * @param string $message
     * @param mixed $notifiable
     * @return Notification
     */
    public function createNotification(User $user, string $type, string $message, $notifiable)
    {
        
        
        Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'message' => $message,
            'notifiable_type' => get_class($notifiable),
            'notifiable_id' => $notifiable->id,
            'is_read' => false,
        ]);
    
        $notifications = auth()->user()->notifications()->latest()->take(3)->get()->toArray();

$notifications = collect($notifications)->map(function ($notification) {
    return [
        'id' => $notification['id'],
        'type' => $notification['type'],
        'title' => $this->getNotificationTitle($notification['type']),
        'icon' => $this->getNotificationIcon($notification['type']),
        'message' => $notification['message'],
        'is_read' => $notification['is_read'],
        'created_at' => $notification['created_at'],
    ];
});
       
    }

    public function getFormattedNotifications()
    {
        $notifications = auth()->user()->notifications()->latest()->take(3)->get();

        return $notifications->map(function ($notification) {
            return [
                'id' => $notification->id,
                'type' => $notification->type,
                'title' => $this->getNotificationTitle($notification->type),
                'icon' => $this->getNotificationIcon($notification->type),
                'message' => $notification->message,
                'is_read' => $notification->is_read,
                'created_at' => $notification->created_at->toDateTimeString(), // Formatear la fecha si es necesario
            ];
        });
    }

    private function getNotificationIcon(string $type): string
    {
        $icons = [
            'assigned_to_project' => 'briefcase',
            'assigned_to_task' => 'task',
            'task_status_changed' => 'clipboard',
            'new_documentation' => 'file',
        ];

        return $icons[$type] ?? 'bell';
    }

    private function getNotificationTitle(string $type): string
    {
        $titles = [
            'assigned_to_project' => 'Project Assignment',
            'assigned_to_task' => 'Task Assignment',
            'task_status_changed' => 'Task Status Updated',
            'new_documentation' => 'New Project Documentation',
        ];

        return $titles[$type] ?? 'Notification';
    }
    

    /**
     * Notifies when a user has been assigned to a project.
     */
    public function notifyAssignedToProject(User $user, $project)
    {
        $message = "You have been assigned to the project: {$project->name}.";
        $this->createNotification($user, 'assigned_to_project', $message, $project);
    }

    /**
     * Notifies when a task has been assigned to a user.
     */
    public function notifyAssignedToTask(User $user, $task)
    {
        $message = "You have been assigned a new task: {$task->name}.";
        $this->createNotification($user, 'assigned_to_task', $message, $task);
    }

    /**
     * Notifies when the status of a task has changed.
     */
    public function notifyTaskStatusChanged(User $user, $task)
    {
        $message = "The status of the task '{$task->name}' has changed to: {$task->status}.";
        $this->createNotification($user, 'task_status_changed', $message, $task);
    }

    /**
     * Notifies when new documentation has been added to a project.
     */
    public function notifyNewDocumentation(User $user, $project, $document)
    {
    
        $message = "{$document->title} document has been added to the project: {$project->name}.";
        $this->createNotification($user, 'new_documentation', $message, $project);
        
    }
}
