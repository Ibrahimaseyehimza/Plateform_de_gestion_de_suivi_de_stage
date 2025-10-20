<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stage;

class MaitreStageController extends Controller
{
    public function getStages(Request $request)
    {
        try {
            $user = $request->user();
            
            // Version simple sans relations pour tester
            $stages = Stage::where('maitre_stage_id', $user->id)->get();
            
            return response()->json([
                'success' => true,
                'data' => $stages
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }
    
    public function downloadRapport($id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Fonctionnalité en développement'
        ], 501);
    }
    
    public function updateNote(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'note' => 'required|numeric|min:0|max:20'
            ]);
            
            $stage = Stage::findOrFail($id);
            $stage->note = $validated['note'];
            $stage->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Note enregistrée'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}