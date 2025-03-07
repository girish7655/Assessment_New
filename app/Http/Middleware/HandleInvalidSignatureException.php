<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\InvalidSignatureException;

class HandleInvalidSignatureException
{
    public function handle(Request $request, Closure $next)
    {
        try {
            return $next($request);
        } catch (InvalidSignatureException $e) {
            return redirect()->route('verification.notice')->with('error', 'The verification link has expired or is invalid. Please request a new verification link.');
        }
    }
}