<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\NewAccessToken;

class ApiAuthService
{
    /**
     * @param  array{name: string, email: string, password: string}  $attributes
     */
    public function register(array $attributes): User
    {
        $user = DB::transaction(function () use ($attributes): User {
            $user = User::query()->create($attributes);
            $user->assignRole('customer');

            return $user;
        });

        event(new Registered($user));

        return $user;
    }

    /**
     * @return array{token: NewAccessToken, user: User}|null
     */
    public function login(string $email, string $password, string $deviceName = 'api-client'): ?array
    {
        $user = User::query()->where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            return null;
        }

        return [
            'token' => $user->createToken($deviceName),
            'user' => $user,
        ];
    }
}
