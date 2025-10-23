<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stage_id')->nullable()->constrained('stages')->onDelete('cascade');
            $table->foreignId('maitre_stage_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('etudiant_id')->constrained('users')->onDelete('cascade');
            $table->string('titre');
            $table->text('description')->nullable();
            $table->date('date_echeance')->nullable();
            
            // ✅ Ajout de la priorité
            $table->enum('priorite', ['basse', 'moyenne', 'haute'])->default('moyenne');
            
            // ✅ Ajout de 'en_attente' dans le statut
            $table->enum('statut', ['en_attente', 'en_cours', 'terminee'])->default('en_attente');
            
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index('maitre_stage_id');
            $table->index('etudiant_id');
            $table->index('statut');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taches');
    }
};