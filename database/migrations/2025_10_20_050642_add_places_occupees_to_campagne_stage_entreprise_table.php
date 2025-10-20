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
        Schema::table('campagne_stage_entreprise', function (Blueprint $table) {
            $table->integer('places_occupees')->default(0)->after('capacite_max');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campagne_stage_entreprise', function (Blueprint $table) {
            $table->dropColumn('places_occupees');

        });
    }
};
