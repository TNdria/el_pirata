<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Mail\VerifyEmail;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class RegisteredUser extends Controller
{
    public function store(Request $request)
    {
        try {
            //code...
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required'],
            ]);

            $user = User::create([
                'name' => $request->name,//
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            Mail::to($user->email)->send(new VerifyEmail($user));

            event(new Registered($user));

            Auth::login($user);

            return response()->json([
                'state' => 'success',
                'message' => 'utlisateur enregistrer avec success',
            ]);

        } catch (ValidationException $e) {
            if ($e->errors()['email'] ?? false) {
                return response()->json([
                    'state' => 'error',
                    'message' => 'Cet email est déjà utilisé.',
                    'field' => 'email'
                ], 200);
            }

            return response()->json([
                'state' => 'error',
                'message' => 'Erreur de validation.',
                'errors' => $e->errors(),
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'state' => 'error',
                'message' => 'Une erreur s\'est produite.',
                'debug' => $th->getMessage()
            ], 500);
        }

    }
}
