<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveUniqueFromPlanoEstudo extends Migration
{
    public function up()
    {
        Schema::table('plano_estudos', function (Blueprint $table) {
            $table->dropUnique('plano_estudos_ano_id_curso_unique');
        });


    }

    public function down()
    {
        Schema::table('plano_estudos', function (Blueprint $table) {
            $table->dropUnique('plano_estudos_ano_id_curso_unique');
        });
    }
}
