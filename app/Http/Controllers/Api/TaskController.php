<?php

namespace App\Http\Controllers\Api;

use App\Enums\TaskStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'user_id' => ['required', Rule::exists('users', 'id')->where('id', $project->user_id)],
            'name' => 'required|string',
            'description' => 'nullable|string',
            'status' => ["sometimes", Rule::enum(TaskStatusEnum::class)],
        ]);

        try {
            $validated["status"] ??= TaskStatusEnum::BACKLOG;
            $task = $project->tasks()->create($validated);
            return new TaskResource($task);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function destroy(Project $project, Task $task)
    {
        $task->delete();
        return response()->json(['success' => true]);
    }
}
