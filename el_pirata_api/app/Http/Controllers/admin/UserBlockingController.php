<?php

namespace App\Http\Controllers\admin;

use App\helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserBlockingLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserBlockingController extends Controller
{
    /**
     * Bloquer un utilisateur
     */
    public function block(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'reason' => 'required|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $user = User::findOrFail($request->user_id);

            if ($user->is_blocked) {
                return response()->json([
                    'state' => 'error',
                    'message' => 'Cet utilisateur est déjà bloqué'
                ], 400);
            }

            $user->block(auth()->id(), $request->reason);

            LogHelper::logAction(auth()->user(), $user, 'block_user');

            DB::commit();

            return response()->json([
                'state' => 'success',
                'message' => 'Utilisateur bloqué avec succès',
                'user' => $user->load(['blockedBy'])
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors du blocage de l\'utilisateur',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Débloquer un utilisateur
     */
    public function unblock(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'reason' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $user = User::findOrFail($request->user_id);

            if (!$user->is_blocked) {
                return response()->json([
                    'state' => 'error',
                    'message' => 'Cet utilisateur n\'est pas bloqué'
                ], 400);
            }

            $user->unblock(auth()->id(), $request->reason);

            LogHelper::logAction(auth()->user(), $user, 'unblock_user');

            DB::commit();

            return response()->json([
                'state' => 'success',
                'message' => 'Utilisateur débloqué avec succès',
                'user' => $user->load(['unblockedBy'])
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors du déblocage de l\'utilisateur',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les utilisateurs bloqués
     */
    public function blockedUsers(Request $request)
    {
        try {
            $query = User::where('is_blocked', true)
                ->with(['blockedBy', 'unblockedBy'])
                ->orderBy('blocked_at', 'desc');

            // Filtres
            if ($request->has('blocked_by')) {
                $query->where('blocked_by', $request->blocked_by);
            }

            if ($request->has('date_from')) {
                $query->where('blocked_at', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->where('blocked_at', '<=', $request->date_to);
            }

            $blockedUsers = $query->paginate(20);

            return response()->json([
                'state' => 'success',
                'blocked_users' => $blockedUsers
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de la récupération des utilisateurs bloqués',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer l'historique de blocage d'un utilisateur
     */
    public function blockingHistory($userId)
    {
        try {
            $user = User::findOrFail($userId);
            
            $blockingHistory = UserBlockingLog::where('user_id', $userId)
                ->with(['admin'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'state' => 'success',
                'user' => $user,
                'blocking_history' => $blockingHistory
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de la récupération de l\'historique',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Statistiques de blocage
     */
    public function stats()
    {
        try {
            $stats = [
                'total_blocked' => User::where('is_blocked', true)->count(),
                'total_unblocked' => User::where('is_blocked', false)
                    ->whereNotNull('blocked_at')->count(),
                'blocked_today' => User::where('is_blocked', true)
                    ->whereDate('blocked_at', today())->count(),
                'blocked_this_week' => User::where('is_blocked', true)
                    ->whereBetween('blocked_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'blocked_this_month' => User::where('is_blocked', true)
                    ->whereBetween('blocked_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
                'total_blocking_actions' => UserBlockingLog::count(),
                'blocking_actions_today' => UserBlockingLog::whereDate('created_at', today())->count(),
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
     * Récupérer tous les logs de blocage
     */
    public function blockingLogs(Request $request)
    {
        try {
            $query = UserBlockingLog::with(['user', 'admin'])
                ->orderBy('created_at', 'desc');

            // Filtres
            if ($request->has('action')) {
                $query->where('action', $request->action);
            }

            if ($request->has('admin_id')) {
                $query->where('admin_id', $request->admin_id);
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

            $logs = $query->paginate(50);

            return response()->json([
                'state' => 'success',
                'blocking_logs' => $logs
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de la récupération des logs',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}

