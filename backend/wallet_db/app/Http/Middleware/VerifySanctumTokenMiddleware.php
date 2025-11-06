<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Sanctum\PersonalAccessToken;

class VerifySanctumTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Extract bearer token from the request
        $token = $request->bearerToken() ?? null;

        // If no token is present
        if (!$token) {
            return response()->json([
                'error' => 'Missing authentication token',
                'message' => 'Bearer token is required to access this resource',
            ], 403);
        }

        // Attempt to find and validate the token
        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken || !$accessToken->tokenable) {
            return response()->json([
                'error' => 'Invalid or expired token',
                'message' => 'Authentication failed. Please log in again.',
            ], 403);
        }

        // Attach the authenticated user to the request
        $request->setUserResolver(fn () => $accessToken->tokenable);

        return $next($request);
    }
}
