<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('message')->nullable();
            $table->string('emotion')->nullable();
            $table->string('response_type')->nullable();
            $table->boolean('is_bot_message')->default(false);
            $table->timestamps();
            
            $table->index(['emotion']);
            $table->index(['user_id', 'created_at']);
        });

        Schema::create('profanity_words', function (Blueprint $table) {
            $table->id();
            $table->string('word')->unique();
            $table->string('language')->default('en');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profanity_words');
        Schema::dropIfExists('chatbot_analytics');
    }
};