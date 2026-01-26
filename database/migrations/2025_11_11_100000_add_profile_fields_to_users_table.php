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
            $table->string('gender')->nullable()->after('telegram_id');
            $table->string('avg_pace')->nullable()->after('gender'); // e.g., "6:30/km"
            $table->text('location')->nullable()->after('avg_pace'); // JSON with latitude, longitude
            $table->string('strava_screenshot')->nullable()->after('location'); // Path to uploaded image
            $table->string('telegram_state')->default('initial')->after('strava_screenshot'); // Track profile setup state
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['gender', 'avg_pace', 'location', 'strava_screenshot', 'telegram_state']);
        });
    }
};
