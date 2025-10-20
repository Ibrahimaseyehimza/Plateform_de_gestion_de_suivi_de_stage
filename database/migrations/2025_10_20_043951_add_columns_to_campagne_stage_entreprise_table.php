<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('campagne_stage_entreprise', function (Blueprint $table) {
            // Vérifier si les colonnes n'existent pas déjà avant de les ajouter
            if (!Schema::hasColumn('campagne_stage_entreprise', 'statut')) {
                $table->string('statut')->default('en_attente')->after('entreprise_id');
            }
            
            if (!Schema::hasColumn('campagne_stage_entreprise', 'capacite_max')) {
                $table->integer('capacite_max')->nullable()->after('statut');
            }
            
            if (!Schema::hasColumn('campagne_stage_entreprise', 'message_refus')) {
                $table->text('message_refus')->nullable()->after('capacite_max');
            }
            
            if (!Schema::hasColumn('campagne_stage_entreprise', 'postulants_count')) {
                $table->integer('postulants_count')->default(0)->after('message_refus');
            }
            
            // Ajouter les timestamps si ils n'existent pas
            if (!Schema::hasColumn('campagne_stage_entreprise', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    public function down()
    {
        Schema::table('campagne_stage_entreprise', function (Blueprint $table) {
            $table->dropColumn([
                'statut',
                'capacite_max',
                'message_refus',
                'postulants_count',
                'created_at',
                'updated_at'
            ]);
        });
    }
};