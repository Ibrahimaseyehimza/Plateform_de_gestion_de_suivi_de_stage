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
        Schema::create('livrables', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('tache_id')->constrained('taches')->onDelete('cascade');
            // $table->string('fichier');
            // $table->text('commentaire')->nullable();

            $table->foreignId('tache_id')->constrained('taches')->onDelete('cascade');
            $table->foreignId('apprenant_id')->constrained('users')->onDelete('cascade');
            $table->string('titre');
            $table->text('description')->nullable();
            $table->string('fichier')->nullable(); // chemin du fichier
            $table->enum('statut', ['en_attente', 'approuve', 'rejete'])->default('en_attente');
            $table->decimal('note', 4, 2)->nullable();
            $table->text('commentaire')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livrables');
    }
};
