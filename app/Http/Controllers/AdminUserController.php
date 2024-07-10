<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // User model

class AdminUserController extends Controller
{
    // Liste tous les utilisateurs
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

   // Afficher les détails d'un utilisateur
public function show($id)
{
    try {
        // Trouver l'utilisateur par son ID
        $user = User::findOrFail($id);

        // Retourner les détails de l'utilisateur en JSON
        return response()->json($user);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        // En cas d'utilisateur non trouvé, retourner une réponse JSON avec un message d'erreur clair
        return response()->json(['error' => 'Utilisateur non trouvé'], 404);
    } catch (\Exception $e) {
        // En cas d'autres erreurs, retourner une réponse JSON avec un message d'erreur général
        return response()->json(['error' => 'Une erreur est survenue lors de la récupération des détails de l\'utilisateur'], 500);
    }
}


    // Mettre à jour les informations d'un utilisateur
    public function update(Request $request, $id)
    {
        try {
             $user = User::findOrFail($id);
        $user->update($request->all());
        return response()->json(['message' => 'User updated successfully']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        // En cas d'utilisateur non trouvé, retourner une réponse JSON avec un message d'erreur clair
        return response()->json(['error' => 'Utilisateur non trouvé'], 404);
    } catch (\Exception $e) {
        // En cas d'autres erreurs, retourner une réponse JSON avec un message d'erreur général
        return response()->json(['error' => 'Une erreur est survenue lors de la récupération des détails de l\'utilisateur'], 500);
    }
    }

    // Supprimer un utilisateur
    public function destroy($id)
    {
        try{
               $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        // En cas d'utilisateur non trouvé, retourner une réponse JSON avec un message d'erreur clair
        return response()->json(['error' => 'Utilisateur non trouvé'], 404);
    } catch (\Exception $e) {
        // En cas d'autres erreurs, retourner une réponse JSON avec un message d'erreur général
        return response()->json(['error' => 'Une erreur est survenue lors de la récupération des détails de l\'utilisateur'], 500);
    }

    }
}
