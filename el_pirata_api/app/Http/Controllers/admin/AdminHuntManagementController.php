<?php

namespace App\Http\Controllers\admin;

use App\helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Models\HuntResult;
use App\Models\hunting;
use App\Models\User;
use App\Services\VipCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminHuntManagementController extends Controller
{
    protected $vipCodeService;

    public function __construct(VipCodeService $vipCodeService)
    {
        $this->vipCodeService = $vipCodeService;
    }

    /**
     * Terminer une chasse et calculer les résultats
     */
    public function finishHunt(Request $request)
    {
        $request->validate([
            'hunting_id' => 'required|exists:huntings,id',
            'prize_distribution' => 'required|array',
            'prize_distribution.*.rank' => 'required|integer|min:1',
            'prize_distribution.*.amount' => 'required|numeric|min:0',
            'auto_assign_vip_codes' => 'boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            $hunting = hunting::withCount('enigmas')->findOrFail($request->hunting_id);

            // Vérifier que la chasse n'est pas déjà terminée
            if ($hunting->is_archived) {
                return response()->json([
                    'state' => 'error',
                    'message' => 'Cette chasse est déjà terminée'
                ], 400);
            }

            // Calculer le classement final
            $leaderboard = $this->calculateLeaderboard($hunting);
            
            if ($leaderboard->isEmpty()) {
                return response()->json([
                    'state' => 'error',
                    'message' => 'Aucun participant n\'a terminé cette chasse'
                ], 400);
            }

            // Créer les résultats de chasse
            $results = [];
            foreach ($leaderboard as $index => $participant) {
                $rank = $index + 1;
                $prizeAmount = $this->getPrizeAmount($rank, $request->prize_distribution);

                $result = HuntResult::create([
                    'hunting_id' => $hunting->id,
                    'user_id' => $participant->user_id,
                    'rank' => $rank,
                    'completed_enigmas' => $participant->completed_count,
                    'total_enigmas' => $hunting->enigmas_count,
                    'completion_percentage' => $participant->completion_percent,
                    'first_enigma_completed_at' => $participant->first_resolved,
                    'last_enigma_completed_at' => $participant->last_resolved,
                    'total_time_seconds' => $participant->time_spent ?? null,
                    'prize_amount' => $prizeAmount,
                    'prize_status' => $prizeAmount > 0 ? 'awarded' : 'pending',
                    'prize_awarded_at' => $prizeAmount > 0 ? now() : null,
                    'awarded_by' => $prizeAmount > 0 ? auth()->id() : null,
                    'notes' => $request->notes,
                ]);

                $results[] = $result->load(['user', 'awardedBy']);
            }

            // Attribuer automatiquement les codes VIP si demandé
            if ($request->boolean('auto_assign_vip_codes')) {
                $this->vipCodeService->assignVipCodesToNextNine($hunting->id);
            }

            // Archiver la chasse
            $hunting->update(['is_archived' => true]);

            LogHelper::logAction(auth()->user(), $hunting, 'finish_hunt');

            DB::commit();

            return response()->json([
                'state' => 'success',
                'message' => 'Chasse terminée avec succès. Résultats calculés et prix attribués.',
                'hunting' => $hunting,
                'results' => $results,
                'total_participants' => count($results),
                'total_prizes_awarded' => collect($results)->where('prize_amount', '>', 0)->sum('prize_amount'),
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de la finalisation de la chasse',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Calculer le classement d'une chasse
     */
    private function calculateLeaderboard($hunting)
    {
        $totalEnigmas = $hunting->enigmas_count;

        $leaderboard = DB::table('enigma_user')
            ->join('enigmas', 'enigma_user.enigma_id', '=', 'enigmas.id')
            ->where('enigmas.hunting_id', $hunting->id)
            ->whereNotNull('enigma_user.completed_at')
            ->select(
                'enigma_user.user_id',
                DB::raw('COUNT(enigma_user.id) as completed_count'),
                DB::raw("MAX(enigma_user.completed_at) as last_resolved"),
                DB::raw("MIN(enigma_user.completed_at) as first_resolved"),
                DB::raw($totalEnigmas . ' as total_enigmas'),
                DB::raw('ROUND((COUNT(enigma_user.id) / ' . $totalEnigmas . ') * 100, 0) as completion_percent')
            )
            ->groupBy('enigma_user.user_id')
            ->orderByDesc('completed_count')
            ->orderBy('last_resolved', 'asc')
            ->get();

        // Calculer le temps total pour chaque participant
        return $leaderboard->map(function ($item) {
            if ($item->first_resolved && $item->last_resolved) {
                $start = \Carbon\Carbon::parse($item->first_resolved);
                $end = \Carbon\Carbon::parse($item->last_resolved);
                $item->time_spent = $end->diffInSeconds($start);
            } else {
                $item->time_spent = null;
            }
            return $item;
        });
    }

    /**
     * Obtenir le montant du prix pour un rang donné
     */
    private function getPrizeAmount($rank, $prizeDistribution)
    {
        foreach ($prizeDistribution as $distribution) {
            if ($distribution['rank'] === $rank) {
                return $distribution['amount'];
            }
        }
        return 0;
    }

    /**
     * Récupérer les résultats d'une chasse terminée
     */
    public function getHuntResults($huntingId)
    {
        try {
            $hunting = hunting::findOrFail($huntingId);
            
            $results = HuntResult::with(['user', 'awardedBy'])
                ->where('hunting_id', $huntingId)
                ->orderBy('rank')
                ->get();

            return response()->json([
                'state' => 'success',
                'hunting' => $hunting,
                'results' => $results,
                'summary' => [
                    'total_participants' => $results->count(),
                    'total_prizes_awarded' => $results->where('prize_amount', '>', 0)->sum('prize_amount'),
                    'pending_prizes' => $results->where('prize_status', 'pending')->count(),
                    'awarded_prizes' => $results->where('prize_status', 'awarded')->count(),
                    'paid_prizes' => $results->where('prize_status', 'paid')->count(),
                ]
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de la récupération des résultats',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Marquer un prix comme payé
     */
    public function markPrizeAsPaid(Request $request, $resultId)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $result = HuntResult::findOrFail($resultId);
            
            if ($result->prize_status !== 'awarded') {
                return response()->json([
                    'state' => 'error',
                    'message' => 'Ce prix n\'a pas encore été attribué'
                ], 400);
            }

            $result->markAsPaid();
            
            if ($request->notes) {
                $result->update(['notes' => $result->notes . "\nPaiement: " . $request->notes]);
            }

            LogHelper::logAction(auth()->user(), $result, 'mark_prize_paid');

            return response()->json([
                'state' => 'success',
                'message' => 'Prix marqué comme payé avec succès',
                'result' => $result->load(['user', 'hunting'])
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors du marquage du paiement',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Modifier le montant d'un prix
     */
    public function updatePrizeAmount(Request $request, $resultId)
    {
        $request->validate([
            'prize_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $result = HuntResult::findOrFail($resultId);
            
            $oldAmount = $result->prize_amount;
            $result->update([
                'prize_amount' => $request->prize_amount,
                'prize_status' => $request->prize_amount > 0 ? 'awarded' : 'pending',
                'prize_awarded_at' => $request->prize_amount > 0 ? now() : null,
                'awarded_by' => $request->prize_amount > 0 ? auth()->id() : null,
                'notes' => $result->notes . "\nModification prix: " . $oldAmount . " → " . $request->prize_amount . ($request->notes ? " (" . $request->notes . ")" : ""),
            ]);

            LogHelper::logAction(auth()->user(), $result, 'update_prize_amount');

            return response()->json([
                'state' => 'success',
                'message' => 'Montant du prix modifié avec succès',
                'result' => $result->load(['user', 'hunting'])
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de la modification du prix',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Statistiques des chasses terminées
     */
    public function getFinishedHuntsStats()
    {
        try {
            $stats = [
                'total_finished_hunts' => hunting::where('is_archived', true)->count(),
                'total_participants' => HuntResult::count(),
                'total_prizes_awarded' => HuntResult::where('prize_amount', '>', 0)->sum('prize_amount'),
                'pending_payments' => HuntResult::where('prize_status', 'awarded')->count(),
                'completed_payments' => HuntResult::where('prize_status', 'paid')->count(),
                'average_participants_per_hunt' => hunting::where('is_archived', true)->count() > 0 
                    ? round(HuntResult::count() / hunting::where('is_archived', true)->count(), 2)
                    : 0,
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
