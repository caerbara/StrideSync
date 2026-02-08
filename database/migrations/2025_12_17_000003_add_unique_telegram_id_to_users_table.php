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
        Schema::table('users', function (Blueprint $table) {
            // Ensure each user can only be linked to a single Telegram account
            // Check if the unique constraint already exists to avoid duplicate key error
            if (!Schema::hasTable('users')) {
                return;
            }
            
            $indexes = DB::select("SHOW INDEXES FROM users WHERE Key_name = 'users_telegram_id_unique'");
            if (empty($indexes)) {
                $table->unique('telegram_id', 'users_telegram_id_unique');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_telegram_id_unique');
        });
    }
};


