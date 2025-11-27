<?php

namespace App\Http\Controllers\api;

use App\helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketResponse;
use App\Models\TicketAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TicketController extends Controller
{
    /**
     * Créer un nouveau ticket
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'required|in:urgent,normal,low',
            'enigma_id' => 'nullable|exists:enigmas,id',
        ]);

        DB::beginTransaction();

        try {
            $ticket = Ticket::create([
                'user_id' => auth()->id(),
                'subject' => $request->subject,
                'message' => $request->message,
                'priority' => $request->priority,
                'enigma_id' => $request->enigma_id,
                'status' => 'open',
            ]);

            // Gérer les pièces jointes si présentes
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('ticket-attachments', 'public');
                    
                    TicketAttachment::create([
                        'ticket_id' => $ticket->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'state' => 'success',
                'message' => 'Ticket créé avec succès',
                'ticket' => $ticket->load(['attachments', 'enigma'])
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'state' => 'error',
                'message' => 'Erreur lors de la création du ticket',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les tickets de l'utilisateur
     */
    public function index(Request $request)
    {
        try {
            $query = Ticket::where('user_id', auth()->id())
                ->with(['responses.admin', 'attachments', 'enigma'])
                ->orderBy('created_at', 'desc');

            // Filtres
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('priority')) {
                $query->where('priority', $request->priority);
            }

            $tickets = $query->paginate(15);

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
     * Récupérer un ticket spécifique
     */
    public function show($id)
    {
        try {
            $ticket = Ticket::where('user_id', auth()->id())
                ->with(['responses.admin', 'attachments', 'enigma'])
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
     * Ajouter une réponse à un ticket
     */
    public function addResponse(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $ticket = Ticket::where('user_id', auth()->id())->findOrFail($id);

            // Vérifier que le ticket n'est pas fermé
            if ($ticket->status === 'closed') {
                return response()->json([
                    'state' => 'error',
                    'message' => 'Impossible d\'ajouter une réponse à un ticket fermé'
                ], 400);
            }

            $response = TicketResponse::create([
                'ticket_id' => $ticket->id,
                'user_id' => auth()->id(),
                'message' => $request->message,
            ]);

            // Mettre à jour le statut du ticket
            if ($ticket->status === 'in_progress') {
                $ticket->update(['status' => 'open']);
            }

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
     * Fermer un ticket
     */
    public function close($id)
    {
        DB::beginTransaction();

        try {
            $ticket = Ticket::where('user_id', auth()->id())->findOrFail($id);
            $ticket->close();

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
     * Rouvrir un ticket
     */
    public function reopen($id)
    {
        DB::beginTransaction();

        try {
            $ticket = Ticket::where('user_id', auth()->id())->findOrFail($id);
            $ticket->reopen();

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
}

