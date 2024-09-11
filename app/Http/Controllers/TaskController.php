<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();
        return view('tasks.index', compact('tasks'));
    }

    public function show()
    {
        $tasks = Task::all();
        return response()->json(['tasks' => $tasks]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:tasks|max:255',
        ]);

        $task = Task::create(['name' => $request->name]);

        return response()->json($task);
    }

    public function update(Request $request, Task $task)
    {
        $task->update(['status' => $request->status]);
        return response()->json($task);
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(['success' => 'Task deleted']);
    }
}
