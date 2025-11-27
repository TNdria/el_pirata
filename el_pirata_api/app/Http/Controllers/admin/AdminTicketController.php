<?php

namespace App\Http\Controllers\admin;

use App\helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketResponse;
use App\Models\TicketAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminTicketController extends Controller
{
    /**
     * Récupérer tous les tickets (admin)
     */
    public function index(Request $request)
    {
        try {
            $query = Ticket::with(['user', 'admin', 'responses', 'attachments', 'enigma'])
                ->orderBy('created_at', 'desc');

            // Filtres
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('priority')) {
                $query->where('priority', $request->priority);
            }

            if ($request->has('admin_id')) {
                $query->where('admin_id', $request->admin_id);
            }

            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            $tickets = $query->paginate(20);

            return response()->json([
                'state' => 'success',
                'tickets' => $tickets
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de la récupération des tickets',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer un ticket spécifique (admin)
     */
    public function show($id)
    {
        try {
            $ticket = Ticket::with(['user', 'admin', 'responses.admin', 'responses.user', 'attachments', 'enigma'])
                ->findOrFail($id);

            return response()->json([
                'state' => 'success',
                'ticket' => $ticket
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'state' => 'error',
                'message' => 'Ticket non trouvé',
                'error' => $th->getMessage()
            ], 404);
        }
    }

    /**
     * Assigner un ticket à un admin
     */
    public function assign(Request $request, $id)
    {
        $request->validate([
            'admin_id' => 'required|exists:admins,id',
        ]);

        DB::beginTransaction();

        try {
            $ticket = Ticket::findOrFail($id);
            $ticket->assignTo($request->admin_id);
            $ticket->update(['status' => 'in_progress']);

            LogHelper::logAction(auth()->user(), $ticket, 'assign');

            DB::commit();

            return response()->json([
                'state' => 'success',
                'message' => 'Ticket assigné avec succès'
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de l\'assignation du ticket',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Ajouter une réponse admin
     */
    public function addResponse(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $ticket = Ticket::findOrFail($id);

            $response = TicketResponse::create([
                'ticket_id' => $ticket->id,
                'admin_id' => auth()->id(),
                'message' => $request->message . "\n\nL'équipe El Pirata – À votre service, moussaillon !",
            ]);

            // Mettre à jour le statut du ticket
            $ticket->update(['status' => 'in_progress']);

            LogHelper::logAction(auth()->user(), $ticket, 'respond');

            DB::commit();

            return response()->json([
                'state' => 'success',
                'message' => 'Réponse ajoutée avec succès',
                'response' => $response
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de l\'ajout de la réponse',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Fermer un ticket (admin)
     */
    public function close($id)
    {
        DB::beginTransaction();

        try {
            $ticket = Ticket::findOrFail($id);
            $ticket->close();

            LogHelper::logAction(auth()->user(), $ticket, 'close');

            DB::commit();

            return response()->json([
                'state' => 'success',
                'message' => 'Ticket fermé avec succès'
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de la fermeture du ticket',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Rouvrir un ticket (admin)
     */
    public function reopen($id)
    {
        DB::beginTransaction();

        try {
            $ticket = Ticket::findOrFail($id);
            $ticket->reopen();

            LogHelper::logAction(auth()->user(), $ticket, 'reopen');

            DB::commit();

            return response()->json([
                'state' => 'success',
                'message' => 'Ticket rouvert avec succès'
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de la réouverture du ticket',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Statistiques des tickets
     */
    public function stats()
    {
        try {
            $stats = [
                'total' => Ticket::count(),
                'open' => Ticket::where('status', 'open')->count(),
                'in_progress' => Ticket::where('status', 'in_progress')->count(),
                'closed' => Ticket::where('status', 'closed')->count(),
                'urgent' => Ticket::where('priority', 'urgent')->where('status', '!=', 'closed')->count(),
                'normal' => Ticket::where('priority', 'normal')->where('status', '!=', 'closed')->count(),
                'low' => Ticket::where('priority', 'low')->where('status', '!=', 'closed')->count(),
            ];

            return response()->json([
                'state' => 'success',
                'stats' => $stats
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de la récupération des statistiques',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}

