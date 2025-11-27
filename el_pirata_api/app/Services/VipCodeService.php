<?php

namespace App\Services;

use App\Models\promo;
use App\Models\transactions;
use App\Models\hunting;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\VipCodeMail;

class VipCodeService
{
    /**
     * Attribue automatiquement les codes VIP aux 9 suivants
     */
    public function assignVipCodesToNextNine($huntId)
    {
        try {
            $hunting = hunting::withCount('enigmas')->findOrFail($huntId);
            $totalEnigmas = $hunting->enigmas_count;

            // Récupérer le classement des joueurs
            $classement = \DB::table('enigma_user')
                ->join('enigmas', 'enigma_user.enigma_id', '=', 'enigmas.id')
                ->join('users', 'enigma_user.user_id', '=', 'users.id')
                ->where('enigmas.hunting_id', $huntId)
                ->whereNotNull('enigma_user.completed_at')
                ->select(
                    'enigma_user.user_id',
                    \DB::raw('COUNT(enigma_user.id) as completed_count'),
                    \DB::raw("MAX(enigma_user.completed_at) as last_resolved"),
                    \DB::raw($totalEnigmas . ' as total_enigmas'),
                    \DB::raw('ROUND((COUNT(enigma_user.id) / ' . $totalEnigmas . ') * 100, 0) as completion_percent')
                )
                ->groupBy('enigma_user.user_id')
                ->orderByDesc('completed_count')
                ->orderBy('last_resolved', 'asc')
                ->get();

            // Ajouter le rang
            $classement = $classement->map(function ($item, $index) {
                $item->rank = $index + 1;
                return $item;
            });

            // Le premier gagne le trésor, les 9 suivants (rang 2 à 10) gagnent les codes VIP
            $vipWinners = $classement->where('rank', '>=', 2)->where('rank', '<=', 10);

            $assignedCodes = [];

            foreach ($vipWinners as $winner) {
                $user = User::find($winner->user_id);
                
                if ($user && !$this->userAlreadyHasVipCodeForHunt($user->id, $huntId)) {
                    $vipCode = $this->createVipCode($user->id, $huntId);
                    $assignedCodes[] = $vipCode;
                    
                    // Envoyer l'email
                    $this->sendVipCodeEmail($user, $vipCode, $hunting, $winner->rank);
                }
            }

            return [
                'success' => true,
                'assigned_codes' => count($assignedCodes),
                'codes' => $assignedCodes
            ];

        } catch (\Throwable $th) {
            return [
                'success' => false,
                'error' => $th->getMessage()
            ];
        }
    }

    /**
     * Vérifie si l'utilisateur a déjà un code VIP pour cette chasse
     */
    private function userAlreadyHasVipCodeForHunt($userId, $huntId)
    {
        return promo::where('user_id', $userId)
            ->where('hunt_id', $huntId)
            ->exists();
    }

    /**
     * Crée un code VIP pour un utilisateur
     */
    private function createVipCode($userId, $huntId)
    {
        $code = $this->generateVipCode();
        
        return promo::create([
            'user_id' => $userId,
            'hunt_id' => $huntId,
            'code' => $code,
            'percent_off' => 15, // 15% de réduction pour les codes VIP
            'valid_until' => now()->addMonths(6), // Valide 6 mois
            'type' => 'vip_code',
            'is_used' => false,
        ]);
    }

    /**
     * Génère un code VIP unique
     */
    private function generateVipCode()
    {
        do {
            $code = 'VIP' . strtoupper(\Str::random(6));
        } while (promo::where('code', $code)->exists());

        return $code;
    }

    /**
     * Envoie l'email avec le code VIP
     */
    private function sendVipCodeEmail($user, $vipCode, $hunting, $rank)
    {
        try {
            Mail::to($user->email)->send(new VipCodeMail($user, $vipCode, $hunting, $rank));
        } catch (\Throwable $th) {
            // Log l'erreur mais ne pas faire échouer le processus
            \Log::error('Erreur envoi email code VIP: ' . $th->getMessage());
        }
    }

    /**
     * Valide un code VIP
     */
    public function validateVipCode($code, $userId)
    {
        $vipCode = promo::where('code', $code)
            ->where('type', 'vip_code')
            ->where('is_used', false)
            ->where(function ($query) {
                $query->whereNull('valid_until')
                    ->orWhere('valid_until', '>', now());
            })
            ->first();

        if (!$vipCode) {
            return [
                'valid' => false,
                'message' => 'Code VIP invalide ou expiré'
            ];
        }

        // Vérifier si le code est pour un utilisateur spécifique
        if ($vipCode->user_id && $vipCode->user_id != $userId) {
            return [
                'valid' => false,
                'message' => 'Ce code VIP n\'est pas pour vous'
            ];
        }

        return [
            'valid' => true,
            'vip_code' => $vipCode,
            'discount_percent' => $vipCode->percent_off
        ];
    }

    /**
     * Utilise un code VIP
     */
    public function useVipCode($code, $userId, $transactionId)
    {
        $validation = $this->validateVipCode($code, $userId);

        if (!$validation['valid']) {
            return $validation;
        }

        $vipCode = $validation['vip_code'];

        // Marquer le code comme utilisé
        $vipCode->update([
            'is_used' => true,
            'used_at' => now(),
            'used_in_transaction' => $transactionId
        ]);

        return [
            'success' => true,
            'discount_percent' => $vipCode->percent_off,
            'message' => 'Code VIP utilisé avec succès'
        ];
    }

    /**
     * Récupère les codes VIP d'un utilisateur
     */
    public function getUserVipCodes($userId)
    {
        return promo::where('user_id', $userId)
            ->where('type', 'vip_code')
            ->where('is_used', false)
            ->where(function ($query) {
                $query->whereNull('valid_until')
                    ->orWhere('valid_until', '>', now());
            })
            ->with('hunting')
            ->get();
    }

    /**
     * Vérifie si une chasse est terminée et attribue les codes VIP
     */
    public function checkAndAssignVipCodes($huntId)
    {
        $hunting = hunting::findOrFail($huntId);
        
        // Vérifier si la chasse est terminée (10 joueurs ont terminé)
        $completedPlayers = \DB::table('enigma_user')
            ->join('enigmas', 'enigma_user.enigma_id', '=', 'enigmas.id')
            ->where('enigmas.hunting_id', $huntId)
            ->whereNotNull('enigma_user.completed_at')
            ->distinct('enigma_user.user_id')
            ->count('enigma_user.user_id');

        if ($completedPlayers >= 10 && !$hunting->vip_codes_assigned) {
            $result = $this->assignVipCodesToNextNine($huntId);
            
            if ($result['success']) {
                $hunting->update(['vip_codes_assigned' => true]);
            }
            
            return $result;
        }

        return [
            'success' => false,
            'message' => 'Chasse non terminée ou codes déjà attribués'
        ];
    }
}

