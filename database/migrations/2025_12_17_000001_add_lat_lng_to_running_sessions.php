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
        Schema::table('running_sessions', function (Blueprint $table) {
            $table->decimal('location_lat', 10, 7)->nullable()->after('location_name');
            $table->decimal('location_lng', 10, 7)->nullable()->after('location_lat');
            $table->timestamp('started_at')->nullable()->after('location_lng');
            $table->timestamp('completed_at')->nullable()->after('started_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('running_sessions', function (Blueprint $table) {
            $table->dropColumn(['location_lat', 'location_lng', 'started_at', 'completed_at']);
        });
    }
};
