<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\RefundRequest;
use App\Models\RefundDocument;
use App\Models\transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class RefundController extends Controller
{
    /**
     * Créer une demande de remboursement
     */
    public function store(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'reason' => 'required|in:medical,emergency,technical,other',
            'description' => 'required|string|max:1000',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        DB::beginTransaction();

        try {
            // Vérifier que la transaction appartient à l'utilisateur
            $transaction = transactions::where('id', $request->transaction_id)
                ->where('user_id', auth()->id())
                ->where('status', 'validated')
                ->first();

            if (!$transaction) {
                return response()->json([
                    'state' => 'error',
                    'message' => 'Transaction non trouvée ou non valide'
                ], 404);
            }

            // Vérifier qu'il n'y a pas déjà une demande de remboursement pour cette transaction
            $existingRequest = RefundRequest::where('transaction_id', $request->transaction_id)
                ->whereIn('status', ['pending', 'approved'])
                ->first();

            if ($existingRequest) {
                return response()->json([
                    'state' => 'error',
                    'message' => 'Une demande de remboursement est déjà en cours pour cette transaction'
                ], 400);
            }

            // Créer la demande de remboursement
            $refundRequest = RefundRequest::create([
                'user_id' => auth()->id(),
                'transaction_id' => $request->transaction_id,
                'reason' => $request->reason,
                'description' => $request->description,
                'refund_amount' => $transaction->amount_paid,
                'status' => 'pending',
            ]);

            // Gérer les documents
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    $path = $file->store('refund-documents', 'public');
                    
                    RefundDocument::create([
                        'refund_request_id' => $refundRequest->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'type' => $this->determineDocumentType($request->reason),
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'state' => 'success',
                'message' => 'Demande de remboursement créée avec succès',
                'refund_request' => $refundRequest->load('documents')
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de la création de la demande de remboursement',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les demandes de remboursement de l'utilisateur
     */
    public function index(Request $request)
    {
        try {
            $query = RefundRequest::where('user_id', auth()->id())
                ->with(['transaction.hunting', 'documents', 'admin'])
                ->orderBy('created_at', 'desc');

            // Filtres
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('reason')) {
                $query->where('reason', $request->reason);
            }

            $refundRequests = $query->paginate(15);

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
     * Récupérer une demande de remboursement spécifique
     */
    public function show($id)
    {
        try {
            $refundRequest = RefundRequest::where('user_id', auth()->id())
                ->with(['transaction.hunting', 'documents', 'admin'])
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
     * Vérifier l'éligibilité au remboursement
     */
    public function checkEligibility($transactionId)
    {
        try {
            $transaction = transactions::where('id', $transactionId)
                ->where('user_id', auth()->id())
                ->where('status', 'validated')
                ->with('hunting')
                ->first();

            if (!$transaction) {
                return response()->json([
                    'state' => 'error',
                    'message' => 'Transaction non trouvée'
                ], 404);
            }

            // Vérifier s'il y a déjà une demande
            $existingRequest = RefundRequest::where('transaction_id', $transactionId)
                ->whereIn('status', ['pending', 'approved'])
                ->first();

            if ($existingRequest) {
                return response()->json([
                    'state' => 'error',
                    'message' => 'Une demande de remboursement est déjà en cours',
                    'existing_request' => $existingRequest
                ], 400);
            }

            // Vérifier la fenêtre de remboursement
            $huntStartDate = $transaction->hunting->start_date;
            $refundDeadline = $huntStartDate->copy()->subHours(48);
            $isWithinWindow = now()->isBefore($refundDeadline);

            return response()->json([
                'state' => 'success',
                'eligible' => $isWithinWindow,
                'refund_deadline' => $refundDeadline,
                'hunt_start_date' => $huntStartDate,
                'transaction' => $transaction
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de la vérification d\'éligibilité',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Déterminer le type de document basé sur la raison
     */
    private function determineDocumentType(string $reason): string
    {
        return match($reason) {
            'medical' => 'medical_certificate',
            'emergency' => 'emergency_proof',
            'technical' => 'technical_issue',
            default => 'other'
        };
    }
}

