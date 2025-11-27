<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Services\VipCodeService;
use App\Models\promo;
use Illuminate\Http\Request;

class AdminVipCodeController extends Controller
{
    protected $vipCodeService;

    public function __construct(VipCodeService $vipCodeService)
    {
        $this->vipCodeService = $vipCodeService;
    }

    /**
     * Récupérer tous les codes VIP
     */
    public function index(Request $request)
    {
        try {
            $query = promo::vipCodes()
                ->with(['user', 'hunting'])
                ->orderBy('created_at', 'desc');

            // Filtres
            if ($request->has('is_used')) {
                $query->where('is_used', $request->boolean('is_used'));
            }

            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            if ($request->has('hunt_id')) {
                $query->where('hunt_id', $request->hunt_id);
            }

            $vipCodes = $query->paginate(20);

            return response()->json([
                'state' => 'success',
                'vip_codes' => $vipCodes
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de la récupération des codes VIP',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Attribuer manuellement les codes VIP pour une chasse
     */
    public function assignVipCodes(Request $request)
    {
        $request->validate([
            'hunt_id' => 'required|exists:huntings,id',
        ]);

        try {
            $result = $this->vipCodeService->assignVipCodesToNextNine($request->hunt_id);

            return response()->json([
                'state' => $result['success'] ? 'success' : 'error',
                'message' => $result['success'] 
                    ? "Codes VIP attribués avec succès ({$result['assigned_codes']} codes)"
                    : 'Erreur lors de l\'attribution des codes VIP',
                'assigned_codes' => $result['assigned_codes'] ?? 0,
                'error' => $result['error'] ?? null
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de l\'attribution des codes VIP',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Vérifier et attribuer automatiquement les codes VIP
     */
    public function checkAndAssign(Request $request)
    {
        $request->validate([
            'hunt_id' => 'required|exists:huntings,id',
        ]);

        try {
            $result = $this->vipCodeService->checkAndAssignVipCodes($request->hunt_id);

            return response()->json([
                'state' => $result['success'] ? 'success' : 'info',
                'message' => $result['message'] ?? 'Codes VIP vérifiés',
                'assigned_codes' => $result['assigned_codes'] ?? 0
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de la vérification des codes VIP',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Statistiques des codes VIP
     */
    public function stats()
    {
        try {
            $stats = [
                'total' => promo::vipCodes()->count(),
                'used' => promo::vipCodes()->where('is_used', true)->count(),
                'unused' => promo::vipCodes()->where('is_used', false)->count(),
                'expired' => promo::vipCodes()->where('valid_until', '<', now())->count(),
                'valid' => promo::vipCodes()->valid()->count(),
                'total_discount_value' => promo::vipCodes()->where('is_used', true)->sum('percent_off'),
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

