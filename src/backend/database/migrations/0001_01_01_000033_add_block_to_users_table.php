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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('blocked')->default(false)->after('type');
            $table->text('blocked_reason')->nullable()->after('blocked');
            $table->timestamp('blocked_at')->nullable()->after('blocked_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('blocked');
            $table->dropColumn('blocked_reason');
            $table->dropColumn('blocked_at');
        });
    }
};
