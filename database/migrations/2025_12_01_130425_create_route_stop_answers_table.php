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

        Schema::create('route_stop_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('game_player_id')->index();
            $table->foreign('game_player_id')->references('id')->on('game_players');
            $table->unsignedBigInteger('route_stop_id')->index();
            $table->foreign('route_stop_id')->references('id')->on('route_stops');
            $table->enum('chosen_option', ["A","B","C","D"]);
            $table->boolean('is_correct')->default(false);
            $table->unsignedInteger('score_awarded');
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();
            $table->unique(['game_player_id', 'route_stop_id']);
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('route_stop_answers');
    }
};
