<?php

namespace App\Http\Middleware;

use App\Models\Token;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleWare
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tokenString = $request->bearerToken();
        $token = Token::where('token', $tokenString)->where('is_active', 1)->first();

        if (!$token) {
            return response()->json([
                'message' => 'Invalid or inactive Token',
            ], 401);
        }

        $request->attributes->set('token', $token);


        return $next($request);
    }
}
