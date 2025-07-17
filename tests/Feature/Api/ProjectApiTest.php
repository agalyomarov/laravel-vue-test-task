<?php

namespace Tests\Feature\Api;

use App\Enums\TaskStatusEnum;
use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

// [GET] /api/projects/{id}
it('returns project with its tasks', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id, 'name' => 'Project Alpha']);
    Task::factory()->count(2)->create(['project_id' => $project->id, 'user_id' => $user->id]);
    $response = getJson("/api/projects/{$project->id}")
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'tasks',
            ],
        ]);
    // dump($response->json());
});



it('returns validation error when creating project with invalid data', function () {
    $response = postJson('/api/projects', [
        'name' => 'Missing user_id',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['user_id']);

    // dump($response->json());
});

it('creates a project', function () {
    $user = User::factory()->create();

    $response = postJson('/api/projects', [
        'name' => 'Project Alpha',
        'user_id' => $user->id
    ])
        ->assertCreated()
        ->assertJsonFragment(['name' => 'Project Alpha'])
        ->assertJsonStructure([
            "data" => [
                'id',
                'user_id',
                'name',
                'description'
            ]
        ]);

    $this->assertDatabaseHas('projects', [
        'name' => 'Project Alpha',
        'user_id' => $user->id,
    ]);

    // dump($response->json());
});


it('returns validation error when creating task with invalid data', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['name' => 'Project Alpha', 'user_id' => $user->id]);
    $response = postJson("/api/projects/{$project->id}/tasks", [
        'name' => 'Missing user_id and project_id',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['user_id']);

    // dump($response->json());
});

// [POST] /api/projects/{id}/tasks
it('creates a task for a given project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['name' => 'Project Alpha', 'user_id' => $user->id]);

    $respoonse = postJson("/api/projects/{$project->id}/tasks", [
        'user_id' => $user->id,
        'name' => 'New Task',
    ])
        ->assertCreated()
        ->assertJsonFragment([
            'name' => 'New Task',
            'user_id' => $user->id,
            "project_id" => $project->id,
        ])
        ->assertJsonPath('data.status', TaskStatusEnum::BACKLOG->value);
});

// [DELETE] /api/projects/{id}
it('deletes a project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    deleteJson("/api/projects/{$project->id}")
        ->assertOk()
        ->assertJson(['success' => true]);
});




// [DELETE] /api/projects/{id}/tasks/{task_id}
it('returns 404 when deleting a Project that does not exist', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $task = Task::factory()->create(['project_id' => $project->id, 'user_id' => $user->id]);

    deleteJson("/api/projects/100/tasks/{$task->id}")
        ->assertNotFound();
});

it('deletes a task from a project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $task = Task::factory()->create(['project_id' => $project->id, 'user_id' => $user->id]);

    deleteJson("/api/projects/{$project->id}/tasks/{$task->id}")
        ->assertOk()
        ->assertJson(['success' => true]);
});

