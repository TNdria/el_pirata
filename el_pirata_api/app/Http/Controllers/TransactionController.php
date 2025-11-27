<?php

namespace App\Http\Controllers;

use App\Models\payment_types;
use App\Models\transactions;
use Date;
use DB;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    //
    public function paymentHunt(Request $request)
    {
        DB::beginTransaction();

        try {
            $user = auth()->user();
            $stripe_transaction_id = $request->stripe_transaction_id;
            $item = $request->item ?? [];

            $paymentType = payment_types::where('code', 'ticket_purchase')->first();

            foreach ($item as $value) {
                transactions::create([
                    "stripe_transaction_id" => $stripe_transaction_id,
                    'payment_type_id' => $paymentType->id,
                    "amount_paid" => $value['registration_fee'],
                    "user_id" => $user->id,
                    "hunt_id" => $value['id'],
                    "promo_code" => $value['promo_code'],
                    "status" => "validated"
                ]);
            }

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

    public function getMyTransactions(Request $request)
    {
        try {
            $user = auth()->user();
            $userTransaction = $user->transactions()->get();
            return response()->json([
                'status' => true,
                'user_transaction' => $userTransaction,
            ]);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function checkIfPayment(Request $request)
    {
        try {
            $user = auth()->user();
            $huntId = $request->hund_id;

            $userHasPurchasedThisHunt = $user->transactions()
                ->where('hunt_id', $huntId)
                ->whereHas('payment_type', fn($q) => $q->where('code', 'ticket_purchase'))
                ->where('status', 'validated')
                ->exists();

            if ($userHasPurchasedThisHunt) {
                return response()->json([
                    'status' => true,
                    'message' => 'Vous avez déjà acheté cette chasse.',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Vous n\'avez pas encore acheté cette chasse. Elle a été ajoutée à votre panier.',
                ]);
            }

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Vous n\'avez pas encore acheté cette chasse. Elle a été ajoutée à votre panier.',
            ]);
        }
    }

    public function getAllTransactions()
    {
        try {
            $transactions = transactions::with(['user', 'hunting', 'payment_type'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'state' => 'success',
                'data' => $transactions
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'state' => 'error',
                'message' => 'Une erreur est survenue',
                'error' => $th->getMessage()
            ], 500);
        }
    }

}
