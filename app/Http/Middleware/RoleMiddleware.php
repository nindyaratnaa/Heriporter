<?php
// [Sefina] Middleware role/authorization

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role)
    {
        if (session('user_role') !== $role) {
            abort(403, 'Akses ditolak.');
        }
        return $next($request);
    }
}
