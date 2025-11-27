<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class VerifyCaptcha
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $action = 'default'): Response
    {
        // Vérifier si reCAPTCHA est configuré
        if (!config('services.recaptcha.secret_key')) {
            return $next($request);
        }

        $token = $request->header('X-Captcha-Token') ?? $request->input('captcha_token');
        
        if (!$token) {
            return response()->json([
                'state' => 'error',
                'message' => 'Token reCAPTCHA requis',
                'captcha_required' => true
            ], 400);
        }

        // Vérifier si le token a déjà été validé
        $cacheKey = 'captcha_verified_' . md5($token . $request->ip());
        if (Cache::has($cacheKey)) {
            return $next($request);
        }

        // Vérifier le token avec Google
        try {
            $response = \Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('services.recaptcha.secret_key'),
                'response' => $token,
                'remoteip' => $request->ip(),
            ]);

            $result = $response->json();

            if (!$result['success']) {
                return response()->json([
                    'state' => 'error',
                    'message' => 'Vérification reCAPTCHA échouée',
                    'captcha_required' => true,
                    'errors' => $result['error-codes'] ?? []
                ], 400);
            }

            // Vérifier le score pour reCAPTCHA v3
            if (isset($result['score']) && $result['score'] < 0.5) {
                return response()->json([
                    'state' => 'error',
                    'message' => 'Score de confiance trop faible',
                    'captcha_required' => true
                ], 400);
            }

            // Vérifier l'action pour reCAPTCHA v3
            if (isset($result['action']) && $result['action'] !== $action) {
                return response()->json([
                    'state' => 'error',
                    'message' => 'Action reCAPTCHA invalide',
                    'captcha_required' => true
                ], 400);
            }

            // Stocker la validation en cache
            Cache::put($cacheKey, true, 300);

        } catch (\Throwable $th) {
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de la vérification reCAPTCHA',
                'captcha_required' => true
            ], 500);
        }

        return $next($request);
    }
}
