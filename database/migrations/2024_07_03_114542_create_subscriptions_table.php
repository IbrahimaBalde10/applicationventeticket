<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('subscriptions', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id'); // ID de l'utilisateur
        $table->unsignedBigInteger('subscription_type_id'); // ID du type d'abonnement
        $table->date('start_date'); // Date de début de l'abonnement
        $table->date('end_date'); // Date de fin de l'abonnement
        $table->text('qr_code')->nullable(); // Champ pour le code QR
        $table->enum('statut', ['valide', 'expire']); // état de la carte
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('subscription_type_id')->references('id')->on('subscription_types')->onDelete('cascade');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
