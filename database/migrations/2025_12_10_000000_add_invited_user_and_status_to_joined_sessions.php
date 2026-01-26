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
        Schema::table('joined_sessions', function (Blueprint $table) {
            // Add invited_user_id column (nullable for sessions where user_id joins)
            $table->unsignedBigInteger('invited_user_id')->nullable()->after('user_id');
            
            // Add status column (default: joined, can be: invited, accepted, declined, joined)
            $table->string('status')->default('joined')->after('invited_user_id');
            
            // Add foreign key for invited_user_id
            $table->foreign('invited_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('joined_sessions', function (Blueprint $table) {
            $table->dropForeign(['invited_user_id']);
            $table->dropColumn(['invited_user_id', 'status']);
        });
    }
};
