<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Services\VipCodeService;
use Illuminate\Http\Request;

class VipCodeController extends Controller
{
    protected $vipCodeService;

    public function __construct(VipCodeService $vipCodeService)
    {
        $this->vipCodeService = $vipCodeService;
    }

    /**
     * Récupérer les codes VIP de l'utilisateur
     */
    public function index()
    {
        try {
            $vipCodes = $this->vipCodeService->getUserVipCodes(auth()->id());

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
     * Valider un code VIP
     */
    public function validate(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        try {
            $validation = $this->vipCodeService->validateVipCode($request->code, auth()->id());

            return response()->json([
                'state' => $validation['valid'] ? 'success' : 'error',
                'message' => $validation['message'] ?? 'Code VIP valide',
                'discount_percent' => $validation['discount_percent'] ?? null,
                'vip_code' => $validation['vip_code'] ?? null
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de la validation du code VIP',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Utiliser un code VIP
     */
    public function use(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'transaction_id' => 'required|string',
        ]);

        try {
            $result = $this->vipCodeService->useVipCode($request->code, auth()->id(), $request->transaction_id);

            return response()->json([
                'state' => $result['success'] ? 'success' : 'error',
                'message' => $result['message'],
                'discount_percent' => $result['discount_percent'] ?? null
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de l\'utilisation du code VIP',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}

