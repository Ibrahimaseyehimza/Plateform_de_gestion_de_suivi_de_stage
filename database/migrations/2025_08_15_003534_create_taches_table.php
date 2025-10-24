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
            $table->string('titre');
            $table->text('description')->nullable();
            $table->foreignId('apprenant_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('stage_id')->nullable()->constrained('stages')->onDelete('cascade');
            $table->foreignId('maitre_stage_id')->constrained('users')->onDelete('cascade');
            $table->date('date_echeance')->nullable();
                    $table->foreignId('entreprise_id')
              ->nullable()
              ->constrained('entreprises')
              ->onDelete('cascade');
            $table->enum('statut', ['en_cours', 'terminee'])->default('en_cours');
            $table->timestamps();

            // Index pour optimiser les requêtes
            $table->index('maitre_stage_id');
            // $table->index('etudiant_id');
            $table->index('statut');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taches');
    }
};
