<?php
// UserController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User; // User model

class UserController extends Controller
{
      // Méthode pour afficher les informations de l'utilisateur connecté
    public function showProfile()
    {
        // Récupérer l'utilisateur authentifié
        $user = auth()->user();

        // Retourner une réponse JSON avec les informations de l'utilisateur
        return response()->json($user);
    }
    
    // Méthode pour mettre à jour les informations de l'utilisateur connecté
    public function updateProfile(Request $request)
    {
        // Validation des champs de la requête entrante
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . auth()->id(),
            'telephone' => 'required|string|max:20',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        // Récupérer l'utilisateur authentifié
        $user = auth()->user();

        try {
            // Mettre à jour les informations de l'utilisateur
        $user->nom = $request->nom;
        $user->prenom = $request->prenom;
        $user->email = $request->email;
        $user->telephone = $request->telephone;

        // Vérifier et mettre à jour le mot de passe si spécifié
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Sauvegarder les modifications
        $user->save();
        
        // Retourner une réponse JSON avec un message de succès
        return response()->json(['message' => 'User profile updated successfully']);

        } catch (\Throwable $e) {
            return response()->json(['error' => 'Erreur',
        'error' => $e->getMessage()], 404);
        }
        

    }
}
