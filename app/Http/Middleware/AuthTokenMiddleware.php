<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\JwtService;

class AuthTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $header = $request->header('Authorization');

        if (!$header) {
            return response()->json(['message' => 'Token required'], 401);
        }

        $token = str_replace('Bearer ', '', $header);

        try {
            $decoded = app(JwtService::class)->verify($token);

            // simpan user dari token
            $request->user = $decoded;

        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid or expired token'], 401);
        }

        return $next($request);
    }
}
