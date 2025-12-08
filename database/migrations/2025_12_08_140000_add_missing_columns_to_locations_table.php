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
        Schema::table('locations', function (Blueprint $table) {
            if (!Schema::hasColumn('locations', 'province')) {
                $table->string('province', 255)->default('')->after('image_path');
            }
            if (!Schema::hasColumn('locations', 'duration')) {
                $table->unsignedInteger('duration')->default(60)->after('province');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            if (Schema::hasColumn('locations', 'province')) {
                $table->dropColumn('province');
            }
            if (Schema::hasColumn('locations', 'duration')) {
                $table->dropColumn('duration');
            }
        });
    }
};
