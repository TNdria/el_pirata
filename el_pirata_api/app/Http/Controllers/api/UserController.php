<?php

namespace App\Http\Controllers\api;

use App\helpers\ImageUploadHelpers;
use App\Http\Controllers\Controller;
use App\Models\promo;
use App\Models\User;
use DB;
use Illuminate\Http\Request;

class UserController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function update(Request $request)
    {
        DB::beginTransaction();

        try {
            $user_info = $request->userInfo;
            $user = auth()->user();
            $user->update($user_info);

            // Vérifier si profil est 100% complet
            if ($user->completeness_percent === 100) {

                // Par exemple, vérifier si utilisateur n'a pas déjà un code promo actif
                if (!$user->promo_code()->exists()) {
                    // Créer un code promo personnalisé pour cet utilisateur
                    $promo = new promo();
                    $promo->user_id = $user->id;
                    $promo->code = $this->generatePromoCode($user);
                    $promo->percent_off = 10; // par exemple 10% de réduction
                    $promo->save();
                }
            }

            DB::commit(); // ✅ Tout s’est bien passé, on valide

            return response()->json([
                'state' => 'success',
                'message' => 'Operation fait avec success',
                'user' => $user
            ]);

        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack(); // ⛔ Une erreur ? On annule tout

            return response()->json(['state' => 'error', 'message' => 'un erreur c\'est produit', 'm' => $th->getMessage()], 200);
        }
    }

    private function generatePromoCode($user)
    {
        // Exemple simple: PREMIER + id utilisateur + timestamp
        $year = date('Y'); // ex: 2025
        return $year . $user->id . strtoupper(substr(md5(time()), 0, 5));
    }

    public function archive(Request $request)
    {
        DB::beginTransaction();

        try {
            $id = $request->id;
            $queryUser = auth()->user();

            if ($queryUser) {
                // Update the user with the new data from $user_info
                $queryUser->update([
                    'is_archived' => 1, // or any other field you want to update
                ]);

                $queryUser->tokens()->delete();

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

    public function changeAvatar(Request $request)
    {
        DB::beginTransaction();

        try {
            $avatar = $request->avatar;
            $user = auth()->user();

            $data = [
                'avatar' => null
            ];

            $data['avatar'] = ImageUploadHelpers::saveBase64Image($avatar);

            $user->update($data);

            DB::commit(); // ✅ Tout s’est bien passé, on valide

            return response()->json([
                'state' => 'success',
                'message' => 'Mise à jour de profil fait avec success',
                'user' => $user
            ]);

        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack(); // ⛔ Une erreur ? On annule tout

            return response()->json(['state' => 'error', 'message' => 'un erreur c\'est produit', 'm' => $th->getMessage()], 200);
        }
    }

}
