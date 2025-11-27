<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\enigma_user;
use App\Models\enigmas;
use App\Services\IntelligentValidationService;
use DB;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Str;

class EnigmasUserController extends Controller
{
    //
    public function getEnigma(Request $request)
    {
        try {
            $userId = auth()->id();  // Ou récupérer l'ID user autrement
            $enigma = null;

            if ($request->hunting_id) {
                $enigma = enigmas::select('id')
                    ->where('is_archived', 0)
                    ->where('is_active', 1)
                    ->where('hunting_id', (int) $request->hunting_id)
                    ->whereDoesntHave('users', function ($query) use ($userId) {
                        $query->where('user_id', $userId)
                            ->whereNotNull('completed_at'); // exclure ceux résolus
                    })
                    ->first();
            } else {
                $enigma = enigmas::select('id')
                    ->where('is_archived', 0)
                    ->where('is_active', 1)
                    ->whereNull('hunting_id')
                    ->whereDoesntHave('users', function ($query) use ($userId) {
                        $query->where('user_id', $userId)
                            ->whereNotNull('completed_at'); // exclure ceux résolus
                    })
                    ->first();
            }

            // Nombre d’énigmes résolues
            $nbr_resolved = DB::table('enigma_user')
                ->where('user_id', $userId)
                ->whereNotNull('completed_at')
                ->count();

            // Nombre d’énigmes en cours
            $nbr_enigmas = DB::table('enigma_user')
                ->where('user_id', $userId)
                ->count();

            return response()->json([
                'enigma' => $enigma,
                'nbr_resolved' => $nbr_resolved,
                'nbr_enigmas' => $nbr_enigmas
            ], 200);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['state' => 'error', 'message' => 'un erreur c\'est produit', 'm' => $th->getMessage(), 'enigma' => null], 200);
        }

    }

    public function preview(Request $request)
    {
        try {

            $enigma = enigmas::select([
                'id',
                'title',
                'text_content',
                'media',
                'media_type',
                'level'
            ])->where(['id' => $request->id, 'is_archived' => 0, 'is_active' => 1])->first();


            return response()->json(['enigma' => $enigma], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['state' => 'error', 'message' => 'un erreur c\'est produit', 'enigma' => [], 'm' => $th->getMessage()], 200);
        }
    }
    public function getInfoEnigmeByID(Request $request)
    {
        try {

            $enigma = enigmas::select([
                'id',
                'title',
                'text_content',
                'media',
                'media_type',
                'level'
            ])->where(['id' => $request->id, 'is_archived' => 0, 'is_active' => 1])->first();

            // if ($request->hunting_id) {

            // }

            $user = auth()->user();

            // Vérifie si l'utilisateur a déjà vu cette énigme
            $alreadyViewed = $user->enigmaAttempts()
                ->where('enigma_id', $enigma->id)
                ->exists();

            if (!$alreadyViewed) {
                // Enregistre la première vue
                $user->enigmaAttempts()->create([
                    'enigma_id' => $enigma->id,
                    'viewed_at' => now(),
                ]);
            }

            return response()->json(['enigma' => $enigma], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['state' => 'error', 'message' => 'un erreur c\'est produit', 'enigma' => [], 'm' => $th->getMessage()], 200);
        }

    }

    public function answerCheck(Request $request)
    {
        try {
            $answer = $request->answer;
            $id_engima = $request->id_engima;

            $enigma = enigmas::select([
                'response',
            ])->where(['id' => $id_engima, 'is_archived' => 0, 'is_active' => 1])->first();

            if ($enigma) {
                // Utiliser la validation intelligente
                $validationService = new IntelligentValidationService();
                $validationResult = $validationService->validateWithManualCheck($answer, $enigma->response);

                if ($validationResult['is_correct']) {
                    $enigmaUser = enigma_user::firstOrCreate([
                        'user_id' => auth()->id(),
                        'enigma_id' => $id_engima,
                    ]);

                    if (!$enigmaUser->completed_at) {
                        $enigmaUser->completed_at = now();

                        do {
                            $code = strtoupper(Str::random(8));
                        } while (enigma_user::where('unique_code', $code)->exists());

                        // Attribue le code à cet utilisateur
                        $enigmaUser->unique_code = $code;

                        $enigmaUser->save();
                    }

                    return response()->json([
                        'is_correct' => true, 
                        'unique_code' => $enigmaUser->unique_code ?? null,
                        'validation_method' => $validationResult['method'],
                        'confidence' => $validationResult['confidence']
                    ], 200);
                } else {
                    return response()->json([
                        'is_correct' => false, 
                        'unique_code' => null,
                        'validation_method' => $validationResult['method'],
                        'confidence' => $validationResult['confidence'],
                        'suggestions' => $validationResult['suggestions'] ?? [],
                        'requires_manual_validation' => $validationResult['requires_manual_validation'] ?? false
                    ], 200);
                }
            }
            return response()->json(['is_correct' => false, 'unique_code' => null], 200);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['state' => 'error', 'message' => 'un erreur c\'est produit', 'enigma' => [], 'm' => $th->getMessage()], 200);
        }
    }

    public function getUserCompletedEnigmaCodes()
    {
        $codes = enigma_user::where('user_id', auth()->id())
            ->whereNotNull('completed_at')
            ->with('enigma')
            ->get()
            ->map(function ($item, $index) {
                return [
                    // 'enigma_number' => $item->enigma->number ?? $index + 1,
                    'enigma_number' => $item->enigma_id,
                    'code' => $item->unique_code
                ];
            });

        return response()->json([
            'codes' => $codes,
            'completed_count' => $codes->count()
        ]);
    }
}
