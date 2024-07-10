<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\SubscriptionType;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;


class SubscriptionController extends Controller
{
    
    // Méthode pour créer un abonnement
public function create(Request $request)
{
    // Validation des données entrantes
    $request->validate([
        'subscription_type_id' => 'required|exists:subscription_types,id',
        // Vous pouvez activer la validation des dates si elles sont nécessaires
        // 'start_date' => 'required|date',
        // 'end_date' => 'required|date|after:start_date',
    ]);

    // Récupérer les informations sur le type d'abonnement
    $subscriptionType = SubscriptionType::findOrFail($request->subscription_type_id);
    $price = $subscriptionType->price;

    // Créer une nouvelle transaction pour l'abonnement
    $transaction = new Transaction();
    $transaction->user_id = auth()->id();
    $transaction->total_amount = $price;
    $transaction->quantity = 1; // Un abonnement est unique
    $transaction->price = $price;
    $transaction->transaction_name = 'subscription';
    $transaction->subscription_type_id = $subscriptionType->id;
    // $transaction->calculateEndDate($subscriptionType->name); // Utilisation du nom du type d'abonnement
    $transaction->save();

    // Créer l'abonnement correspondant
    $subscription = new Subscription();
    $subscription->user_id = auth()->id();
    $subscription->subscription_type_id = $subscriptionType->id;
    $subscription->calculateEndDate($subscriptionType->name); // Utilisation du nom du type d'abonnement
    $subscription->updateStatut(); // Mettre à jour le statut de l'abonnement
    $subscription->save();

    return response()->json([
        'message' => 'Subscription created successfully',
        'subscription' => $subscription,
        'transaction' => $transaction,
        'Abonnement:' => $subscriptionType->name
    ]);
}



    // Méthode pour afficher les abonnements d'un utilisateur
    public function index()
    {
        $subscriptions = Subscription::where('user_id', auth()->id())->get();

        return response()->json($subscriptions);
    }

    // Méthode pour afficher les détails d'un abonnement spécifique
    public function show($id)
    {
        $subscription = Subscription::findOrFail($id);

        if ($subscription->user_id != auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($subscription);
    }

    // Méthode pour mettre à jour un abonnement
    public function update(Request $request, $id)
    {
        $subscription = Subscription::findOrFail($id);

        if ($subscription->user_id != auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'subscription_type_id' => 'required|exists:subscription_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $subscription->subscription_type_id = $request->subscription_type_id;
        $subscription->start_date = $request->start_date;
        $subscription->end_date = $request->end_date;
        $subscription->save();

        return response()->json([
            'message' => 'Subscription updated successfully',
            'subscription' => $subscription,
        ]);
    }

    // Méthode pour supprimer un abonnement
    public function destroy($id)
    {
        $subscription = Subscription::findOrFail($id);

        if ($subscription->user_id != auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $subscription->delete();

        return response()->json(['message' => 'Subscription deleted successfully']);
    }


    // Méthode pour mettre à jour un abonnement
    public function updateSubscription(Request $request, $id)
    {
       // Récupérer l'abonnement à mettre à jour
        $subscription = Subscription::findOrFail($id);

        // Mettre à jour les attributs de l'abonnement
        if ($request->has('end_date')) {
            $subscription->end_date = Carbon::parse($request->input('end_date'));
        }

        // Appel de la méthode pour mettre à jour le statut de l'abonnement
        $subscription->updateStatut();

        // Enregistrer les modifications de l'abonnement
        $subscription->save();

        // Retourner une réponse JSON avec le message de succès et les détails de l'abonnement mis à jour
        return response()->json([
            'message' => 'Abonnement updated successfully',
            'subscription' => $subscription,
        ]);

    }

// methode permettant aux abonnées de verifier le statut de leur abonnement
public function checkSubscriptionStatus(Request $request)
{
    $user = auth()->user();

    $subscription = Subscription::where('user_id', $user->id)->latest()->first();

    if ($subscription) {
        $subscription->updateStatut(); // Mettez à jour le statut avant de renvoyer la réponse
        return response()->json([
            'status' => $subscription->statut,
            'end_date' => $subscription->end_date,
            // 'status' => $subscription->su
            'Abonnement:' => $subscription->subscription_type_id
        ]);
    }

    return response()->json([
        'message' => 'No subscription found'
    ], 404);
}

// verifier le satut de l'abonnement du client via son num telephone
public function checkSubscriptionStatusTel(Request $request)
{
    $request->validate([
        'telephone' => 'required',
    ]);

    $user = User::where('telephone', $request->telephone)->first();

    if (!$user) {
        return response()->json([
            'message' => 'User not found'
        ], 404);
    }

    $subscription = Subscription::where('user_id', $user->id)->latest()->first();

    if ($subscription) {
        $subscription->updateStatut(); // Mettez à jour le statut avant de renvoyer la réponse
        return response()->json([
            'status' => $subscription->statut,
            'end_date' => $subscription->end_date
        ]);
    }

    return response()->json([
        'message' => 'No subscription found'
    ], 404);
}

//Reabonner un client via son Tel et type d'abonnement indique
public function renewSubscription(Request $request)
{
    $request->validate([
        'telephone' => 'required',
        'subscription_type_id' => 'required|exists:subscription_types,id'
    ]);

    $user = User::where('telephone', $request->telephone)->first();

    if (!$user) {
        return response()->json([
            'message' => 'User not found'
        ], 404);
    }

    $subscription = Subscription::where('user_id', $user->id)->latest()->first();

    if ($subscription && $subscription->statut == 'valide') {
        return response()->json([
            'message' => 'Subscription is still valid',
            'end_date' => $subscription->end_date
        ]);
    }

    // Créer une nouvelle transaction pour le réabonnement
    $subscriptionType = SubscriptionType::findOrFail($request->subscription_type_id);
    $price = $subscriptionType->price;

    $transaction = new Transaction();
    $transaction->user_id = $user->id;
    $transaction->total_amount = $price;
    $transaction->quantity = 1;
    $transaction->price = $price;
    $transaction->transaction_name = 'subscription';
    $transaction->save();

    // Créer le réabonnement
    $newSubscription = new Subscription();
    $newSubscription->user_id = $user->id;
    $newSubscription->subscription_type_id = $subscriptionType->id;
    $newSubscription->calculateEndDate($subscriptionType->name);
    $newSubscription->updateStatut();
    $newSubscription->save();

    return response()->json([
        'message' => 'Subscription renewed successfully',
        'subscription' => $newSubscription
    ]);
}

}
