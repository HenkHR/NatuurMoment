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

        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('game_id')->index();
            $table->foreign('game_id')->references('id')->on('games');
            $table->unsignedBigInteger('game_player_id')->index();
            $table->foreign('game_player_id')->references('id')->on('game_players');
            $table->unsignedBigInteger('bingo_item_id')->index();
            $table->foreign('bingo_item_id')->references('id')->on('bingo_items');
            $table->string('path', 255);
            $table->enum('status', ["pending","approved","rejected"])->index();
            $table->timestamp('taken_at')->nullable();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
};
