<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function markAllAsRead()
    {
        auth()->user()->notifications()->update(['is_read' => true]);
    
    }

    public function markAsRead($id)
    {
        $notification = Notification::find($id);
        $notification->update(['is_read' => true]);

        
    }

    public function viewAllNotifications()
    {
       return Inertia::render('Users/AllNotifications');

    
    }

    public function deleteNotification($id)
    {
        $notification = Notification::find($id);
        $notification->delete();
    }
}
