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
    // {
    //     Schema::create('stages', function (Blueprint $table) {
    //         $table->id();
    //         $table->foreignId('etudiant_id')->constrained('users')->onDelete('cascade');
    //         $table->foreignId('entreprise_id')->constrained('entreprises')->onDelete('cascade');
    //         $table->foreignId('tuteur_id')->nullable()->constrained('users')->onDelete('set null');
    //         $table->date('dateDebut');
    //         $table->date('dateFin');
    //         $table->foreignId('campagne_id')->nullable()->constrained('campagnes')->onDelete('cascade');
    //         $table->timestamps();
    //     });
    // }


    {
        Schema::create('stages', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description')->nullable();

            // Dates de stage
            $table->date('date_debut');
            $table->date('date_fin');

            // Statut / état du stage
            $table->enum('etat', ['en attente', 'validé', 'refusé', 'terminé'])
                  ->default('en attente');

            // Relations
            $table->foreignId('campagne_id')
                  ->constrained('campagne_de_stages')
                  ->onDelete('cascade');

            $table->foreignId('entreprise_id')
                  ->constrained('entreprises')
                  ->onDelete('cascade');

            $table->foreignId('etudiant_id')
                  ->constrained('users')
                  ->onDelete('cascade');
                  $table->string('rapport_url')->nullable(); // URL du rapport de stage
                    // $table->string('rapport_path')->nullable();
                    // $table->decimal('note', 5, 2)->nullable();


            $table->timestamps();
        });

    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stages');
    }
};
