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
        // Schema::create('users', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('name');
        //     $table->string('email')->unique();
        //     $table->timestamp('email_verified_at')->nullable();
        //     $table->string('password');
        //     // $table->enum('role', ['admin', 'tuteur', 'etudiant'])->default('etudiant');
        //     // $table->string('role')->default('apprenant');
        //     // $table->foreignId('metier_id')->nullable()->constrained()->onDelete('set null');
        //     // $table->foreignId('departement_id')->nullable()->constrained()->onDelete('set null');

        //     // $table->string('role');
        //     // Table users
        //     $table->enum('role', [
        //         'chef_departement', 'chef_metier', 'maitre_stage', 'rh', 'apprenant'
        //     ]);
        //     // $table->foreignId('departement_id')->nullable()->constrained('departements')->nullOnDelete();
        //     $table->foreignId('metier_id')->nullable()->constrained('metiers')->nullOnDelete();
        //     $table->foreignId('entreprise_id')->nullable()->constrained()->onDelete('set null')->after('departement_id');
        //     $table->boolean('must_change_password')->default(true);


        //     //Contrainte : 1 seul chef par département
        //     $table->unique(['departement_id', 'role'], 'unique_departement_chef')->where('role', 'chef_departement')->default('eit');

        //     //Contrainte : 1 seul chef de métier par métier
        //     $table->unique(['metier_id', 'role'], 'unique_metier_chef')->where('role', 'chef_metier');


        //     $table->rememberToken();
        //     $table->timestamps();
        // });


        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
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
