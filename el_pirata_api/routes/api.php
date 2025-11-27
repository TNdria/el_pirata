<?php

use App\Http\Controllers\admin\EnigmasController;
use App\Http\Controllers\admin\HuntingController;
use App\Http\Controllers\admin\PlayerController;
use App\Http\Controllers\admin\RoleController;
use App\Http\Controllers\admin\statController;
use App\Http\Controllers\admin\userAdmin;
use App\Http\Controllers\api\ContactController;
use App\Http\Controllers\api\EnigmasUserController;
use App\Http\Controllers\api\LegalDocumentController;
use App\Http\Controllers\api\LoginController;
use App\Http\Controllers\api\LogsActivitiesController;
use App\Http\Controllers\api\RegisteredUser;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\UserSettingController;
use App\Http\Controllers\api\TicketController;
use App\Http\Controllers\api\RefundController;
use App\Http\Controllers\api\VipCodeController;
use App\Http\Controllers\admin\AdminTicketController;
use App\Http\Controllers\admin\AdminRefundController;
use App\Http\Controllers\admin\AdminVipCodeController;
use App\Http\Controllers\admin\UserBlockingController;
use App\Http\Controllers\admin\AdminTreasureValidationController;
use App\Http\Controllers\admin\AdminTerrainValidationController;
use App\Http\Controllers\admin\AdminHuntManagementController;
use App\Http\Controllers\admin\AdminTiebreakerController;
use App\Http\Controllers\api\HealthController;
use App\Http\Controllers\api\TiebreakerController;
use App\Http\Controllers\api\TreasureValidationController;
use App\Http\Controllers\api\TerrainValidationController;
use App\Http\Controllers\api\CaptchaController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\twoFactorAuthControlloer;
use Illuminate\Support\Facades\Route;

//login user
Route::post('/login', [LoginController::class, 'login'])->middleware(['throttle:5,1', 'verify.captcha:login']);
//login admin
Route::post('/admin/login', [LoginController::class, 'loginAdmin'])->middleware(['throttle:5,1', 'verify.captcha:login']);
//verification de opt
Route::post('/admin/verify/opt', [LoginController::class, 'verifyOTPadmin'])->middleware('throttle:10,1');
//login user par gmail
Route::post('/login/gmail', [LoginController::class, 'authGmail'])->middleware(['throttle:5,1', 'verify.captcha:login']);
//veirifacation mail user
Route::get('/verifed_mail', [LoginController::class, 'verifed_mail'])->middleware(['auth:sanctum']);
// Register
Route::post('/register', [RegisteredUser::class, 'store'])->middleware(['throttle:3,1', 'verify.captcha:register']);
// Cote admin
Route::post('/admin/forgot_password', [userAdmin::class, 'forgotPassword'])->middleware(['verify.captcha:password_reset']);
Route::post('/admin/recovery_password', [userAdmin::class, 'recovery_password']);
Route::get('/hunting/slide', [HuntingController::class, 'getSlideHunter']);
Route::get('/hunting/all', [HuntingController::class, 'getAllHunter']);
//cote client 
Route::get('/hunting/retriew/{id}', [HuntingController::class, 'retriew']);

// contact us
Route::post('/contact/us', [ContactController::class, 'send'])->middleware(['verify.captcha:contact']);

// Health check et monitoring
Route::get('/health', [HealthController::class, 'health']);
Route::get('/metrics', [HealthController::class, 'metrics']);
Route::get('/stats', [HealthController::class, 'stats']);
Route::get('/load-test', [HealthController::class, 'loadTest']);

// Système captcha/anti-bot
Route::post('/captcha/verify', [CaptchaController::class, 'verify']);
Route::post('/captcha/check-verified', [CaptchaController::class, 'checkVerified']);
Route::post('/captcha/math-challenge', [CaptchaController::class, 'generateMathChallenge']);
Route::post('/captcha/math-verify', [CaptchaController::class, 'verifyMathChallenge']);
Route::post('/captcha/math-check-verified', [CaptchaController::class, 'checkMathVerified']);

