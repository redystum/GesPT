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
        Schema::create('plano_estudos', function (Blueprint $table) {
            $table->id();
            $table->string('ano');
            $table->foreignId('id_curso')->constrained('cursos');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('plano_estudos', function (Blueprint $table) {
            $table->unique(['ano', 'id_curso'], 'plano_estudos_ano_id_curso_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plano_estudos');

        Schema::table('plano_estudos', function (Blueprint $table) {
            $table->dropUnique('plano_estudos_ano_id_curso_unique');
        });
    }
};
