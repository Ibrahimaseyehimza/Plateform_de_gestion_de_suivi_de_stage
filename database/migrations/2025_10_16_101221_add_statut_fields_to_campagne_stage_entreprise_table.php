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
            $table->enum('statut', ['en_attente', 'acceptee', 'refusee'])
                ->default('en_attente')
                ->after('entreprise_id');

            $table->timestamp('validated_at')
                ->nullable()
                ->after('statut');

            $table->foreignId('validated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->after('validated_at');
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campagne_stage_entreprise', function (Blueprint $table) {
            $table->dropForeign(['validated_by']);
            $table->dropColumn(['statut', 'validated_at', 'validated_by']);
        });
    }
};