Route::middleware(['auth:admin', 'throttle:120,1'])->group(
    function () {
        Route::get('/promo/all', [PromoController::class, 'getAllPromo']);

        Route::prefix('admin')->group(
            function () {



                Route::get('/all', [userAdmin::class, 'all']);
                Route::post('/createOrupdate', [userAdmin::class, 'createOrupdate']);
                Route::get('/find/{id}', [userAdmin::class, 'find']);
                Route::post('/archive', [userAdmin::class, 'archive']);
                Route::post('/change_password', [userAdmin::class, 'changePassword']);

                Route::get('/stats', [statController::class, 'getStats']);

                //Double auth
                Route::post('/2fa', [twoFactorAuthControlloer::class, 'authTwoFactorChangeSetting']);
                Route::get('/2fa/check', [twoFactorAuthControlloer::class, 'authTwoFactorCheckSetting']);
                // chasse
                Route::post('/hunting/createOrupdate', [HuntingController::class, 'createOrupdate']);
                Route::get('/hunting/all', [HuntingController::class, 'all']);
                Route::get('/hunting/find/{id}', [HuntingController::class, 'find']);
                Route::post('/hunting/archive', [HuntingController::class, 'archive']);

                // enigme
                Route::get('/enigmas/all', [EnigmasController::class, 'all']);
                Route::post('/enigmas/createOrupdate', [EnigmasController::class, 'createOrupdate']);
                Route::get('/enigmas/find/{id}', [EnigmasController::class, 'find']);
                Route::post('/enigmas/archive', [EnigmasController::class, 'archive']);
                Route::post('/enigmas/user/stat', [EnigmasController::class, 'statEnigmeUser']);

                Route::get('/transactions/all', [TransactionController::class, 'getAllTransactions']);

            }
        );

        Route::post('/promo/save', [PromoController::class, 'createOrupdate']);

        Route::get('/role/all', [RoleController::class, 'all']);

        //document légaux
        Route::get('/legal_document/all', [LegalDocumentController::class, 'all']);
        Route::post('/legal_document/createOrupdate', [LegalDocumentController::class, 'createOrupdate']);

        // Lister les joueurs 
        Route::get('/players/all', [PlayerController::class, 'all']);
        Route::post('/players/archive', [PlayerController::class, 'archive']);

        // Liste logs
        Route::get('/logs/all', [LogsActivitiesController::class, 'all']);

        // Gestion des tickets (admin)
        Route::get('/tickets', [AdminTicketController::class, 'index']);
        Route::get('/tickets/{id}', [AdminTicketController::class, 'show']);
        Route::post('/tickets/{id}/assign', [AdminTicketController::class, 'assign']);
        Route::post('/tickets/{id}/response', [AdminTicketController::class, 'addResponse']);
        Route::post('/tickets/{id}/close', [AdminTicketController::class, 'close']);
        Route::post('/tickets/{id}/reopen', [AdminTicketController::class, 'reopen']);
        Route::get('/tickets/stats', [AdminTicketController::class, 'stats']);

        // Gestion des remboursements (admin)
        Route::get('/refunds', [AdminRefundController::class, 'index']);
        Route::get('/refunds/{id}', [AdminRefundController::class, 'show']);
        Route::post('/refunds/{id}/approve', [AdminRefundController::class, 'approve']);
        Route::post('/refunds/{id}/reject', [AdminRefundController::class, 'reject']);
        Route::post('/refunds/{id}/process', [AdminRefundController::class, 'markAsProcessed']);
        Route::get('/refunds/stats', [AdminRefundController::class, 'stats']);

        // Gestion des codes VIP (admin)
        Route::get('/vip-codes', [AdminVipCodeController::class, 'index']);
        Route::post('/vip-codes/assign', [AdminVipCodeController::class, 'assignVipCodes']);
        Route::post('/vip-codes/check-assign', [AdminVipCodeController::class, 'checkAndAssign']);
        Route::get('/vip-codes/stats', [AdminVipCodeController::class, 'stats']);

        // Gestion du blocage/déblocage des utilisateurs (admin)
        Route::post('/users/block', [UserBlockingController::class, 'block']);
        Route::post('/users/unblock', [UserBlockingController::class, 'unblock']);
        Route::get('/users/blocked', [UserBlockingController::class, 'blockedUsers']);
        Route::get('/users/{id}/blocking-history', [UserBlockingController::class, 'blockingHistory']);
        Route::get('/users/blocking-logs', [UserBlockingController::class, 'blockingLogs']);
        Route::get('/users/blocking-stats', [UserBlockingController::class, 'stats']);

        // Gestion des validations de trésor (admin)
        Route::get('/treasure-validations', [AdminTreasureValidationController::class, 'index']);
        Route::get('/treasure-validations/{id}', [AdminTreasureValidationController::class, 'show']);
        Route::post('/treasure-validations/{id}/approve', [AdminTreasureValidationController::class, 'approve']);
        Route::post('/treasure-validations/{id}/reject', [AdminTreasureValidationController::class, 'reject']);
        Route::get('/treasure-validations/stats', [AdminTreasureValidationController::class, 'stats']);
        Route::get('/treasure-validations/by-hunting/{huntingId}', [AdminTreasureValidationController::class, 'byHunting']);

        // Gestion des codes de validation terrain (admin)
        Route::get('/terrain-codes', [AdminTerrainValidationController::class, 'index']);
        Route::post('/terrain-codes', [AdminTerrainValidationController::class, 'store']);
        Route::get('/terrain-codes/{id}', [AdminTerrainValidationController::class, 'show']);
        Route::put('/terrain-codes/{id}', [AdminTerrainValidationController::class, 'update']);
        Route::post('/terrain-codes/{id}/deactivate', [AdminTerrainValidationController::class, 'deactivate']);
        Route::get('/terrain-codes/{id}/validations', [AdminTerrainValidationController::class, 'getValidations']);
        Route::get('/terrain-codes/stats', [AdminTerrainValidationController::class, 'stats']);

        // Gestion des chasses - Terminer et attribuer les prix (admin)
        Route::post('/hunts/finish', [AdminHuntManagementController::class, 'finishHunt']);
        Route::get('/hunts/{huntingId}/results', [AdminHuntManagementController::class, 'getHuntResults']);
        Route::post('/hunt-results/{resultId}/mark-paid', [AdminHuntManagementController::class, 'markPrizeAsPaid']);
        Route::put('/hunt-results/{resultId}/prize', [AdminHuntManagementController::class, 'updatePrizeAmount']);
        Route::get('/hunts/finished-stats', [AdminHuntManagementController::class, 'getFinishedHuntsStats']);

        // Gestion des défis de départage (admin)
        Route::get('/tiebreaker-challenges', [AdminTiebreakerController::class, 'index']);
        Route::post('/tiebreaker-challenges', [AdminTiebreakerController::class, 'store']);
        Route::get('/tiebreaker-challenges/{id}', [AdminTiebreakerController::class, 'show']);
        Route::put('/tiebreaker-challenges/{id}', [AdminTiebreakerController::class, 'update']);
        Route::post('/tiebreaker-challenges/{id}/deactivate', [AdminTiebreakerController::class, 'deactivate']);
        Route::get('/tiebreaker-challenges/{id}/participations', [AdminTiebreakerController::class, 'getParticipations']);
        Route::get('/tiebreaker-challenges/stats', [AdminTiebreakerController::class, 'stats']);
        Route::post('/tiebreaker-challenges/create-for-tiebreaker', [AdminTiebreakerController::class, 'createForTiebreaker']);
    }
);

