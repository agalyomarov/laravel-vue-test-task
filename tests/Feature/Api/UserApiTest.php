<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

// [GET] /api/user/{id}
it('returns user without password field', function () {
    $user = User::factory()->create();

    $response = getJson("/api/user/{$user->id}")
        ->assertOk()
        ->assertJsonMissing(['password'])
        ->assertJsonFragment(['id' => $user->id]);

    // dump($response->json());
});

// [GET] /api/user/{id}/projects
it('returns user projects and filters by search', function () {
    $user = User::factory()->create();

    Project::factory()->create(['user_id' => $user->id, 'name' => 'Project Alpha']);
    Project::factory()->create(['user_id' => $user->id, 'name' => 'Project Alpha 2']);
    Project::factory()->create(['user_id' => $user->id, 'name' => 'Something else']);


    $response = getJson("/api/user/{$user->id}/projects?search=Alpha")
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonFragment(['name' => 'Project Alpha'])
        ->assertJsonFragment(['name' => 'Project Alpha 2']);

    // dump($response->json());
});
