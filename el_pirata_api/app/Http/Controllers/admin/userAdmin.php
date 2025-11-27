<?php

namespace App\Http\Controllers\admin;

use App\helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Mail\fortgotpassword;
use App\Models\Admin;
use Auth;
use DB;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Mail;
use Validator;


class userAdmin extends Controller
{
    public function all()
    {
        try {
            $adminList = Admin::with([
                'role'
            ])->where(['is_archived' => 0])->get();
            return response()->json(['list' => $adminList]);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function createOrupdate(Request $request)
    {
        DB::beginTransaction();

        try {
            $user_info = $request->userInfo;
            $queryUser = null;
            $action = 'create';

            if (!isset($user_info['id'])) {
                $user_data = [
                    "email" => $user_info['email'],
                    "name" => $user_info['name'],
                    "password" => Hash::make($user_info['password']),
                    "role_id" => $user_info['role_id']
                ];
                $queryUser = Admin::create($user_data);
            } else {
                $queryUser = Admin::findOrFail($user_info['id']);

                $user_data = [
                    "email" => $user_info['email'],
                    "name" => $user_info['name'],
                    "password" => Hash::make($user_info['password']),
                    "role_id" => $user_info['role_id']
                ];

                $action = 'update';

                $queryUser->update($user_data);
            }

            LogHelper::logAction(auth()->user(), $queryUser, $action);

            DB::commit(); // ✅ Tout s’est bien passé, on valide

            return response()->json([
                'state' => 'success',
                'message' => 'Operation fait avec success',
            ]);

        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack(); // ⛔ Une erreur ? On annule tout

            return response()->json([
                'state' => 'error',
                'message' => 'un erreur c\'est produit',
                'm' => $th->getMessage()
            ], 200);
        }
    }

    public function find(Request $request)
    {
        try {

            $id = $request->id;

            $user = Admin::findOrFail($id);

            return response()->json([
                'state' => 'success',
                'user' => $user
            ]);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['state' => 'error', 'message' => 'un erreur c\'est produit', 'user' => []], 200);
        }
    }

    public function archive(Request $request)
    {
        DB::beginTransaction();

        try {
            $id = $request->id;
            $queryUser = Admin::findOrFail($id);

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

    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string',
        ]);

        $user = Auth::user();
        // Vérifier que l'ancien mot de passe est correct
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'state' => 'warn',
                'message' => 'L’ancien mot de passe est incorrect.',
            ]);
        }

        // Mettre à jour le mot de passe
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'state' => 'success',
            'message' => 'Mot de passe modifié avec succès.',
        ]);
    }

    public function forgotPassword(Request $request)
    {
        try {
            $email = $request->email;
            $queryAdmin = Admin::where(['email' => $email])->first();

            if (!$queryAdmin) {
                return response()->json(['state' => 'warn', 'message' => 'Utilisteur non existant']);
            }

            Mail::to($queryAdmin->email)->send(new fortgotpassword((object) [
                'is_admin' => true,
                'user' => $queryAdmin
            ]));

            return response()->json(['state' => 'success', 'message' => 'le lien est envoyé']);

        } catch (\Throwable $th) {
            return response()->json(['state' => 'error', 'message' => 'un erreur c\'est produit']);
        }
    }

    public function recovery_password(Request $request)
    {
        try {
            $user = Auth::guard('admin')->user();
            // Mettre à jour le mot de passe

            if (!$user) {
                return response()->json(['state' => 'warn', 'message' => 'ce lien est expirer']);
            }

            $user->password = Hash::make($request->new_password);
            $user->save();

            // Révoquer tous les anciens tokens de l'utilisateur
            $user->tokens->each(function ($token) {
                $token->delete();
            });

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'state' => 'success',
                'message' => 'Mot de passe modifié avec succès.',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role
                ]
            ]);

        } catch (\Throwable $th) {
            return response()->json(['state' => 'error', 'message' => 'un erreur c\'est produit']);
        }
    }
}
