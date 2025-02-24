<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        return response()->json(Task::with('project')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'project_id' => 'required|exists:projects,id'
        ]);

        $task = Task::create([
            'name' => $request->name,
            'priority' => $request->priority,
            'project_id' => $request->project_id
        ]);

        return response()->json($task, 201);
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'name' => 'required|string',
            'priority' => 'required|in:low,medium,high',
        ]);

        $task->update([
            'name' => $request->name,
            'priority' => $request->priority,
        ]);

        return response()->json($task);
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(['message' => 'Task deleted successfully']);
    }

    public function getTasksByProject($projectId)
    {
        $tasks = Task::where('project_id', $projectId)->orderBy('priority')->get();
        return response()->json($tasks);
    }

    public function reorder(Request $request)
    {
        foreach ($request->tasks as $taskData) {
            Task::where('id', $taskData['id'])->update(['priority' => $taskData['priority']]);
        }
        return response()->json(['message' => 'Tasks reordered successfully']);
    }
}
