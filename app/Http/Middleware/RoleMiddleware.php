<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Usage: ->middleware('role:admin,manager')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::user();
        // Block customer role for admin/staff only area
        if ($user->role === 'customer') {
            Auth::logout();
            return redirect()->route('admin.login')->withErrors(['email' => 'Unauthorized access.']);
        }

        if (!empty($roles) && !in_array($user->role, $roles, true)) {
            abort(403, 'Forbidden');
        }

        return $next($request);
    }
}
