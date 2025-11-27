<?php

namespace App\Http\Controllers\admin;

use App\helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Models\TiebreakerChallenge;
use App\Models\TiebreakerParticipation;
use App\Models\hunting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminTiebreakerController extends Controller
{
    /**
     * Récupérer tous les défis de départage (admin)
     */
    public function index(Request $request)
    {
        try {
            $query = TiebreakerChallenge::with(['hunting'])
                ->orderBy('created_at', 'desc');

            // Filtres
            if ($request->has('hunting_id')) {
                $query->where('hunting_id', $request->hunting_id);
            }

            if ($request->has('status')) {
                switch ($request->status) {
                    case 'active':
                        $query->active();
                        break;
                    case 'scheduled':
                        $query->where('starts_at', '>', now());
                        break;
                    case 'finished':
                        $query->where('ends_at', '<', now());
                        break;
                    case 'inactive':
                        $query->where('is_active', false);
                        break;
                }
            }

            $challenges = $query->paginate(20);

            return response()->json([
                'state' => 'success',
                'challenges' => $challenges
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
     * Créer un nouveau défi de départage
     */
    public function store(Request $request)
    {
        $request->validate([
            'hunting_id' => 'required|exists:huntings,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'question' => 'required|string|max:2000',
            'correct_answer' => 'required|string|max:500',
            'hints' => 'nullable|string|max:1000',
            'time_limit_minutes' => 'required|integer|min:5|max:120',
            'starts_at' => 'required|date|after:now',
            'ends_at' => 'required|date|after:starts_at',
        ]);

        DB::beginTransaction();

        try {
            $hunting = hunting::findOrFail($request->hunting_id);

            $challenge = TiebreakerChallenge::create([
                'hunting_id' => $request->hunting_id,
                'title' => $request->title,
                'description' => $request->description,
                'question' => $request->question,
                'correct_answer' => $request->correct_answer,
                'hints' => $request->hints,
                'time_limit_minutes' => $request->time_limit_minutes,
                'starts_at' => $request->starts_at,
                'ends_at' => $request->ends_at,
                'is_active' => true,
            ]);

            LogHelper::logAction(auth()->user(), $challenge, 'create_tiebreaker_challenge');

            DB::commit();

            return response()->json([
                'state' => 'success',
                'message' => 'Défi de départage créé avec succès',
                'challenge' => $challenge->load('hunting')
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de la création du défi',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer un défi spécifique (admin)
     */
    public function show($id)
    {
        try {
            $challenge = TiebreakerChallenge::with(['hunting', 'participations.user'])
                ->findOrFail($id);

            $leaderboard = $challenge->getLeaderboard();

            return response()->json([
                'state' => 'success',
                'challenge' => $challenge,
                'leaderboard' => $leaderboard,
                'stats' => [
                    'total_participations' => $challenge->participations()->count(),
                    'correct_answers' => $challenge->participations()->correct()->count(),
                    'incorrect_answers' => $challenge->participations()->incorrect()->count(),
                    'average_response_time' => $challenge->participations()
                        ->whereNotNull('response_time_seconds')
                        ->avg('response_time_seconds'),
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
     * Mettre à jour un défi de départage
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'question' => 'required|string|max:2000',
            'correct_answer' => 'required|string|max:500',
            'hints' => 'nullable|string|max:1000',
            'time_limit_minutes' => 'required|integer|min:5|max:120',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            $challenge = TiebreakerChallenge::findOrFail($id);
            
            $challenge->update([
                'title' => $request->title,
                'description' => $request->description,
                'question' => $request->question,
                'correct_answer' => $request->correct_answer,
                'hints' => $request->hints,
                'time_limit_minutes' => $request->time_limit_minutes,
                'starts_at' => $request->starts_at,
                'ends_at' => $request->ends_at,
                'is_active' => $request->is_active ?? $challenge->is_active,
            ]);

            LogHelper::logAction(auth()->user(), $challenge, 'update_tiebreaker_challenge');

            DB::commit();

            return response()->json([
                'state' => 'success',
                'message' => 'Défi de départage mis à jour avec succès',
                'challenge' => $challenge->load('hunting')
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de la mise à jour du défi',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Désactiver un défi de départage
     */
    public function deactivate($id)
    {
        DB::beginTransaction();

        try {
            $challenge = TiebreakerChallenge::findOrFail($id);
            $challenge->update(['is_active' => false]);

            LogHelper::logAction(auth()->user(), $challenge, 'deactivate_tiebreaker_challenge');

            DB::commit();

            return response()->json([
                'state' => 'success',
                'message' => 'Défi de départage désactivé avec succès'
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de la désactivation du défi',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les participations d'un défi
     */
    public function getParticipations($id)
    {
        try {
            $challenge = TiebreakerChallenge::findOrFail($id);
            
            $participations = TiebreakerParticipation::with(['user'])
                ->where('tiebreaker_challenge_id', $id)
                ->orderBy('answered_at', 'desc')
                ->paginate(20);

            return response()->json([
                'state' => 'success',
                'challenge' => $challenge,
                'participations' => $participations
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de la récupération des participations',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Statistiques des défis de départage
     */
    public function stats()
    {
        try {
            $stats = [
                'total_challenges' => TiebreakerChallenge::count(),
                'active_challenges' => TiebreakerChallenge::active()->count(),
                'scheduled_challenges' => TiebreakerChallenge::where('starts_at', '>', now())->count(),
                'finished_challenges' => TiebreakerChallenge::where('ends_at', '<', now())->count(),
                'inactive_challenges' => TiebreakerChallenge::where('is_active', false)->count(),
                'total_participations' => TiebreakerParticipation::count(),
                'correct_participations' => TiebreakerParticipation::correct()->count(),
                'incorrect_participations' => TiebreakerParticipation::incorrect()->count(),
                'average_response_time' => TiebreakerParticipation::whereNotNull('response_time_seconds')
                    ->avg('response_time_seconds'),
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
     * Créer automatiquement un défi pour départager des ex-aequo
     */
    public function createForTiebreaker(Request $request)
    {
        $request->validate([
            'hunting_id' => 'required|exists:huntings,id',
            'tied_users' => 'required|array|min:2',
            'tied_users.*' => 'exists:users,id',
            'title' => 'required|string|max:255',
            'question' => 'required|string|max:2000',
            'correct_answer' => 'required|string|max:500',
            'time_limit_minutes' => 'required|integer|min:5|max:60',
        ]);

        DB::beginTransaction();

        try {
            $hunting = hunting::findOrFail($request->hunting_id);

            $challenge = TiebreakerChallenge::create([
                'hunting_id' => $request->hunting_id,
                'title' => $request->title,
                'description' => 'Défi de départage pour les utilisateurs ex-aequo',
                'question' => $request->question,
                'correct_answer' => $request->correct_answer,
                'hints' => 'Réfléchissez bien avant de répondre !',
                'time_limit_minutes' => $request->time_limit_minutes,
                'starts_at' => now()->addMinutes(5), // Commence dans 5 minutes
                'ends_at' => now()->addMinutes($request->time_limit_minutes + 5),
                'is_active' => true,
            ]);

            LogHelper::logAction(auth()->user(), $challenge, 'create_automatic_tiebreaker');

            DB::commit();

            return response()->json([
                'state' => 'success',
                'message' => 'Défi de départage créé automatiquement',
                'challenge' => $challenge->load('hunting'),
                'tied_users_count' => count($request->tied_users),
                'starts_in_minutes' => 5,
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de la création du défi automatique',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
