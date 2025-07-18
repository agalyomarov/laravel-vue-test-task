<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::all();

        return UserResource::collection($users);
    }

    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function projects(Request $request, User $user)
    {
        $query = $user->projects();

        if ($request->has('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $projects = $query->get();
        return ProjectResource::collection($projects);
    }
}
