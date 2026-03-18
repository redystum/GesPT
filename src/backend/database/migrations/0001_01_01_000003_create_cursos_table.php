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
        Schema::create('cursos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('sigla');
            $table->string('codigo');
            $table->foreignId('id_curso_parente')->nullable()->constrained('cursos');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('cursos', function (Blueprint $table) {
            $table->unique('sigla');
            $table->unique('codigo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cursos');

        Schema::table('cursos', function (Blueprint $table) {
            $table->dropUnique(['sigla']);
            $table->dropUnique(['codigo']);
        });
    }
};
