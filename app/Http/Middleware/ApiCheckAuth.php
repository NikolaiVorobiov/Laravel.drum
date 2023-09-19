<?php

namespace App\Http\Middleware;

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
        $users = User::all();
        $authorizationHeader = $request->header('Authorization');

        $authorization = false;
        foreach ($users as $user) {
            if ( $authorizationHeader == 'Bearer ' . $user->token) {
                $authorization = true;
                break;
            }
        }

        return $authorization ? $next($request) : response()->json([
            'message' => Response::$statusTexts[Response::HTTP_UNAUTHORIZED]
        ], Response::HTTP_UNAUTHORIZED);
    }
}
