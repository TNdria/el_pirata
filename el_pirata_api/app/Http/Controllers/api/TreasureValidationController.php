<?php

namespace App\Http\Controllers\api;

use App\helpers\ImageUploadHelpers;
use App\Http\Controllers\Controller;
use App\Models\TreasureValidation;
use App\Models\hunting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TreasureValidationController extends Controller
{
    /**
     * Soumettre une validation de trésor pour une chasse physique
     */
    public function store(Request $request)
    {
        $request->validate([
            'hunting_id' => 'required|exists:huntings,id',
            'photo' => 'required|string', // Base64 image
            'description' => 'nullable|string|max:1000',
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

            // Vérifier que l'utilisateur peut soumettre une validation
            if (!TreasureValidation::canUserSubmitValidation(auth()->id(), $request->hunting_id)) {
                return response()->json([
                    'state' => 'error',
                    'message' => 'Vous avez déjà soumis une validation pour cette chasse'
                ], 400);
            }

            // Sauvegarder la photo
            $photoPath = ImageUploadHelpers::saveBase64Image($request->photo, true);
            
            if (!$photoPath) {
                return response()->json([
                    'state' => 'error',
                    'message' => 'Erreur lors du traitement de la photo'
                ], 400);
            }

            // Créer la validation
            $validation = TreasureValidation::create([
                'user_id' => auth()->id(),
                'hunting_id' => $request->hunting_id,
                'photo_path' => $photoPath,
                'description' => $request->description,
                'status' => 'pending',
            ]);

            DB::commit();

            return response()->json([
                'state' => 'success',
                'message' => 'Validation soumise avec succès. Elle sera examinée par notre équipe.',
                'validation' => $validation->load(['hunting', 'user'])
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de la soumission de la validation',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les validations de l'utilisateur
     */
    public function index(Request $request)
    {
        try {
            $query = TreasureValidation::with(['hunting', 'validator'])
                ->where('user_id', auth()->id())
                ->orderBy('created_at', 'desc');

            // Filtres
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('hunting_id')) {
                $query->where('hunting_id', $request->hunting_id);
            }

            $validations = $query->paginate(20);

            return response()->json([
                'state' => 'success',
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
     * Récupérer une validation spécifique
     */
    public function show($id)
    {
        try {
            $validation = TreasureValidation::with(['hunting', 'validator', 'user'])
                ->where('user_id', auth()->id())
                ->findOrFail($id);

            return response()->json([
                'state' => 'success',
                'validation' => $validation
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'state' => 'error',
                'message' => 'Validation non trouvée',
                'error' => $th->getMessage()
            ], 404);
        }
    }

    /**
     * Vérifier si l'utilisateur peut soumettre une validation pour une chasse
     */
    public function canSubmit(Request $request)
    {
        $request->validate([
            'hunting_id' => 'required|exists:huntings,id',
        ]);

        try {
            $hunting = hunting::findOrFail($request->hunting_id);
            
            $canSubmit = $hunting->type === 'physic' && 
                        TreasureValidation::canUserSubmitValidation(auth()->id(), $request->hunting_id);

            return response()->json([
                'state' => 'success',
                'can_submit' => $canSubmit,
                'hunting_type' => $hunting->type,
                'message' => $canSubmit 
                    ? 'Vous pouvez soumettre une validation' 
                    : 'Vous ne pouvez pas soumettre de validation pour cette chasse'
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
