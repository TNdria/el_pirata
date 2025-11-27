<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Mail\TwoFactorCodeMail;
use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Mail;
class LoginController extends Controller
{
    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');
            if (Auth::attempt($credentials)) {
                $user = Auth::user();

                if (is_null($user->email_verified_at)) {
                    return response()->json([
                        'state' => 'error',
                        'message' => 'Votre compte n\'est pas encore vérifié.',
                    ]);
                }

                if ($user->is_archived) {
                    return response()->json([
                        'state' => 'error',
                        'message' => 'Votre compte a été archivé. Veuillez contacter l\'administrateur.',
                    ]);
                }

                $user->last_activity_at = now();
                $user->save();

                $token = $user->createToken('auth_token')->plainTextToken;
                return response()->json([
                    'state' => 'success',
                    'token' => $token,
                    'user' => $user
                ]);

            }

            return response()->json(['state' => 'error', 'message' => 'Email ou mot de passe incorrect']);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['state' => 'error', 'message' => 'un erreur c\'est produit', 'm' => $th->getMessage()]);
        }

    }

    public function authGmail(Request $request)
    {
        try {
            $user_info = $request->userInfo;
            $user_data = [
                "google_id" => $user_info['id'],
                "email" => $user_info['email'],
                "name" => $user_info['given_name'],
                "password" => Hash::make($user_info['id']),
                "email_verified_at" => now()
            ];
            $user = User::updateOrCreate(['email' => $user_info['email']], $user_data);
            $user->last_activity_at = now();
            $user->save();

            Auth::login($user);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'state' => 'success',
                'message' => 'authentification enregistrer avec success',
                'token' => $token,
                'user' => $user
            ]);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['state' => 'error', 'message' => 'un erreur c\'est produit']);
        }
    }

    public function verifed_mail(Request $request)
    {
        try {
            $user = Auth::user();

            if ($user) {
                $user->email_verified_at = now();
                $user->save();

                return response()->json([
                    'state' => 'verified',
                    'user' => [
                        'name' => $user->name,
                        'email' => $user->email,
                    ]
                ]);
            }

            return response()->json([
                'state' => 'failed'
            ]);

        } catch (\Throwable $th) {
            return response()->json(['isConnected' => false, 'message' => $th->getMessage], 200);
        }
    }

    public function loginAdmin(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');
            $admin = Admin::where('email', $request->email)->first();

            if (!$admin || !Hash::check($request->password, $admin->password)) {

                if ($admin) {

                    $admin->increment('login_attempts');

                    if ($admin->is_blocked) {
                        return response()->json(['state' => 'error', 'message' => 'Votre compte est bloqué. Contactez l\'administrateur'], 200);
                    }
                }

                return response()->json(['state' => 'error', 'message' => 'Invalid credentials'], 200);
            }

            if ($admin->auth_two_factor) {
                $admin->generateTwoFactorCode();

                Mail::to($admin->email)->send(new TwoFactorCodeMail($admin));

                return response()->json([
                    'state' => 'success',
                    'user' => [
                        'auth_two_factor' => $admin->auth_two_factor
                    ],
                ]);
            }

            $admin->login_attempts = 0;
            $admin->save();

            $token = $admin->createToken('admin-token')->plainTextToken;


            return response()->json([
                'state' => 'success',
                'token' => $token,
                'user' => [
                    'id' => $admin->id,
                    'name' => $admin->name,
                    'email' => $admin->email,
                    'role' => $admin->role
                ],
                'message' => 'admin connecté'
            ]);

            return response()->json(['state' => 'error', 'message' => 'Email ou mot de passe incorrect']);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['state' => 'error', 'message' => 'un erreur c\'est produit']);
        }

    }

    public function verifyOTPadmin(Request $request)
    {

        try {
            $admin = Admin::where('two_factor_code', $request->code)->where('two_factor_expires_at', '>', Carbon::now())->first();
            if ($admin) {
                $admin->resetTwoFactorCode();

                $token = $admin->createToken('admin-token')->plainTextToken;

                return response()->json([
                    'state' => 'success',
                    'token' => $token,
                    'user' => [
                        'id' => $admin->id,
                        'name' => $admin->name,
                        'email' => $admin->email,
                        'role' => $admin->role
                    ],
                    'message' => 'admin connecté'
                ]);
            } else {
                return response()->json(['state' => 'error', 'message' => 'code expirer']);
            }

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['state' => 'error', 'message' => 'un erreur c\'est produit']);
        }
        // $request->validate(['code' => 'required']);

        // $user = Auth::user();

        // if (
        //     $user->two_factor_code === $request->code &&
        //     $user->two_factor_expires_at->gt(now())
        // ) {
        //     $user->resetTwoFactorCode();
        //     session(['2fa_passed' => true]);

        //     return redirect()->intended('/dashboard');
        // }

        // return back()->withErrors(['code' => 'Code invalide ou expiré.']);
    }
}
