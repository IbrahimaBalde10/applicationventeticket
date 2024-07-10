<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminUserController;

use App\Http\Controllers\TicketTypeController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TransactionController; 
use App\Http\Controllers\SubscriptionTypeController; 
use App\Http\Controllers\SubscriptionController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Authentification routes
Route::post('/register', [AuthController::class, 'register']); 
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);


// Modifier le rôle
Route::middleware('auth:sanctum')->put('/users/{id}/role', [AuthController::class, 'updateRole']);

// l'utilisateur connecté édite ses informations
Route::middleware('auth:sanctum')->put('/users/updateProfile', [UserController::class, 'updateProfile']);

// l'utilisateur affiche ses infos
Route::middleware('auth:sanctum')->get('/users/showProfile', [UserController::class, 'showProfile']);

// gestion des utilisateurs par l'admin
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/admin/users', [AdminUserController::class, 'index']);
    Route::get('/admin/users/{id}', [AdminUserController::class, 'show']);
    Route::put('/admin/users/{id}', [AdminUserController::class, 'update']);
    Route::delete('/admin/users/{id}', [AdminUserController::class, 'destroy']);
});

// gestion de type de tickets
// pour tout type d'utilisateur: il peut lister et afficher un type 
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/ticketTypes', [TicketTypeController::class, 'index']);
    Route::get('/ticketTypes/{ticketType}', [TicketTypeController::class, 'show']);
});

// Middleware 'admin' appliqué pour gérer les types de tickets
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('/ticketTypes', [TicketTypeController::class, 'store']);
    Route::put('/ticketTypes/{ticketType}', [TicketTypeController::class, 'update']);
    Route::delete('/ticketTypes/{ticketType}', [TicketTypeController::class, 'destroy']);
});

// route pour créer des tickets et transactions concernés
Route::middleware('auth:sanctum')->post('/tickets/create', [TicketController::class, 'create']);

// routes pour gérer les transactions pour les comptables
Route::middleware('auth:sanctum','comptable')->get('/transactions', [TransactionController::class, 'index']);
Route::middleware('auth:sanctum','comptable')->get('/transactions/{id}', [TransactionController::class, 'show']);


// gestion de type de subscriptionTypes
// pour tout type d'utilisateur
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/subscriptionTypes', [SubscriptionTypeController::class, 'index']);
    Route::get('/subscriptionTypes/{subscriptionType}', [SubscriptionTypeController::class, 'show']);
});
// Middleware 'admin' appliqué pour gérer les subscriptionTypes
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('/subscriptionTypes', [SubscriptionTypeController::class, 'store']);
    Route::put('/subscriptionTypes/{subscriptionType}', [SubscriptionTypeController::class, 'update']);
    Route::delete('/subscriptionTypes/{subscriptionType}', [SubscriptionTypeController::class, 'destroy']);
});

// fin des verifications des erreurs( la suite ...)

// route pour créer des abonnements et transactions concernés
Route::middleware('auth:sanctum')->post('/subscription/create', [SubscriptionController::class, 'create']);

// route pour editer un ticket (test seulement pour le statut)
Route::put('/tickets/{id}', [TicketController::class, 'updateTicket']);

// route pour editer un abonnement (test seulement pour le statut)
Route::put('/subscriptions/{id}', [SubscriptionController::class, 'updateSubscription']);

// verifier le statut d'abonnement du client connecté
Route::middleware('auth:sanctum')->get('/subscriptions/status', [SubscriptionController::class, 'checkSubscriptionStatus']);

// verifier le statut d'abonnement du client via son num Te
Route::middleware('auth:sanctum')->post('/subscriptions/check', [SubscriptionController::class, 'checkSubscriptionStatusTel']);

// Reabonner un client 
Route::middleware('auth:sanctum')->post('/subscriptions/renew', [SubscriptionController::class, 'renewSubscription']);


// test qrCode
// use App\Http\Controllers\QRcodeGenerateController;



// Route::get('/qrCode', [TicketController::class,'qrcode']);