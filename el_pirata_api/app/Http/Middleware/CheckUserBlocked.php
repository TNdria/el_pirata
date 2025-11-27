<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserBlocked
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->is_blocked) {
            return response()->json([
                'state' => 'error',
                'message' => 'Votre compte est temporairement bloqué. Veuillez contacter le support El Pirata.',
                'blocked_at' => auth()->user()->blocked_at,
                'block_reason' => auth()->user()->block_reason,
            ], 403);
        }

        return $next($request);
    }
}

