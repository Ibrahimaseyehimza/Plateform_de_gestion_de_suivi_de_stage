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
        Schema::create('soumissions_maitre_stage', function (Blueprint $table) {
            $table->id();
                 // Entreprise qui soumet
            $table->foreignId('entreprise_id')->constrained('entreprises')->onDelete('cascade');

            //RH qui soumet
            $table->foreignId('rh_id')->constrained('users')->onDelete('cascade');

            // Maître de stage qui reçoit
            $table->foreignId('maitre_stage_id')->constrained('users')->onDelete('cascade');

            // Campagne concernée
            $table->foreignId('campagne_id')->constrained('campagne_de_stages')->onDelete('cascade');

            // Informations
            $table->text('message')->nullable(); // Message du RH au maître de stage
            $table->json('etudiants_ids'); // Liste des IDs des étudiants soumis

            // Statut
            $table->enum('statut', ['en_attente', 'accepte', 'refuse'])->default('en_attente');
            $table->text('commentaire_maitre')->nullable(); // Retour du maître de stage

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soumissions_maitre_stage');
    }
};
