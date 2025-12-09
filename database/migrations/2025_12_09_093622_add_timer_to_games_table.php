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
        Schema::table('games', function (Blueprint $table) {
            $table->boolean('timer_enabled')->default(false)->after('host_token');
            $table->unsignedInteger('timer_duration_minutes')->nullable()->after('timer_enabled');
            $table->timestamp('timer_ends_at')->nullable()->after('timer_duration_minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn(['timer_enabled', 'timer_duration_minutes', 'timer_ends_at']);
        });
    }
};
