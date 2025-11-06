<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * AuthService handles user registration, login, and credential validation.
 */
class AuthService
{
    /**
     * Register a new user with hashed password.
     *
     * @param array $data
     */
    public function register(array $data)
    {
        // Hash the password before saving
        $data['password'] = Hash::make($data['password']);

        try {
            // Create and return the new user
            $new_user = User::create($data);

            return response()->json([
                'message' => 'User registered successfully',
                'data' => [
                    'id' => $new_user->id,
                    'names' => $new_user->names,
                    'email' => $new_user->email,
                    'document' => $new_user->document,
                    'phone' => $new_user->phone,
                ],
            ], 201);
        } catch (\Throwable $th) {
            if ($th->getCode() === '23000') {
                return response()->json([
                    'error' => 'Duplicated user. A user with the same email, document or phone already exists.'
                ], 400);
            }

            return response()->json([
                'error' => 'An unexpected error occurred.'
            ], 400);
        }
    }

    /**
     * Authenticate user and return the token.
     *
     * @param array $request
     */
    public function login(array $request)
    {
        try {
            // Find the user by email
            $user = User::where('email', $request['email'])->first();

            // Check if user exists and password matches
            if (!$user || !Hash::check($request['password'], $user->password)) {
                return response()->json([
                    'status' => 400,
                    'message' => 'An error occurred',
                    'error' => 'The combination of email and password is invalid.',
                    'data' => null,
                ], 400);
            }

            // Generate token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => 200,
                'message' => 'Login successful',
                'error' => null,
                'data' => [
                    'access_token' => $token,
                    'user' => [
                        'id' => $user->id,
                        'names' => $user->names,
                        'email' => $user->email,
                        'document' => $user->document,
                        'phone' => $user->phone,
                    ],
                ],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred',
                'error' => 'Unexpected error during login',
                'data' => null,
            ], 500);
        }
    }
}
