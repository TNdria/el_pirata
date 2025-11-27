<?php

namespace App\Http\Controllers;

use App\Models\promo;
use Illuminate\Http\Request;

class PromoController extends Controller
{

    public function getAllPromo()
    {
        try {

            // Récupérer tous les codes promo de l'utilisateur avec le nombre de transactions associées
            $promos = promo::with('user')
                ->withCount('transactions')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($promo) {
                    return [
                        'id' => $promo->id,
                        'user' => $promo->user,
                        'code' => $promo->code,
                        'percent_off' => $promo->percent_off,
                        'used' => $promo->transactions_count > 0,  // true si utilisé, false sinon
                    ];
                });

            return response()->json([
                'state' => 'success',
                'promos' => $promos
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'state' => 'error',
                'message' => 'Une erreur est survenue',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    public function getUserPromo()
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'state' => 'error',
                    'message' => 'Utilisateur non authentifié.'
                ], 401);
            }

            // Récupérer le code promo actif lié à cet utilisateur
            $promo = $user->promo_code()->whereDoesntHave('transactions')->get();


            return response()->json([
                'state' => 'success',
                'promo' => $promo
            ]);

        } catch (\Throwable $th) {

            return response()->json(['state' => 'error', 'message' => 'un erreur c\'est produit', 'm' => $th->getMessage()], 200);
        }
    }

    public function createOrupdate(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:255',
            'percent_off' => 'required|integer|min:1|max:100',
            'valid_until' => 'nullable|date|after:now',
        ]);

        try {

            // Créer un nouveau code promo
            $promo = new promo();
            $promo->code = $request->code;
            $promo->percent_off = $request->percent_off;
            $promo->valid_until = $request->valid_until;
            $promo->save();

            return response()->json([
                'state' => 'success',
                'message' => 'Code promo créé avec succès.',
                'promo' => $promo
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'state' => 'error',
                'message' => 'Une erreur est survenue lors de la création du code promo.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function checkCodePromo(Request $request)
    {

        try {
            $promo = promo::where('code', $request->code)
                ->whereDoesntHave('transactions')
                ->where(function ($query) {
                    $query->whereNull('valid_until')
                        ->orWhere('valid_until', '>', now());
                })
                ->first();

            if (!$promo) {
                return response()->json([
                    'state' => 'error',
                    'message' => 'Code promo invalide ou expiré.'
                ], 200);
            }
            if (isset($promo->user_id) && $promo->user_id != auth()->user()->id) {
                # code...
                return response()->json([
                    'state' => 'error',
                    'message' => 'Code promo n\'est pas pour vous.'
                ], 200);
            }

            return response()->json([
                'state' => 'success',
                'message' => 'Code promo valide',
                'promo' => $promo
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'state' => 'error',
                'message' => 'Une erreur est survenue lors de la vérification du code promo.',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
