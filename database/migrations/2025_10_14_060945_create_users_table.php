<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     */
    public function up(): void
    {

             // Supprimer la table users si elle existe
        Schema::dropIfExists('users');

          Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // $table->string('prenom')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('matricule')->nullable()->change();
            $table->string('password');
            $table->enum('role', [
                'chef_departement', 'chef_metier', 'maitre_stage', 'rh', 'apprenant'
            ]);
            // $table->foreignId('departement_id')->nullable()->constrained('departements')->nullOnDelete();
            $table->foreignId('metier_id')->nullable()->constrained('metiers')->nullOnDelete();
            $table->foreignId('entreprise_id')->nullable()->constrained()->nullOnDelete();

            $table->boolean('must_change_password')->default(true);

            $table->rememberToken();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
          Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('must_change_password');
            $table->dropConstrainedForeignId('departement_id');
            $table->dropConstrainedForeignId('metier_id');
        });
    }
};

