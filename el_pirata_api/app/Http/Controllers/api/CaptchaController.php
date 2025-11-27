<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class CaptchaController extends Controller
{
    /**
     * Vérifier un token reCAPTCHA
     */
    public function verify(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'action' => 'required|string|in:login,register,contact,password_reset',
        ]);

        try {
            $secretKey = config('services.recaptcha.secret_key');
            
            if (!$secretKey) {
                return response()->json([
                    'state' => 'error',
                    'message' => 'Configuration reCAPTCHA manquante'
                ], 500);
            }

            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $request->token,
                'remoteip' => $request->ip(),
            ]);

            $result = $response->json();

            if (!$result['success']) {
                return response()->json([
                    'state' => 'error',
                    'message' => 'Vérification reCAPTCHA échouée',
                    'errors' => $result['error-codes'] ?? []
                ], 400);
            }

            // Vérifier le score pour reCAPTCHA v3 (optionnel)
            if (isset($result['score']) && $result['score'] < 0.5) {
                return response()->json([
                    'state' => 'error',
                    'message' => 'Score de confiance trop faible'
                ], 400);
            }

            // Vérifier l'action pour reCAPTCHA v3
            if (isset($result['action']) && $result['action'] !== $request->action) {
                return response()->json([
                    'state' => 'error',
                    'message' => 'Action reCAPTCHA invalide'
                ], 400);
            }

            // Stocker le résultat en cache pour éviter les vérifications multiples
            $cacheKey = 'captcha_verified_' . md5($request->token . $request->ip());
            Cache::put($cacheKey, true, 300); // 5 minutes

            return response()->json([
                'state' => 'success',
                'message' => 'Vérification reCAPTCHA réussie',
                'score' => $result['score'] ?? null,
                'action' => $result['action'] ?? null,
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de la vérification reCAPTCHA',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Vérifier si un token a déjà été validé
     */
    public function checkVerified(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $cacheKey = 'captcha_verified_' . md5($request->token . $request->ip());
        $isVerified = Cache::has($cacheKey);

        return response()->json([
            'state' => 'success',
            'verified' => $isVerified,
        ]);
    }

    /**
     * Générer un challenge mathématique simple (fallback)
     */
    public function generateMathChallenge()
    {
        $operations = ['+', '-', '*'];
        $operation = $operations[array_rand($operations)];
        
        $a = rand(1, 20);
        $b = rand(1, 20);
        
        switch ($operation) {
            case '+':
                $result = $a + $b;
                $question = "{$a} + {$b} = ?";
                break;
            case '-':
                $result = max($a, $b) - min($a, $b);
                $question = max($a, $b) . " - " . min($a, $b) . " = ?";
                break;
            case '*':
                $a = rand(1, 10);
                $b = rand(1, 10);
                $result = $a * $b;
                $question = "{$a} × {$b} = ?";
                break;
        }

        $challengeId = uniqid('math_');
        
        // Stocker le résultat en cache
        Cache::put("math_challenge_{$challengeId}", $result, 300);

        return response()->json([
            'state' => 'success',
            'challenge_id' => $challengeId,
            'question' => $question,
            'expires_in' => 300, // 5 minutes
        ]);
    }

    /**
     * Vérifier une réponse au challenge mathématique
     */
    public function verifyMathChallenge(Request $request)
    {
        $request->validate([
            'challenge_id' => 'required|string',
            'answer' => 'required|integer',
        ]);

        $cacheKey = "math_challenge_{$request->challenge_id}";
        $correctAnswer = Cache::get($cacheKey);

        if ($correctAnswer === null) {
            return response()->json([
                'state' => 'error',
                'message' => 'Challenge expiré ou invalide'
            ], 400);
        }

        $isCorrect = $request->answer == $correctAnswer;
        
        // Supprimer le challenge après vérification
        Cache::forget($cacheKey);

        if ($isCorrect) {
            // Stocker la validation en cache
            $validationKey = 'math_verified_' . md5($request->challenge_id . $request->ip());
            Cache::put($validationKey, true, 300);
        }

        return response()->json([
            'state' => 'success',
            'correct' => $isCorrect,
            'message' => $isCorrect ? 'Réponse correcte' : 'Réponse incorrecte',
        ]);
    }

    /**
     * Vérifier si un challenge mathématique est validé
     */
    public function checkMathVerified(Request $request)
    {
        $request->validate([
            'challenge_id' => 'required|string',
        ]);

        $validationKey = 'math_verified_' . md5($request->challenge_id . $request->ip());
        $isVerified = Cache::has($validationKey);

        return response()->json([
            'state' => 'success',
            'verified' => $isVerified,
        ]);
    }
}
