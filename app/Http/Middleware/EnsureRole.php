<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Route middleware: role:student or role:admin.
 * A user with the wrong role is sent back to their own dashboard.
 */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if ($user->role !== $role) {
            return redirect()->route($user->homeRoute())
                ->with('error', 'You do not have permission to access that page.');
        }

        return $next($request);
    }
}
