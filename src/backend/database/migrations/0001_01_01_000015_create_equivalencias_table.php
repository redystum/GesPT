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
        Schema::create('equivalencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_rule')->nullable()->constrained('equivalencia_rules');
            $table->foreignId('id_plano_uc')->nullable()->constrained('plano_ucs');
            $table->enum('lado', ['Original', 'Final']);
            $table->text('custom')->nullable(); // if UC is null

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equivalencias');
    }
};
