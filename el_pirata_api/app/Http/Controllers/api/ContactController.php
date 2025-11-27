<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Mail\ContactMail;
use Illuminate\Http\Request;
use Mail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email' => 'required|email',
                'phone' => 'nullable|string',
                'subject' => 'required|string',
                'message' => 'required|string',
                'terms_accepted' => 'accepted',
                'captcha_checked' => 'accepted',
            ]);
            // 
            Mail::to('contact@elpirata.fr')->bcc('elpirata.contact@gmail.com ')->cc($validated['email'])->send(new ContactMail($validated));

            return response()->json([
                'state' => 'success',
                'message' => 'Message envoyé avec succès.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Erreur de validation, renvoyer les messages d'erreur
            return response()->json([
                'state' => 'error',
                'message' => 'Validation échouée.',
                'errors' => $e->errors(),
            ], 200);
        } catch (\Exception $e) {
            // Toute autre erreur (ex: problème d'envoi mail)
            return response()->json([
                'state' => 'error',
                'message' => 'Une erreur est survenue lors de l\'envoi du message.',
                'error' => $e->getMessage(),
            ], 200);
        }
    }

}
