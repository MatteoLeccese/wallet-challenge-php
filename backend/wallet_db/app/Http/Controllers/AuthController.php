<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Services\AuthService;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Register a new user.
     *
     * @param RegisterRequest $request
     */
    public function register(RegisterRequest $request)
    {
        // Get the validated data and register the user
        $response = $this->authService->register($request->validated());

        // Return the response from the service
        return $response;
    }

    /**
     * Authenticate user and return the token.
     *
     * @param LoginRequest $request
     */
    public function login(LoginRequest $request)
    {
        // Get the user from the service
        $response = $this->authService->login($request->validated());

        // Return the response from the service
        return $response;
    }

}
