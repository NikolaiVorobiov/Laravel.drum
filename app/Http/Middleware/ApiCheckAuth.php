<?php

namespace App\Http\Middleware;

use App\Http\Requests\TaskRequest;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ApiCheckAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken(); // Retrieving the token from the authorization header

        if (!$token) {
            return response()->json([
                'message' => Response::$statusTexts[Response::HTTP_BAD_REQUEST]
            ], Response::HTTP_BAD_REQUEST);
        }

        // Searching for a user by token in cache or database
        $user = User::query()->where('token', $token)->first();

        if (!$user) {
            return response()->json([
                'message' => Response::$statusTexts[Response::HTTP_UNAUTHORIZED]
            ], Response::HTTP_UNAUTHORIZED);
        }

        $request->merge(['user' => $user]);

        // Pass control further if authentication is successful
        return $next($request);
    }
}
