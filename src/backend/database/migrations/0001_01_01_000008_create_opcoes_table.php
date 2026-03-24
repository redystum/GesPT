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
        Schema::create('opcoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_uc_opcional')->constrained('plano_ucs');
            $table->foreignId('id_uc')->constrained('ucs');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opcoes');
    }
};
