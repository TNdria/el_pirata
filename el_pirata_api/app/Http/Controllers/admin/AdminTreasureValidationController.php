<?php

namespace App\Http\Controllers\admin;

use App\helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Models\TreasureValidation;
use App\Models\hunting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminTreasureValidationController extends Controller
{
    /**
     * Récupérer toutes les validations de trésor (admin)
     */
    public function index(Request $request)
    {
        try {
            $query = TreasureValidation::with(['user', 'hunting', 'validator'])
                ->orderBy('created_at', 'desc');

            // Filtres
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('hunting_id')) {
                $query->where('hunting_id', $request->hunting_id);
            }

            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            if ($request->has('date_from')) {
                $query->where('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->where('created_at', '<=', $request->date_to);
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
     * Récupérer une validation spécifique (admin)
     */
    public function show($id)
    {
        try {
            $validation = TreasureValidation::with(['user', 'hunting', 'validator'])
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
     * Approuver une validation de trésor
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            $validation = TreasureValidation::findOrFail($id);

            if ($validation->status !== 'pending') {
                return response()->json([
                    'state' => 'error',
                    'message' => 'Cette validation ne peut plus être modifiée'
                ], 400);
            }

            $validation->approve(auth()->id(), $request->admin_notes);

            LogHelper::logAction(auth()->user(), $validation, 'approve_treasure_validation');

            DB::commit();

            return response()->json([
                'state' => 'success',
                'message' => 'Validation approuvée avec succès'
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de l\'approbation de la validation',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Rejeter une validation de trésor
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            $validation = TreasureValidation::findOrFail($id);

            if ($validation->status !== 'pending') {
                return response()->json([
                    'state' => 'error',
                    'message' => 'Cette validation ne peut plus être modifiée'
                ], 400);
            }

            $validation->reject(auth()->id(), $request->admin_notes);

            LogHelper::logAction(auth()->user(), $validation, 'reject_treasure_validation');

            DB::commit();

            return response()->json([
                'state' => 'success',
                'message' => 'Validation rejetée avec succès'
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors du rejet de la validation',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Statistiques des validations de trésor
     */
    public function stats()
    {
        try {
            $stats = [
                'total' => TreasureValidation::count(),
                'pending' => TreasureValidation::where('status', 'pending')->count(),
                'approved' => TreasureValidation::where('status', 'approved')->count(),
                'rejected' => TreasureValidation::where('status', 'rejected')->count(),
                'pending_today' => TreasureValidation::where('status', 'pending')
                    ->whereDate('created_at', today())->count(),
                'approved_today' => TreasureValidation::where('status', 'approved')
                    ->whereDate('validated_at', today())->count(),
                'rejected_today' => TreasureValidation::where('status', 'rejected')
                    ->whereDate('validated_at', today())->count(),
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

    /**
     * Récupérer les validations par chasse
     */
    public function byHunting($huntingId)
    {
        try {
            $hunting = hunting::findOrFail($huntingId);
            
            $validations = TreasureValidation::with(['user', 'validator'])
                ->where('hunting_id', $huntingId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'state' => 'success',
                'hunting' => $hunting,
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
}
