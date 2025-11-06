<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiResponseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // If response is already JSON, normalize it
        if ($response instanceof Response && $response->headers->get('content-type') === 'application/json') {
            // Getting original response content
            $original = json_decode($response->getContent() ?? []); 

            // Extract values or apply defaults
            $status = $original->status ?? ($response->getStatusCode() < 400 ? 200 : 400);
            $message = $original->message ?? ($status < 400 ? 'Success' : 'An error occurred');
            $error = $original->error ?? ($status < 400 ? null : 'An unexpected error has occurred');
            $data = $original->data ?? null;

            return response()->json([
                'status' => $status,
                'message' => $message,
                'error' => $error,
                'data' => $data,
            ], $status);
        }

        // If response is not JSON, wrap it as error
        return response()->json([
            'status' => 400,
            'message' => 'An error occurred',
            'error' => 'An unexpected error has occurred',
            'data' => null,
        ], 400);
    }
}
