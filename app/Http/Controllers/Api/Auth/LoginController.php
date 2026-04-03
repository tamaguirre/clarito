<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\ClientRepository;
use RuntimeException;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::query()
            ->where('email', $validated['email'])
            ->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'errors' => [
                    'email' => ['Credenciales inválidas.'],
                ],
            ], 422);
        }

        $clientRepository = app(ClientRepository::class);

        try {
            $clientRepository->personalAccessClient(config('auth.guards.api.provider'));
        } catch (RuntimeException) {
            $clientRepository->createPersonalAccessGrantClient(
                'Local Personal Access Client',
                config('auth.guards.api.provider')
            );
        }

        $user->load(['educationLevel', 'conditions']);

        $accessToken = $user->createToken('local-web')->accessToken;

        return (new UserResource($user))
            ->additional([
                'meta' => [
                    'token_type' => 'Bearer',
                    'access_token' => $accessToken,
                ],
            ])
            ->response()
            ->setStatusCode(200);
    }
}
