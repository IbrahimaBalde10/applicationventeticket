<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TicketType; // TicketType model


class TicketTypeController extends Controller
{
     public function index()
    {
        // Récupérer tous les types de tickets
        $ticketTypes = TicketType::all();
        return response()->json($ticketTypes);
    }

    public function store(Request $request)
    {
        try{
        // Validation des données reçues
         $request->validate([
        'name' => 'required|string|in:Aller simple,Aller retour|max:255',
        'price' => 'required|numeric',
    ], [
        'name.in' => 'Le type de ticket doit être "Aller simple" ou "Aller retour".'
    ]);


         // Vérifier si le type de ticket existe déjà
        $existingTicketType = TicketType::where('name', $request->name)->first();

        if ($existingTicketType) {
            return response()->json(['message' => 'Ce type de ticket existe déjà'], 400);
        }
        // Créer un nouveau type de ticket
        $ticketType = TicketType::create([
            'name' => $request->name,
            'price' => $request->price,
        ]);

        return response()->json($ticketType, 201);
    }catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        // En cas d'utilisateur non trouvé, retourner une réponse JSON avec un message d'erreur clair
        return response()->json(['error' => 'Type non trouvé'], 404);
    } catch (\Exception $e) {
        // En cas d'autres erreurs, retourner une réponse JSON avec un message d'erreur général
        return response()->json(['error' => 'Une erreur est survenue lors de la récupération des détails du type '
    , 'details' => $e->getMessage()], 500);
    }
    }
    public function show($id)
    {
        // Afficher un type de ticket spécifique
        
     try{
         $ticketType = TicketType::findOrFail($id);
        return response()->json($ticketType);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        // En cas type de ticket non trouvé, retourner une réponse JSON avec un message d'erreur clair
        return response()->json(['error' => 'Ce type de ticket n\'est pas trouvé'], 404);
    } catch (\Exception $e) {
        // En cas d'autres erreurs, retourner une réponse JSON avec un message d'erreur général
        return response()->json(['error' => 'Une erreur est survenue lors de la récupération des détails de ticketTypeid'], 500);
    }
    }

   public function update(Request $request, $id)
{
    try {
        // Trouver le type de ticket ou échouer
        $ticketType = TicketType::findOrFail($id);

        // Validation des données reçues
        $request->validate([
            'name' => 'required|string|in:Aller simple,Aller retour|max:255',
            'price' => 'required|numeric',
        ], [
            'name.in' => 'Le type de ticket doit être "Aller simple" ou "Aller retour".'
        ]);

        // // Vérifier si le nom du type de ticket existe déjà (et ne pas autoriser la mise à jour si le nom existe pour un autre type)
        // $existingTicketType = TicketType::where('name', $request->name)->where('id', '!=', $id)->first();
        // if ($existingTicketType) {
        //     return response()->json(['error' => 'Ce type de ticket existe déjà'], 400);
        // }

        // Mettre à jour le type de ticket
        $ticketType->update([
            'name' => $request->name,
            'price' => $request->price,
        ]);

       return response()->json(['message' => 'ticketType updated successfully',
        'ticketType'=>$ticketType]);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        // En cas de ticketType non trouvé, retourner une réponse JSON avec un message d'erreur clair
        return response()->json(['error' => 'TicketType non trouvé'], 404);
    } catch (\Exception $e) {
        // En cas d'autres erreurs, retourner une réponse JSON avec le message d'erreur exact
        return response()->json(['error' => 'Une erreur est survenue lors de la mise à jour du ticketType', 
        'details' => $e->getMessage()], 500);
    }
}


    public function destroy($id)
    {
        // Supprimer un type de ticket
        try{
                $ticketType = TicketType::findOrFail($id);
                $ticketType->delete();
                return response()->json(['message' => 'Type de ticket supprimé avec succès']);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // En cas d'utilisateur non trouvé, retourner une réponse JSON avec un message d'erreur clair
            return response()->json(['error' => 'ticketType non trouvé'], 404);
        } catch (\Exception $e) {
            // En cas d'autres erreurs, retourner une réponse JSON avec un message d'erreur général
            return response()->json(['error' => 'Une erreur est survenue lors de la récupération des détails de ticketType'], 500);
        }
    }
}
