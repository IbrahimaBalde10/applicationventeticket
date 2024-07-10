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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id'); // ID de la transaction associée
            $table->unsignedBigInteger('ticket_type_id'); // ID du type de ticket acheté
            $table->text('qr_code')->nullable(); // Champ pour le code QR
            $table->enum('statut', ['valide', 'expire']); // état du ticket
            $table->timestamp('purchase_date')->nullable();
            $table->timestamp('expiration_date')->nullable();
            $table->timestamps();
            $table->foreign('transaction_id')->references('id')->on('transactions');
            $table->foreign('ticket_type_id')->references('id')->on('ticket_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
