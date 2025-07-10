<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GuestLogin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->has('guest_login')) {
            return redirect()->route('login')->with('error', 'Sesi anda berakhir, harap login kembali.');
        }

        if (now()->timestamp > $request->session()->get('guest_login_expire')) {
            $request->session()->forget(['guest_login', 'guest_login_expire']);
            return redirect()->route('login')->with('error', 'Sesi anda berakhir, harap login kembali.');
        }

        return $next($request);

    }
}
