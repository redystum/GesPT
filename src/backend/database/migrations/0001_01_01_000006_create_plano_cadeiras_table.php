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
        Schema::create('plano_cadeiras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_plano')->constrained('plano_estudos');
            $table->foreignId('id_uc')->constrained('ucs');
            $table->boolean('opcional')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plano_cadeiras');
    }
};
