<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\TerrainValidationCode;
use App\Models\TerrainValidation;
use App\Models\hunting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TerrainValidationController extends Controller
{
    /**
     * Valider un code terrain avec géolocalisation
     */
    public function validateCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'hunting_id' => 'required|exists:huntings,id',
        ]);

        DB::beginTransaction();

        try {
            $hunting = hunting::findOrFail($request->hunting_id);

            // Vérifier que c'est une chasse physique
            if ($hunting->type !== 'physic') {
                return response()->json([
                    'state' => 'error',
                    'message' => 'Cette fonctionnalité est uniquement disponible pour les chasses physiques'
                ], 400);
            }

            // Trouver le code de validation terrain
            $terrainCode = TerrainValidationCode::active()
                ->forHunting($request->hunting_id)
                ->where('code', strtoupper($request->code))
                ->first();

            if (!$terrainCode) {
                return response()->json([
                    'state' => 'error',
                    'message' => 'Code de validation invalide ou expiré'
                ], 400);
            }

            // Calculer la distance
            $distance = $terrainCode->calculateDistance($request->latitude, $request->longitude);
            $isValid = $terrainCode->isUserInRadius($request->latitude, $request->longitude);

            // Enregistrer la validation
            $validation = TerrainValidation::create([
                'user_id' => auth()->id(),
                'terrain_code_id' => $terrainCode->id,
                'user_latitude' => $request->latitude,
                'user_longitude' => $request->longitude,
                'distance_meters' => round($distance),
                'is_valid' => $isValid,
                'validated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'state' => 'success',
                'is_valid' => $isValid,
                'distance_meters' => round($distance),
                'radius_meters' => $terrainCode->radius_meters,
                'location_name' => $terrainCode->location_name,
                'message' => $isValid 
                    ? 'Validation réussie ! Vous êtes dans la zone autorisée.'
                    : 'Validation échouée. Vous êtes à ' . round($distance) . 'm du point de validation (rayon autorisé: ' . $terrainCode->radius_meters . 'm)',
                'validation' => $validation->load('terrainCode')
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de la validation du code',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les codes de validation disponibles pour une chasse
     */
    public function getAvailableCodes(Request $request)
    {
        $request->validate([
            'hunting_id' => 'required|exists:huntings,id',
        ]);

        try {
            $hunting = hunting::findOrFail($request->hunting_id);

            // Vérifier que c'est une chasse physique
            if ($hunting->type !== 'physic') {
                return response()->json([
                    'state' => 'error',
                    'message' => 'Cette fonctionnalité est uniquement disponible pour les chasses physiques'
                ], 400);
            }

            $codes = TerrainValidationCode::active()
                ->forHunting($request->hunting_id)
                ->select(['id', 'code', 'location_name', 'latitude', 'longitude', 'radius_meters', 'description'])
                ->get();

            return response()->json([
                'state' => 'success',
                'codes' => $codes,
                'hunting' => $hunting
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de la récupération des codes',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer l'historique des validations de l'utilisateur
     */
    public function getValidationHistory(Request $request)
    {
        try {
            $query = TerrainValidation::with(['terrainCode.hunting'])
                ->where('user_id', auth()->id())
                ->orderBy('validated_at', 'desc');

            // Filtres
            if ($request->has('hunting_id')) {
                $query->whereHas('terrainCode', function ($q) use ($request) {
                    $q->where('hunting_id', $request->hunting_id);
                });
            }

            if ($request->has('is_valid')) {
                $query->where('is_valid', $request->boolean('is_valid'));
            }

            $validations = $query->paginate(20);

            return response()->json([
                'state' => 'success',
                'validations' => $validations
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de la récupération de l\'historique',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Vérifier si l'utilisateur peut valider des codes pour une chasse
     */
    public function canValidate(Request $request)
    {
        $request->validate([
            'hunting_id' => 'required|exists:huntings,id',
        ]);

        try {
            $hunting = hunting::findOrFail($request->hunting_id);
            
            $canValidate = $hunting->type === 'physic' && 
                          TerrainValidationCode::active()->forHunting($request->hunting_id)->exists();

            return response()->json([
                'state' => 'success',
                'can_validate' => $canValidate,
                'hunting_type' => $hunting->type,
                'available_codes_count' => $canValidate 
                    ? TerrainValidationCode::active()->forHunting($request->hunting_id)->count()
                    : 0,
                'message' => $canValidate 
                    ? 'Vous pouvez valider des codes terrain pour cette chasse' 
                    : 'Aucun code de validation terrain disponible pour cette chasse'
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de la vérification',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
