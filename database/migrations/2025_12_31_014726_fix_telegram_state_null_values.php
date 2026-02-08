<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, update all NULL values to 'initial'
        DB::table('users')->whereNull('telegram_state')->update([
            'telegram_state' => 'initial'
        ]);

        // Then modify the column to NOT NULL
        Schema::table('users', function (Blueprint $table) {
            $table->string('telegram_state')->default('initial')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('telegram_state')->nullable()->change();
        });
    }
};


