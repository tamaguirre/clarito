<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\ClientRepository;
use RuntimeException;

class RegisterController extends Controller
{
    public function __invoke(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $defaultRole = Role::query()->firstOrCreate(['name' => 'user']);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'birth_date' => $validated['birth_date'],
            'education_level_id' => $validated['education_level_id'],
            'role_id' => $defaultRole->id,
        ]);

        $user->conditions()->sync($validated['conditions'] ?? []);
        $user->load(['educationLevel', 'conditions', 'role', 'company']);

        $clientRepository = app(ClientRepository::class);

        try {
            $clientRepository->personalAccessClient(config('auth.guards.api.provider'));
        } catch (RuntimeException) {
            $clientRepository->createPersonalAccessGrantClient(
                'Local Personal Access Client',
                config('auth.guards.api.provider')
            );
        }

        $accessToken = $user->createToken('local-web')->accessToken;

        return (new UserResource($user))
            ->additional([
                'meta' => [
                    'token_type' => 'Bearer',
                    'access_token' => $accessToken,
                ],
            ])
            ->response()
            ->setStatusCode(201);
    }
}
