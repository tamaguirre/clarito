<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return UserResource::collection(
            User::query()
                ->with(['role', 'company', 'educationLevel', 'conditions'])
                ->orderBy('name')
                ->get()
        );
    }

    public function roles(): AnonymousResourceCollection
    {
        return RoleResource::collection(
            Role::query()->orderBy('name')->get()
        );
    }

    public function update(Request $request, User $user): UserResource
    {
        $validated = $request->validate([
            'role_id' => ['sometimes', 'nullable', Rule::exists('roles', 'id')],
            'company_id' => ['sometimes', 'nullable', Rule::exists('companies', 'id')->whereNull('deleted_at')],
        ]);

        $user->update($validated);
        $user->load(['role', 'company', 'educationLevel', 'conditions']);

        return new UserResource($user);
    }
}
