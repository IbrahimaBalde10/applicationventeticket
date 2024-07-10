<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;

class TransactionController extends Controller
{
    // Méthode pour lister les transactions de l'utilisateur
    public function index()
    {
        $transactions = auth()->user()->transactions;

        return response()->json([
            'transactions' => $transactions
        ]);
    }

    // Méthode pour afficher les détails d'une transaction spécifique
    public function show($id)
    {
       try{
         $transaction = Transaction::findOrFail($id);
        return response()->json($transaction);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        // En cas type de ticket non trouvé, retourner une réponse JSON avec un message d'erreur clair
        return response()->json(['error' => 'Cette transaction n\'est pas trouvé'], 404);
    } catch (\Exception $e) {
        // En cas d'autres erreurs, retourner une réponse JSON avec un message d'erreur général
        return response()->json(['error' => 'Une erreur est survenue lors de la récupération des détails de transaction'], 500);
    }
}
}