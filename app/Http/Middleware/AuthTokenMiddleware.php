<?php
// [Nya] Middleware autentikasi session

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('user_id')) {
            return redirect()->route('login')->withErrors(['email' => 'Silakan login terlebih dahulu.']);
        }

        // Student belum sorting hat → paksa ke sorting hat dulu
        if (
            session('user_role') === 'student' &&
            empty(session('user_house')) &&
            !$request->routeIs('sorting-hat.*') &&
            !$request->routeIs('logout')
        ) {
            return redirect()->route('sorting-hat.questions');
        }

        return $next($request);
    }
}
