<?php

namespace App\Http\Controllers\admin;

use App\helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use DB;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    //

    public function all()
    {
        try {
            $list = User::withCount([
                'enigmas as enigmas_viewed_count' => function ($q) {
                    $q->whereNotNull('enigma_user.viewed_at');
                },
                'enigmas as enigmas_completed_count' => function ($q) {
                    $q->whereNotNull('enigma_user.completed_at');
                }
            ])->get();

            return response()->json(['list' => $list]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['error' => $th->getMessage()]);
        }
    }

    public function archive(Request $request)
    {
        DB::beginTransaction();

        try {
            $id = $request->id;
            $queryUser = User::findOrFail($id);

            if ($queryUser) {
                // Update the user with the new data from $user_info
                $queryUser->update([
                    'is_archived' => 1, // or any other field you want to update
                ]);

                LogHelper::logAction(auth()->user(), $queryUser, 'archive');

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
}
