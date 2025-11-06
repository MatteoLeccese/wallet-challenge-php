<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware that validates the presence of the system API key.
 */
class SystemApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Extract API key from request headers
        $apiKeyHeader = $request->header('x-system-api-key');

        // Expected API key from environment variables
        $expectedKey = config('auth.wallet_db_api_key');

        // Validate the provided API key
        if (empty($expectedKey) || empty($apiKeyHeader) || $apiKeyHeader !== $expectedKey) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or missing system API key',
                'data' => null,
            ], 403);
        }

        // Continue request lifecycle if validation passes
        return $next($request);
    }
}
