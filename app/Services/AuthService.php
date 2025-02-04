<?php

namespace App\Services;

use App\Enums\UserTypes;
use App\Http\Requests\Common\RegistrationRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Attempt to log in a user.
     *
     * @param  array  $credentials  The user credentials (email and password)
     *
     * @return array An array containing the token
     *
     * @throws ValidationException If authentication fails
     */
    public function attemptLogin(array $credentials): array
    {
        // Attempt to authenticate the user
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
        // once authenticated, find the user by email
        $user = User::where('email', $credentials['email'])->first();
        // Create token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return the token
        return ['token' => $token];
    }

    /**
     * Register a new user.
     *
     * @param  array  $data  The user data
     *
     * @return array An array containing the token
     */
    public function register(array $data): array
    {
        // Create the user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'user_type' => UserTypes::CLIENT->value,
        ]);

        // Create token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return the token
        return ['token' => $token];
    }
}