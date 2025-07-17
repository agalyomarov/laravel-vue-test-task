<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function show(Project $project)
    {
        $project = $project->load(['tasks']);
        return new ProjectResource($project);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string',
            'description' => 'nullable|string',
        ]);
        try {
            $project = Project::create($validated);
            return new ProjectResource($project);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return response()->json(['success' => true]);
    }
}