//document légaux recherche
Route::post('/legal_document/find', [LegalDocumentController::class, 'find']);

// preview enigme
Route::post('/enigmas/preview', [EnigmasUserController::class, 'preview']);



// cote utilisateur 
Route::middleware(['auth:sanctum', 'last.activity', 'check.user.blocked', 'throttle:60,1'])->group(
    function () {
        Route::get('/transactions/me', [TransactionController::class, 'getMyTransactions']);
        Route::post('/transactions/add/', [TransactionController::class, 'paymentHunt']);
        Route::post('/check/hunt', [TransactionController::class, 'checkIfPayment']);

        Route::get('/hunt/result/{huntId}', [HuntingController::class, 'getResultatHunt']);
        Route::get('/me/hunt/result', [HuntingController::class, 'getMyResultatHunt']);
        Route::get('/my/hunt', [HuntingController::class, 'MyHunt']);
        
        Route::get('/promo/check/{code}', [PromoController::class, 'checkCodePromo']);

        Route::get('/check-me', function (Request $request) {
            try {
                // On vérifie si l'utilisateur est authentifié
                $user = auth()->user();

                if (!$user) {
                    return response()->json([
                        'message' => 'Utilisateur non authentifié'
                    ], 401);
                }

                return response()->json([
                    'user' => $user,
                    'message' => 'Token valide, utilisateur connecté'
                ]);
            } catch (\Exception $e) {
                // En cas d'erreur quelconque
                return response()->json([
                    'message' => 'Erreur serveur : ' . $e->getMessage()
                ], 500);
            }
        });

        Route::prefix('user')->group(
            function () {
                // Engime
                Route::post('/enigmas/getEnigma', [EnigmasUserController::class, 'getEnigma']);
                Route::post('/enigmas/info', [EnigmasUserController::class, 'getInfoEnigmeByID']);
                Route::post('/enigmas/check/answer', [EnigmasUserController::class, 'answerCheck']);
                Route::get('/enigmas/code/list', [EnigmasUserController::class, 'getUserCompletedEnigmaCodes']);
                Route::post('/update', [UserController::class, 'update']);
                Route::post('/avatar/update', [UserController::class, 'changeAvatar']);
                Route::post('/change_password', [userAdmin::class, 'changePassword']);
                Route::post('/me/delete', [UserController::class, 'archive']);
                Route::get('/setting/find', [UserSettingController::class, 'find']);
                Route::post('/setting/createOrupdate', [UserSettingController::class, 'createOrupdate']);

                // mes promo
                Route::get('/me/promo', [PromoController::class, 'getUserPromo']);

                // Tickets de support
                Route::get('/tickets', [TicketController::class, 'index']);
                Route::post('/tickets', [TicketController::class, 'store']);
                Route::get('/tickets/{id}', [TicketController::class, 'show']);
                Route::post('/tickets/{id}/response', [TicketController::class, 'addResponse']);
                Route::post('/tickets/{id}/close', [TicketController::class, 'close']);
                Route::post('/tickets/{id}/reopen', [TicketController::class, 'reopen']);

                // Demandes de remboursement
                Route::get('/refunds', [RefundController::class, 'index']);
                Route::post('/refunds', [RefundController::class, 'store']);
                Route::get('/refunds/{id}', [RefundController::class, 'show']);
                Route::get('/refunds/check-eligibility/{transactionId}', [RefundController::class, 'checkEligibility']);

                // Codes VIP
                Route::get('/vip-codes', [VipCodeController::class, 'index']);
                Route::post('/vip-codes/validate', [VipCodeController::class, 'validate']);
                Route::post('/vip-codes/use', [VipCodeController::class, 'use']);

                // Validation de trésor pour chasses physiques
                Route::get('/treasure-validations', [TreasureValidationController::class, 'index']);
                Route::post('/treasure-validations', [TreasureValidationController::class, 'store']);
                Route::get('/treasure-validations/{id}', [TreasureValidationController::class, 'show']);
                Route::post('/treasure-validations/can-submit', [TreasureValidationController::class, 'canSubmit']);

                // Validation de codes terrain avec géolocalisation
                Route::post('/terrain-validation/validate', [TerrainValidationController::class, 'validateCode']);
                Route::get('/terrain-validation/codes', [TerrainValidationController::class, 'getAvailableCodes']);
                Route::get('/terrain-validation/history', [TerrainValidationController::class, 'getValidationHistory']);
                Route::post('/terrain-validation/can-validate', [TerrainValidationController::class, 'canValidate']);

                // Système de défis pour départager les ex-aequo
                Route::get('/tiebreaker/challenges', [TiebreakerController::class, 'getAvailableChallenges']);
                Route::get('/tiebreaker/challenges/{id}', [TiebreakerController::class, 'getChallenge']);
                Route::post('/tiebreaker/challenges/{id}/answer', [TiebreakerController::class, 'submitAnswer']);
                Route::get('/tiebreaker/challenges/{id}/leaderboard', [TiebreakerController::class, 'getLeaderboard']);
                Route::get('/tiebreaker/my-participations', [TiebreakerController::class, 'getMyParticipations']);

            }
        );
    }
);