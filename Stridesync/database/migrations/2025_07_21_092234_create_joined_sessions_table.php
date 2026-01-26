<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('joined_sessions', function (Blueprint $table) {
            $table->id('jsession_id'); // Primary Key
            $table->foreignId('session_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('joined_at')->nullable(); // Optional
            $table->unique(['session_id', 'user_id']); // Prevent duplicate joins
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('joined_sessions');
    }
};
