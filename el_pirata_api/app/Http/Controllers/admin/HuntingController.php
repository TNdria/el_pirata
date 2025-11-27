<?php

namespace App\Http\Controllers\admin;

use App\helpers\ImageUploadHelpers;
use App\helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Models\hunting;
use App\Models\transactions;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class HuntingController extends Controller
{
    public function all()
    {
        try {
            $list = hunting::where(['is_archived' => 0])->get();
            return response()->json(['list' => $list]);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }


    public function getResultatHunt(Request $request)
    {
        try {
            $huntId = $request->huntId;
            $hunting = hunting::withCount('enigmas')->findOrFail($huntId);

            $totalEnigmas = $hunting->enigmas_count;

            $classement = DB::table('enigma_user')
                ->join('enigmas', 'enigma_user.enigma_id', '=', 'enigmas.id')
                ->join('users', 'enigma_user.user_id', '=', 'users.id')
                ->where('enigmas.hunting_id', $huntId)
                ->whereNotNull('enigma_user.completed_at') // uniquement énigmes finies
                ->select(
                    'enigma_user.user_id',
                    DB::raw('COUNT(enigma_user.id) as completed_count'),
                    DB::raw("MAX(enigma_user.completed_at) as last_resolved"),
                    DB::raw($totalEnigmas . ' as total_enigmas'),
                    DB::raw('ROUND((COUNT(enigma_user.id) / ' . $totalEnigmas . ') * 100, 0) as completion_percent')
                )
                ->groupBy('enigma_user.user_id')
                ->orderByDesc('completed_count')   // d’abord plus d’énigmes finies
                ->orderBy('last_resolved', 'asc')  // puis plus rapide (dernière énigme résolue la + tôt)
                ->get();

            $classement = $classement->map(function ($item, $index) {
                $item->rank = $index + 1; // le premier aura 1, le deuxième 2…
                return $item;
            });

            return response()->json(['classement' => $classement]);

        } catch (\Throwable $th) {
            return response()->json(['classement' => [], 'message' => 'un erreur c\'est produit', 'e' => $th->getMessage()], 200);
        }
    }


    public function getMyResultatHunt()
    {
        try {
            $userId = auth()->id();
            // récupérer toutes les chasses où l'utilisateur a joué
            $huntIds = DB::table('enigma_user')
                ->join('enigmas', 'enigma_user.enigma_id', '=', 'enigmas.id')
                ->where('enigma_user.user_id', $userId)
                ->pluck('enigmas.hunting_id')
                ->filter() // enlève les null
                ->unique()
                ->values();

            $result = [];

            foreach ($huntIds as $huntId) {
                $hunting = Hunting::withCount('enigmas')->find($huntId);
                $totalEnigmas = $hunting->enigmas_count;

                // classement global de cette chasse
                $classement = DB::table('enigma_user')
                    ->join('enigmas', 'enigma_user.enigma_id', '=', 'enigmas.id')
                    ->where('enigmas.hunting_id', $huntId)
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

                // ajouter le rang
                $classement = $classement->map(function ($item, $index) {
                    $item->rank = $index + 1;

                    // calcul du temps (en secondes ici)
                    if ($item->first_resolved && $item->last_resolved) {
                        $start = \Carbon\Carbon::parse($item->first_resolved);
                        $end = \Carbon\Carbon::parse($item->last_resolved);
                        $item->time_spent = $end->diffInSeconds($start);
                    } else {
                        $item->time_spent = null;
                    }

                    return $item;
                });

                // chercher le classement du user courant
                $userClassement = $classement->firstWhere('user_id', $userId);

                $result[] = [
                    'hunt_id' => $huntId,
                    'hunt_name' => $hunting->title ?? null,
                    'hunt_date' => $hunting->start_date
                        ? gmdate('d/m/Y H:i', strtotime($hunting->start_date))
                        : null,
                    'rank' => $userClassement->rank ?? null,
                    'progress' => $userClassement->completion_percent ?? 0,
                    'completed' => $userClassement->completed_count ?? 0,
                    'total' => $totalEnigmas,
                    'time_spent' => $userClassement->time_spent ? gmdate("H:i:s", $userClassement->time_spent) : null
                ];
            }

            return response()->json($result);

        } catch (\Throwable $th) {
            return response()->json([
                'classement' => [],
                'message' => 'un erreur c\'est produit',
                'e' => $th->getMessage()
            ], 200);
        }
    }


    public function createOrupdate(Request $request)
    {
        DB::beginTransaction();

        try {
            $hunting_info = $request->HuntingInfo;
            $queryHunting = null;
            $action = "create";
            if (!isset($hunting_info['id'])) {
                $imagePath = ImageUploadHelpers::saveBase64Image($hunting_info['image']);
                $data = $hunting_info;
                $data['image'] = $imagePath;
                // $data['start_date'] = Carbon::parse($data['start_date']);
                $queryHunting = hunting::create($data);
            } else {
                $queryHunting = hunting::findOrFail($hunting_info['id']);
                $data = $hunting_info;
                if ($data['image'] != $queryHunting->image) {
                    $imagePath = ImageUploadHelpers::saveBase64Image($hunting_info['image']);
                    $data['image'] = $imagePath;
                }
                $action = "update";
                // $data['start_date'] = Carbon::parse($data['start_date']);
                $queryHunting->update($data);
            }
            LogHelper::logAction(auth()->user(), $queryHunting, $action);

            DB::commit(); // ✅ Tout s’est bien passé, on valide

            return response()->json([
                'state' => 'success',
                'message' => 'Operation fait avec success',
            ]);

        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack(); // ⛔ Une erreur ? On annule tout

            return response()->json(['state' => 'error', 'message' => 'un erreur c\'est produit', 'm' => $th->getMessage()], 200);
        }
    }

    public function find(Request $request)
    {
        try {

            $id = $request->id;

            $hunting = hunting::findOrFail($id);

            return response()->json([
                'state' => 'success',
                'hunting' => $hunting
            ]);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['state' => 'error', 'message' => 'un erreur c\'est produit', 'hunting' => []], 200);
        }
    }

    public function retriew(Request $request)
    {
        try {

            $id = $request->id;

            $hunting = hunting::findOrFail($id);

            return response()->json([
                'state' => 'success',
                'hunting' => $hunting
            ]);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['state' => 'error', 'message' => 'un erreur c\'est produit', 'hunting' => []], 200);
        }
    }

    public function archive(Request $request)
    {
        DB::beginTransaction();

        try {
            $id = $request->id;
            $queryHunting = hunting::findOrFail($id);

            if ($queryHunting) {
                // Update the user with the new data from $user_info
                $queryHunting->update([
                    'is_archived' => 1, // or any other field you want to update
                ]);

                LogHelper::logAction(auth()->user(), $queryHunting, 'archive');

                DB::commit();

                return response()->json([
                    'state' => 'success',
                    'message' => 'Operation fait avec success',
                ]);

            } else {
                DB::rollBack();
                // Handle the case when the user is not found
                return response()->json(['state' => 'error', 'message' => 'Utilisateur non trouvé'], 200);
            }

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['state' => 'error', 'message' => 'un erreur c\'est produit'], 200);
        }
    }

    public function getSlideHunter(Request $request)
    {
        try {
            $now = Carbon::now();
            $oneHourLater = $now->copy()->addHour();

            $queryHunting = Hunting::where('is_archived', 0)
                ->where('start_date', '>', $oneHourLater)->where('is_published', 1)
                ->get()
                ->groupBy('type');

            return response()->json(['hunting' => $queryHunting], 200);

        } catch (\Throwable $th) {

            return response()->json(['state' => 'error', 'message' => 'un erreur c\'est produit', 'e' => $th->getMessage()], 200);
        }
    }

    public function getAllHunter(Request $request)
    {
        try {
            $now = Carbon::now();
            $oneHourLater = $now->copy()->addHour();

            $queryHunting = Hunting::where('is_archived', 0)->where('is_published', 1)
                ->where('start_date', '>', $oneHourLater)
                ->get();

            return response()->json(['hunting' => $queryHunting], 200);

        } catch (\Throwable $th) {

            return response()->json(['state' => 'error', 'message' => 'un erreur c\'est produit', 'e' => $th->getMessage()], 200);
        }
    }

    public function MyHunt(Request $request)
    {
        try {
            $userId = auth()->id(); // ou $request->user()->id si tu utilises sanctum/passport
            $now = Carbon::now();
            $oneHourLater = $now->copy()->addHour();

            // Celles que l’utilisateur a achetées
            $purchased = Hunting::where('is_archived', 0)
                ->where('is_published', 1)
                ->where('start_date', '>', $oneHourLater)
                ->whereHas('transactions', function ($q) use ($userId) {
                    $q->where('user_id', $userId)
                        ->where('status', 'validated'); // uniquement validées
                })
                ->get();

            // Celles que l’utilisateur n’a PAS achetées
            $notPurchased = Hunting::where('is_archived', 0)
                ->where('is_published', 1)
                ->where('start_date', '>', $oneHourLater)
                ->whereDoesntHave('transactions', function ($q) use ($userId) {
                    $q->where('user_id', $userId)
                        ->where('status', 'validated');
                })
                ->get();

            return response()->json([
                'purchased' => $purchased,
                'notBought' => $notPurchased,
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'state' => 'error',
                'message' => 'Une erreur s\'est produite',
                'e' => $th->getMessage()
            ], 200);
        }
    }
}
