<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ApiAuthService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request, ApiAuthService $authService): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if ($validator->fails()) {
            return ApiResponse::error(
                message: 'Data registrasi tidak valid.',
                code: 422,
                data: $validator->errors(),
            );
        }

        $user = $authService->register($validator->validated());

        return ApiResponse::success(
            data: $this->userData($user),
            message: 'Registrasi berhasil.',
            code: 201,
        );
    }

    public function login(Request $request, ApiAuthService $authService): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['sometimes', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return ApiResponse::error(
                message: 'Data login tidak valid.',
                code: 422,
                data: $validator->errors(),
            );
        }

        $credentials = $validator->validated();
        $authentication = $authService->login(
            Str::lower($credentials['email']),
            $credentials['password'],
            $credentials['device_name'] ?? 'api-client',
        );

        if (! $authentication) {
            return ApiResponse::error(
                message: 'Email atau password salah.',
                code: 401,
            );
        }

        return ApiResponse::success(
            data: [
                'token_type' => 'Bearer',
                'access_token' => $authentication['token']->plainTextToken,
                'user' => $this->userData($authentication['user']),
            ],
            message: 'Login berhasil.',
        );
    }

    /**
     * @return array{id: int, name: string, email: string}
     */
    private function userData(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];
    }
}
