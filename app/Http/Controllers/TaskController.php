<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::orderBy('created_at', 'desc')->get();
        return view('tasks.index', compact('tasks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:tasks,name'
        ], [
            'name.required' => 'The task name is required.',
            'name.unique' => 'This task already exists. Please enter a different name.'
        ]);

        $task = Task::create([
            'name' => $request->name,
        ]);

        return response()->json(['success' => true, 'task' => $task]);
    }

    public function markComplete(Task $task)
    {
        $isCompleted = request('is_completed') == 'true' ? 1 : 0;

        $task->update([
            'is_completed' => $isCompleted,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(['success' => true]);
    }

}
