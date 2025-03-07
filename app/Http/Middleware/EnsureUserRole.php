<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserRole
{
    public function handle(Request $request, Closure $next, string $role)
    {
        if (!$request->user() || !$request->user()->{"is{$role}"}()) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}