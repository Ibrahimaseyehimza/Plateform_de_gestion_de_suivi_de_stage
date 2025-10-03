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
        Schema::create('campagne_stage_metier', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campagne_de_stage_id')->constrained('campagne_de_stages')->onDelete('cascade');
            $table->foreignId('metier_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campagne_stage_metier');
    }
};
