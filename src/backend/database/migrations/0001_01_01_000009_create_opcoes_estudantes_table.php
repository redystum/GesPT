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
        Schema::create('opcoes_estudante', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_estudante')->constrained('estudantes');
            $table->foreignId('id_opcao')->constrained('opcoes');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opcoes_estudante');
    }
};
