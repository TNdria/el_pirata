<?php

namespace App\Http\Controllers\api;

use App\helpers\LogHelper;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Http\Request;

class UserSettingController extends Controller
{
    public function find()
    {
        try {

            $user = Auth::user();

            $setting = $user->settings()->first() ?? [
                'two_factor_auth' => true,
                'profile_visible' => true,
                'online_status' => true,
                'achievements_visible' => true,
                'email_notifications' => true,
                'sms_notifications' => false,
            ];

            return response()->json(['setting' => $setting]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['setting' => []]);
        }
    }

    public function createOrupdate(Request $request)
    {
        DB::beginTransaction();

        try {
            $settings_info = $request->settingsInfo;

            $user = Auth::user();

            $user->settings()->updateOrCreate([], $settings_info);

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
}
