<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\JwtService;

class ApiAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $header = $request->header('Authorization');

        if (!$header || !str_starts_with($header, 'Bearer ')) {
            return response()->json(['message' => 'Token diperlukan.'], 401);
        }

        $token = substr($header, 7);

        try {
            $decoded       = app(JwtService::class)->verify($token);
            $request->user = $decoded;
        } catch (\Exception $e) {
            return response()->json(['message' => 'Token tidak valid atau sudah expired.'], 401);
        }

        return $next($request);
    }
}
