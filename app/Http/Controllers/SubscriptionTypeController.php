<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubscriptionType; // SubscriptionType model

class SubscriptionTypeController extends Controller
{
    
   public function index()
{
    // Récupérer tous les types de subscriptions
    $subscriptionTypes = SubscriptionType::all();
    return response()->json($subscriptionTypes);
}

public function store(Request $request)
{
    // Validation des données reçues
    try {
        $request->validate([
            'name' => 'required|string|in:Hebdomadaire,Mensuelle,Annuelle|max:255',
            'price' => 'required|numeric|min:10',
        ], [
            'name.in' => 'Le type de subscription doit être "Hebdomadaire, Mensuelle ou Annuelle".',
            'price.min' => 'Le prix doit être supérieur ou égal à 10.',
        ]);

        // Vérifier si le type existe déjà
        $existingSubscriptionType = SubscriptionType::where('name', $request->name)->first();
        if ($existingSubscriptionType) {
            return response()->json(['message' => 'Ce type de subscription existe déjà'], 400);
        }

        // Créer un nouveau type de subscription
        $subscriptionType = SubscriptionType::create([
            'name' => $request->name,
            'price' => $request->price,
        ]);

        return response()->json($subscriptionType, 201);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        // En cas d'utilisateur non trouvé, retourner une réponse JSON avec un message d'erreur clair
        return response()->json(['error' => 'subscriptionType non trouvé'], 404);
    } catch (\Exception $e) {
        // En cas d'autres erreurs, retourner une réponse JSON avec un message d'erreur général
        return response()->json(['error' => 'Une erreur est survenue lors de la création de subscriptionType',
            'details' => $e->getMessage()], 500);
    }
}

    
    public function show($id)
    {
        // Afficher un type de subscription spécifique
        try{
            $subscriptionType = SubscriptionType::findOrFail($id);
        return response()->json($subscriptionType);
        }
        catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        // En cas type de ticket non trouvé, retourner une réponse JSON avec un message d'erreur clair
        return response()->json(['error' => 'Ce type de SubscriptionType n\'est pas trouvé'], 404);
    } catch (\Exception $e) {
        // En cas d'autres erreurs, retourner une réponse JSON avec un message d'erreur général
        return response()->json(['error' => 'Une erreur est survenue lors de la récupération des détails de SubscriptionType'], 500);
    }
    }

   public function update(Request $request, $id)
{
    try {
        // Trouver le type d'abonnement ou échouer
        $subscriptionType = SubscriptionType::findOrFail($id);

        // Validation des données reçues
        $request->validate([
            'name' => 'required|string|in:Hebdomadaire,Mensuelle,Annuelle|max:255',
            'price' => 'required|numeric|min:10',
        ], [
            'name.in' => 'Le type de subscription doit être Hebdomadaire, Mensuelle ou Annuelle.',
            'price.min' => 'Le prix doit être supérieur ou égal à 10.',
        ]);

        // Vérifier si le nom du type d'abonnement existe déjà (et ne pas autoriser la mise à jour si le nom existe pour un autre type)
        $existingSubscriptionType = SubscriptionType::where('name', $request->name)->where('id', '!=', $id)->first();
        if ($existingSubscriptionType) {
            return response()->json(['error' => 'Ce type de subscriptionType existe déjà',
                                    'La voici:'=>$existingSubscriptionType], 400);
        }

        // Mettre à jour le type d'abonnement
        $subscriptionType->update([
            'name' => $request->name,
            'price' => $request->price,
        ]);

        return response()->json([
            'message' => 'SubscriptionType updated successfully',
            'subscriptionType' => $subscriptionType,
        ]);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        // En cas de type d'abonnement non trouvé, retourner une réponse JSON avec un message d'erreur clair
        return response()->json(['error' => 'SubscriptionType non trouvé'], 404);
    } catch (\Exception $e) {
        // En cas d'autres erreurs, retourner une réponse JSON avec le message d'erreur exact
        return response()->json(['error' => 'Une erreur est survenue lors de la mise à jour du SubscriptionType', 'details' => $e->getMessage()], 500);
    }
}


    public function destroy($id)
    {
        // Supprimer un type de subscription
         try{
                $subscriptionType = SubscriptionType::findOrFail($id);
                $subscriptionType->delete();
                return response()->json(['message' => 'SubscriptionType supprimé avec succès']);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // En cas d'utilisateur non trouvé, retourner une réponse JSON avec un message d'erreur clair
            return response()->json(['error' => 'subscriptionType non trouvé'], 404);
        } catch (\Exception $e) {
            // En cas d'autres erreurs, retourner une réponse JSON avec un message d'erreur général
            return response()->json(['error' => 'Une erreur est survenue lors de la récupération des détails de subscriptionType'], 500);
        }
    }
    }

