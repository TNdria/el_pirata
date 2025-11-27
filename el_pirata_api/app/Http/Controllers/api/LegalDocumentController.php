<?php

namespace App\Http\Controllers\api;

use App\helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Models\legal_document;
use DB;
use Illuminate\Http\Request;

class LegalDocumentController extends Controller
{
    //

    public function all()
    {
        try {
            $legal = legal_document::select([
                'id',
                'type',
                'slug',
                'title'
            ])->get();
            return response()->json(['list' => $legal]);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }


    public function find(Request $request)
    {
        try {

            $id = $request->id;
            $slug = $request->slug;

            $legal = [];
            if ($id) {
                $legal = legal_document::findOrFail($id);
            }

            if ($slug) {
                $legal = legal_document::where([
                    'slug' => $slug
                ])->firstOrFail();
            }

            return response()->json([
                'state' => 'success',
                'legal' => $legal
            ]);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['state' => 'error', 'message' => 'un erreur c\'est produit', 'user' => []], 200);
        }
    }

    public function createOrupdate(Request $request)
    {
        DB::beginTransaction();

        try {
            $legal_info = $request->legalInfo;
            $queryLegal = legal_document::updateOrCreate(['id' => $legal_info['id']], $legal_info);

            LogHelper::logAction(auth()->user(), $queryLegal, 'update');
            
            DB::commit(); // ✅ Tout s’est bien passé, on valide

            return response()->json([
                'state' => 'success',
                'message' => 'Operation fait avec success',
            ]);

        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack(); // ⛔ Une erreur ? On annule tout

            return response()->json(['state' => 'error', 'message' => 'un erreur c\'est produit'], 200);
        }
    }

}
