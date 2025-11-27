<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class VerifyMathCaptcha
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $challengeId = $request->header('X-Math-Challenge') ?? $request->input('math_challenge_id');
        
        if (!$challengeId) {
            return response()->json([
                'state' => 'error',
                'message' => 'Challenge mathématique requis',
                'captcha_required' => true,
                'captcha_type' => 'math'
            ], 400);
        }

        // Vérifier si le challenge a été validé
        $validationKey = 'math_verified_' . md5($challengeId . $request->ip());
        if (!Cache::has($validationKey)) {
            return response()->json([
                'state' => 'error',
                'message' => 'Challenge mathématique non validé',
                'captcha_required' => true,
                'captcha_type' => 'math'
            ], 400);
        }

        return $next($request);
    }
}
