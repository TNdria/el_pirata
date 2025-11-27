<?php

namespace App\Http\Controllers\admin;

use App\helpers\ImageUploadHelpers;
use App\helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Models\enigma_user;
use App\Models\enigmas;
use App\Models\hunting;
use DB;
use Illuminate\Http\Request;

class EnigmasController extends Controller
{
    public function all()
    {
        try {
            $list = enigmas::with(['hunting:id,title'])->where(['is_archived' => 0])->get();
            $huntingList = hunting::select(['id', 'title'])
                ->where([
                    'is_archived' => 0,
                ])
                ->get();
            return response()->json(['list' => $list, 'huntingList' => $huntingList], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['state' => 'error', 'message' => 'un erreur c\'est produit', 'm' => $th->getMessage()], 200);
        }
    }

    public function statEnigmeUser(Request $request)
    {
        try {
            $user_id = $request->user_id;
            $enigmas = enigma_user::with(['enigma.hunting'])->where(['user_id' => $user_id])
                ->get();
            return response()->json(['enigmas' => $enigmas], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['state' => 'error', 'message' => 'un erreur c\'est produit', 'm' => $th->getMessage()], 200);
        }
    }

    public function createOrupdate(Request $request)
    {
        DB::beginTransaction();

        try {
            $engima_info = $request->EnigmasInfo;
            $queryEnigmas = null;
            $action = "create";
            if (!isset($engima_info['id'])) {
                $imagePath = ImageUploadHelpers::saveBase64Image($engima_info['media']);
                $data = $engima_info;
                $data['media'] = $imagePath;
                // $data['start_date'] = Carbon::parse($data['start_date']);
                $queryEnigmas = enigmas::create($data);
            } else {
                $queryEnigmas = enigmas::findOrFail($engima_info['id']);
                $data = $engima_info;
                if ($data['media'] != $queryEnigmas->media) {
                    $imagePath = ImageUploadHelpers::saveBase64Image($engima_info['media']);
                    $data['media'] = $imagePath;
                }
                $action = "update";
                // $data['start_date'] = Carbon::parse($data['start_date']);
                $queryEnigmas->update($data);
            }

            LogHelper::logAction(auth()->user(), $queryEnigmas, $action);

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

            $enigma = enigmas::findOrFail($id);

            return response()->json([
                'state' => 'success',
                'enigma' => $enigma
            ]);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['state' => 'error', 'message' => 'un erreur c\'est produit', 'enigma' => []], 200);
        }
    }

    public function archive(Request $request)
    {
        DB::beginTransaction();

        try {
            $id = $request->id;
            $queryEnigmas = enigmas::findOrFail($id);

            if ($queryEnigmas) {
                // Update the user with the new data from $user_info
                $queryEnigmas->update([
                    'is_archived' => 1, // or any other field you want to update
                ]);

                LogHelper::logAction(auth()->user(), $queryEnigmas, 'archive');

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
