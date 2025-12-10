<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->renameColumn('duration', 'distance');
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->decimal('distance', 5, 1)->change();
        });
    }

    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->integer('distance')->change();
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->renameColumn('distance', 'duration');
        });
    }
};
