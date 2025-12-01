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
        Schema::disableForeignKeyConstraints();

        Schema::create('bingo_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('game_id')->index();
            $table->foreign('game_id')->references('id')->on('games');
            $table->string('label', 255);
            $table->unsignedInteger('points')->default(1);
            $table->unsignedTinyInteger('position');
            $table->string('icon_path', 255)->nullable();
            $table->timestamps();
            $table->index(['game_id', 'position']);
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bingo_items');
    }
};
