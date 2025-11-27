<?php

namespace App\Http\Controllers\admin;

use App\helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Models\RefundRequest;
use App\Models\RefundDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminRefundController extends Controller
{
    /**
     * Récupérer toutes les demandes de remboursement (admin)
     */
    public function index(Request $request)
    {
        try {
            $query = RefundRequest::with(['user', 'transaction.hunting', 'documents', 'admin'])
                ->orderBy('created_at', 'desc');

            // Filtres
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('reason')) {
                $query->where('reason', $request->reason);
            }

            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            $refundRequests = $query->paginate(20);

            return response()->json([
                'state' => 'success',
                'refund_requests' => $refundRequests
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de la récupération des demandes de remboursement',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer une demande de remboursement spécifique (admin)
     */
    public function show($id)
    {
        try {
            $refundRequest = RefundRequest::with(['user', 'transaction.hunting', 'documents', 'admin'])
                ->findOrFail($id);

            return response()->json([
                'state' => 'success',
                'refund_request' => $refundRequest
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'state' => 'error',
                'message' => 'Demande de remboursement non trouvée',
                'error' => $th->getMessage()
            ], 404);
        }
    }

    /**
     * Approuver une demande de remboursement
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            $refundRequest = RefundRequest::findOrFail($id);

            if ($refundRequest->status !== 'pending') {
                return response()->json([
                    'state' => 'error',
                    'message' => 'Cette demande ne peut plus être modifiée'
                ], 400);
            }

            $refundRequest->approve(auth()->id(), $request->admin_notes);

            LogHelper::logAction(auth()->user(), $refundRequest, 'approve_refund');

            DB::commit();

            return response()->json([
                'state' => 'success',
                'message' => 'Demande de remboursement approuvée'
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de l\'approbation de la demande',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Rejeter une demande de remboursement
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            $refundRequest = RefundRequest::findOrFail($id);

            if ($refundRequest->status !== 'pending') {
                return response()->json([
                    'state' => 'error',
                    'message' => 'Cette demande ne peut plus être modifiée'
                ], 400);
            }

            $refundRequest->reject(auth()->id(), $request->rejection_reason);

            LogHelper::logAction(auth()->user(), $refundRequest, 'reject_refund');

            DB::commit();

            return response()->json([
                'state' => 'success',
                'message' => 'Demande de remboursement rejetée'
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors du rejet de la demande',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Marquer un remboursement comme traité
     */
    public function markAsProcessed(Request $request, $id)
    {
        $request->validate([
            'refund_date' => 'nullable|date',
        ]);

        DB::beginTransaction();

        try {
            $refundRequest = RefundRequest::findOrFail($id);

            if ($refundRequest->status !== 'approved') {
                return response()->json([
                    'state' => 'error',
                    'message' => 'Seules les demandes approuvées peuvent être marquées comme traitées'
                ], 400);
            }

            $refundRequest->markAsProcessed($request->refund_date);

            LogHelper::logAction(auth()->user(), $refundRequest, 'process_refund');

            DB::commit();

            return response()->json([
                'state' => 'success',
                'message' => 'Remboursement marqué comme traité'
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors du traitement du remboursement',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Statistiques des remboursements
     */
    public function stats()
    {
        try {
            $stats = [
                'total' => RefundRequest::count(),
                'pending' => RefundRequest::where('status', 'pending')->count(),
                'approved' => RefundRequest::where('status', 'approved')->count(),
                'rejected' => RefundRequest::where('status', 'rejected')->count(),
                'processed' => RefundRequest::where('status', 'processed')->count(),
                'total_amount_pending' => RefundRequest::where('status', 'pending')->sum('refund_amount'),
                'total_amount_approved' => RefundRequest::where('status', 'approved')->sum('refund_amount'),
                'total_amount_processed' => RefundRequest::where('status', 'processed')->sum('refund_amount'),
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

