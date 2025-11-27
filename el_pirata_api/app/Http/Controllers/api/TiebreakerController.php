<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\TiebreakerChallenge;
use App\Models\TiebreakerParticipation;
use App\Models\hunting;
use App\Services\IntelligentValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TiebreakerController extends Controller
{
    protected $validationService;

    public function __construct(IntelligentValidationService $validationService)
    {
        $this->validationService = $validationService;
    }

    /**
     * Récupérer les défis disponibles pour une chasse
     */
    public function getAvailableChallenges(Request $request)
    {
        $request->validate([
            'hunting_id' => 'required|exists:huntings,id',
        ]);

        try {
            $hunting = hunting::findOrFail($request->hunting_id);

            $challenges = TiebreakerChallenge::active()
                ->forHunting($request->hunting_id)
                ->where('starts_at', '<=', now())
                ->where('ends_at', '>=', now())
                ->select(['id', 'title', 'description', 'time_limit_minutes', 'starts_at', 'ends_at'])
                ->get()
                ->map(function ($challenge) {
                    return [
                        'id' => $challenge->id,
                        'title' => $challenge->title,
                        'description' => $challenge->description,
                        'time_limit_minutes' => $challenge->time_limit_minutes,
                        'time_remaining' => $challenge->getTimeRemainingFormatted(),
                        'can_participate' => $challenge->canUserParticipate(auth()->id()),
                    ];
                });

            return response()->json([
                'state' => 'success',
                'challenges' => $challenges,
                'hunting' => $hunting
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de la récupération des défis',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer un défi spécifique
     */
    public function getChallenge($id)
    {
        try {
            $challenge = TiebreakerChallenge::active()
                ->findOrFail($id);

            if (!$challenge->canUserParticipate(auth()->id())) {
                return response()->json([
                    'state' => 'error',
                    'message' => 'Vous ne pouvez pas participer à ce défi'
                ], 403);
            }

            return response()->json([
                'state' => 'success',
                'challenge' => [
                    'id' => $challenge->id,
                    'title' => $challenge->title,
                    'description' => $challenge->description,
                    'question' => $challenge->question,
                    'hints' => $challenge->hints,
                    'time_limit_minutes' => $challenge->time_limit_minutes,
                    'time_remaining' => $challenge->getTimeRemainingFormatted(),
                    'starts_at' => $challenge->starts_at,
                    'ends_at' => $challenge->ends_at,
                ]
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'state' => 'error',
                'message' => 'Défi non trouvé',
                'error' => $th->getMessage()
            ], 404);
        }
    }

    /**
     * Soumettre une réponse à un défi
     */
    public function submitAnswer(Request $request, $id)
    {
        $request->validate([
            'answer' => 'required|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            $challenge = TiebreakerChallenge::active()
                ->findOrFail($id);

            if (!$challenge->canUserParticipate(auth()->id())) {
                return response()->json([
                    'state' => 'error',
                    'message' => 'Vous ne pouvez pas participer à ce défi'
                ], 403);
            }

            $startTime = now();
            $responseTime = $startTime->diffInSeconds($challenge->starts_at);

            // Utiliser la validation intelligente
            $validationResult = $this->validationService->validateWithManualCheck(
                $request->answer, 
                $challenge->correct_answer
            );

            $participation = TiebreakerParticipation::create([
                'tiebreaker_challenge_id' => $challenge->id,
                'user_id' => auth()->id(),
                'answer' => $request->answer,
                'is_correct' => $validationResult['is_correct'],
                'answered_at' => $startTime,
                'response_time_seconds' => $responseTime,
            ]);

            DB::commit();

            return response()->json([
                'state' => 'success',
                'is_correct' => $validationResult['is_correct'],
                'response_time_seconds' => $responseTime,
                'validation_method' => $validationResult['method'],
                'confidence' => $validationResult['confidence'],
                'message' => $validationResult['is_correct'] 
                    ? 'Réponse correcte ! Félicitations !'
                    : 'Réponse incorrecte. Bonne chance pour le prochain défi !',
                'suggestions' => $validationResult['suggestions'] ?? [],
                'participation' => $participation
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de la soumission de la réponse',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer le classement d'un défi
     */
    public function getLeaderboard($id)
    {
        try {
            $challenge = TiebreakerChallenge::findOrFail($id);

            $leaderboard = $challenge->getLeaderboard()
                ->load('user')
                ->map(function ($participation) {
                    return [
                        'rank' => $participation->rank,
                        'user' => [
                            'id' => $participation->user->id,
                            'name' => $participation->user->name,
                        ],
                        'is_correct' => $participation->is_correct,
                        'response_time_formatted' => $participation->response_time_formatted,
                        'answered_at' => $participation->answered_at,
                    ];
                });

            return response()->json([
                'state' => 'success',
                'challenge' => [
                    'id' => $challenge->id,
                    'title' => $challenge->title,
                    'status' => $challenge->status,
                ],
                'leaderboard' => $leaderboard
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de la récupération du classement',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer l'historique des participations de l'utilisateur
     */
    public function getMyParticipations(Request $request)
    {
        try {
            $query = TiebreakerParticipation::with(['tiebreakerChallenge.hunting'])
                ->where('user_id', auth()->id())
                ->orderBy('answered_at', 'desc');

            // Filtres
            if ($request->has('hunting_id')) {
                $query->whereHas('tiebreakerChallenge', function ($q) use ($request) {
                    $q->where('hunting_id', $request->hunting_id);
                });
            }

            if ($request->has('is_correct')) {
                $query->where('is_correct', $request->boolean('is_correct'));
            }

            $participations = $query->paginate(20);

            return response()->json([
                'state' => 'success',
                'participations' => $participations
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de la récupération de l\'historique',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
