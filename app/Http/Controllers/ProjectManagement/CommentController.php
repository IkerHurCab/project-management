<?php

namespace App\Http\Controllers\ProjectManagement;

use Illuminate\Http\Request;
use App\Models\ProjectManagement\Task;
use App\Models\ProjectManagement\Comment;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;


class CommentController extends Controller
{

    public function store(Request $request, $projectId, $taskId)
    {   


        $request->validate([
            'content' => 'required|string',
        ]);

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'task_id' => $taskId,
            'content' => $request->content,
        ]);


        return redirect()->route('tasks.show', ['projectId' => $projectId, 'taskId' => $taskId])->with('success', 'Comment posted successfully');


    }
}
