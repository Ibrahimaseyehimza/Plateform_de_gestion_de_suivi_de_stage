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
        Schema::create('campagne_stage_entreprise', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campagne_de_stage_id')->constrained('campagne_de_stages')->onDelete('cascade');
            // $table->foreignId('entreprise_id')->constrained()->onDelete('cascade');
                $table->foreignId('entreprise_id')->constrained('entreprises')->onDelete('cascade');
                $table->enum('statut', ['en_attente', 'acceptée', 'refusée'])->default('en_attente');
                // $table->integer('nb_places')->nullable();
                $table->integer('capacite_max')->default(0); // 🧠 Capacité définie par le RH
                $table->integer('postulants_count')->default(0);
                $table->text('message_refus')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campagne_stage_entreprise');
    }
};
