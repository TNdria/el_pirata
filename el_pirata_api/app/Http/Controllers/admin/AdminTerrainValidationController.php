<?php

namespace App\Http\Controllers\admin;

use App\helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Models\TerrainValidationCode;
use App\Models\TerrainValidation;
use App\Models\hunting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminTerrainValidationController extends Controller
{
    /**
     * Récupérer tous les codes de validation terrain (admin)
     */
    public function index(Request $request)
    {
        try {
            $query = TerrainValidationCode::with(['hunting'])
                ->orderBy('created_at', 'desc');

            // Filtres
            if ($request->has('hunting_id')) {
                $query->where('hunting_id', $request->hunting_id);
            }

            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            if ($request->has('status')) {
                switch ($request->status) {
                    case 'active':
                        $query->active();
                        break;
                    case 'expired':
                        $query->where('expires_at', '<', now());
                        break;
                    case 'inactive':
                        $query->where('is_active', false);
                        break;
                }
            }

            $codes = $query->paginate(20);

            return response()->json([
                'state' => 'success',
                'codes' => $codes
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
     * Créer un nouveau code de validation terrain
     */
    public function store(Request $request)
    {
        $request->validate([
            'hunting_id' => 'required|exists:huntings,id',
            'location_name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meters' => 'required|integer|min:10|max:1000',
            'description' => 'nullable|string|max:1000',
            'expires_at' => 'nullable|date|after:now',
        ]);

        DB::beginTransaction();

        try {
            $hunting = hunting::findOrFail($request->hunting_id);

            // Vérifier que c'est une chasse physique
            if ($hunting->type !== 'physic') {
                return response()->json([
                    'state' => 'error',
                    'message' => 'Les codes de validation terrain sont uniquement disponibles pour les chasses physiques'
                ], 400);
            }

            // Générer un code unique
            $code = TerrainValidationCode::generateUniqueCode($request->hunting_id);

            $terrainCode = TerrainValidationCode::create([
                'hunting_id' => $request->hunting_id,
                'code' => $code,
                'location_name' => $request->location_name,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'radius_meters' => $request->radius_meters,
                'description' => $request->description,
                'expires_at' => $request->expires_at,
                'is_active' => true,
            ]);

            LogHelper::logAction(auth()->user(), $terrainCode, 'create_terrain_code');

            DB::commit();

            return response()->json([
                'state' => 'success',
                'message' => 'Code de validation terrain créé avec succès',
                'code' => $terrainCode->load('hunting')
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de la création du code',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer un code spécifique (admin)
     */
    public function show($id)
    {
        try {
            $code = TerrainValidationCode::with(['hunting', 'validations.user'])
                ->findOrFail($id);

            return response()->json([
                'state' => 'success',
                'code' => $code
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'state' => 'error',
                'message' => 'Code non trouvé',
                'error' => $th->getMessage()
            ], 404);
        }
    }

    /**
     * Mettre à jour un code de validation terrain
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'location_name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meters' => 'required|integer|min:10|max:1000',
            'description' => 'nullable|string|max:1000',
            'expires_at' => 'nullable|date|after:now',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            $terrainCode = TerrainValidationCode::findOrFail($id);
            
            $terrainCode->update([
                'location_name' => $request->location_name,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'radius_meters' => $request->radius_meters,
                'description' => $request->description,
                'expires_at' => $request->expires_at,
                'is_active' => $request->is_active ?? $terrainCode->is_active,
            ]);

            LogHelper::logAction(auth()->user(), $terrainCode, 'update_terrain_code');

            DB::commit();

            return response()->json([
                'state' => 'success',
                'message' => 'Code de validation terrain mis à jour avec succès',
                'code' => $terrainCode->load('hunting')
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de la mise à jour du code',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Désactiver un code de validation terrain
     */
    public function deactivate($id)
    {
        DB::beginTransaction();

        try {
            $terrainCode = TerrainValidationCode::findOrFail($id);
            $terrainCode->update(['is_active' => false]);

            LogHelper::logAction(auth()->user(), $terrainCode, 'deactivate_terrain_code');

            DB::commit();

            return response()->json([
                'state' => 'success',
                'message' => 'Code de validation terrain désactivé avec succès'
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de la désactivation du code',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les validations pour un code spécifique
     */
    public function getValidations($id)
    {
        try {
            $terrainCode = TerrainValidationCode::findOrFail($id);
            
            $validations = TerrainValidation::with(['user'])
                ->where('terrain_code_id', $id)
                ->orderBy('validated_at', 'desc')
                ->paginate(20);

            return response()->json([
                'state' => 'success',
                'terrain_code' => $terrainCode,
                'validations' => $validations
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de la récupération des validations',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Statistiques des codes de validation terrain
     */
    public function stats()
    {
        try {
            $stats = [
                'total_codes' => TerrainValidationCode::count(),
                'active_codes' => TerrainValidationCode::active()->count(),
                'expired_codes' => TerrainValidationCode::where('expires_at', '<', now())->count(),
                'inactive_codes' => TerrainValidationCode::where('is_active', false)->count(),
                'total_validations' => TerrainValidation::count(),
                'successful_validations' => TerrainValidation::valid()->count(),
                'failed_validations' => TerrainValidation::invalid()->count(),
                'validations_today' => TerrainValidation::whereDate('validated_at', today())->count(),
            ];

            return response()->json([
                'state' => 'success',
                'stats' => $stats
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de la récupération des statistiques',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
