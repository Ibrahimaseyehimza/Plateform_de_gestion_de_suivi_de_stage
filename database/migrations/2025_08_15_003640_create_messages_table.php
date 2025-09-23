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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediteur_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('destinataire_id')->constrained('users')->onDelete('cascade');
            $table->text('contenu');
            $table->timestamp('date')->useCurrent();
            $table->boolean('lu')->default(false); // Pour marquer si le message est lu
            $table->timestamps();

            // Index pour optimiser les requêtes
            $table->index(['expediteur_id', 'destinataire_id']);


            // $table->foreign('expediteur_id')->references('id')->on('utilisateurs')->onDelete('cascade');
            // $table->foreign('destinataire_id')->references('id')->on('utilisateurs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
