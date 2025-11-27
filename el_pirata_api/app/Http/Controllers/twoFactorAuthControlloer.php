<?php

namespace App\Http\Controllers;

use Auth;
use Hash;
use Illuminate\Http\Request;

class twoFactorAuthControlloer extends Controller
{
    public function authTwoFactorChangeSetting(Request $request)
    {
        try {
            $user = Auth::user();

            if (Hash::check($request->password, $user->password)) {
                $user->auth_two_factor = $request->auth_two_factor;
                $user->save();
                return response()->json(['state' => 'success', 'message' => 'Operation fait avec success']);
            } else {
                return response()->json(['state' => 'error', 'message' => 'Mot de passe incorrect']);
            }

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['state' => 'error', 'message' => 'un erreur c\'est produit']);
        }
    }

    public function authTwoFactorCheckSetting()
    {
        try {
            $user = Auth::user();
            return response()->json(['state' => 'success', 'auth_two_factor' => $user->auth_two_factor]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['state' => 'error', 'message' => 'un erreur c\'est produit']);
        }
    }
}
