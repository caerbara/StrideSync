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
            $table->unsignedBigInteger('session_id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('joined_at')->nullable(); // Optional
            $table->unique(['session_id', 'user_id']); // Prevent duplicate joins
            $table->foreign('session_id')->references('session_id')->on('running_sessions')->onDelete('cascade');
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
