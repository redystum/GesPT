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
        Schema::table('ucs', function (Blueprint $table) {
            $table->float('horas_t')->default(0);
            $table->float('horas_tp')->default(0);
            $table->float('horas_pl')->default(0);
            $table->float('horas_tc')->default(0);
            $table->float('horas_s')->default(0);
            $table->float('horas_e')->default(0);
            $table->float('horas_ot')->default(0);
            $table->float('horas_o')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ucs', function (Blueprint $table) {
            $table->integer('horas_t');
            $table->dropColumn('horas_tp');
            $table->dropColumn('horas_pl');
            $table->dropColumn('horas_tc');
            $table->dropColumn('horas_s');
            $table->dropColumn('horas_e');
            $table->dropColumn('horas_ot');
            $table->dropColumn('horas_o');
        });
    }
};
