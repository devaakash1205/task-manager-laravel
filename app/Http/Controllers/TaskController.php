<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    // home page
    public function index()
    {
        return view('tasks.index');
    }

    // get tasks
    public function getTasks()
    {
        return response()->json(Task::where('completed', false)->get());
    }

    // get all task
    public function getAllTasks()
    {
        return response()->json(Task::all());
    }

    // save task 
    public function store(Request $request)
    {
        $task = Task::create([
            'title' => $request->title,
            'completed' => false
        ]);

        return response()->json($task);
    }

    // task complete
    public function markAsCompleted($id)
    {
        $task = Task::findOrFail($id);
        $task->update(['completed' => true]);

        return response()->json(['success' => true]);
    }

    // delete task
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return response()->json(['success' => true]);
    }
}
