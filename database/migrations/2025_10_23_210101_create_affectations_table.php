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
        Schema::create('affectations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etudiant_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('entreprise_id')->constrained('entreprises')->onDelete('cascade');
            $table->foreignId('maitre_stage_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('campagne_id')->nullable()->constrained('campagne_de_stages')->onDelete('set null');
            $table->foreignId('metier_id')->nullable()->constrained('metiers')->onDelete('set null');
            $table->enum('statut', ['en_attente', 'accepte', 'refuse', 'en_cours', 'termine'])->default('en_attente');
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->text('commentaire')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affectations');
    }
};