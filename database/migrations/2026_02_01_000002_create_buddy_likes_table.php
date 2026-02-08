<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buddy_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('liker_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('liked_id')->constrained('users')->onDelete('cascade');
            $table->string('status', 10);
            $table->timestamps();

            $table->unique(['liker_id', 'liked_id']);
            $table->index(['liked_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buddy_likes');
    }
};


