<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->unsignedInteger('bingo_three_in_row_points')->default(50)->after('game_modes');
            $table->unsignedInteger('bingo_full_card_points')->default(100)->after('bingo_three_in_row_points');
        });
    }

    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn(['bingo_three_in_row_points', 'bingo_full_card_points']);
        });
    }
};
