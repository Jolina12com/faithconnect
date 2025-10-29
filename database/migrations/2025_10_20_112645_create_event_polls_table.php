<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_polls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->date('deadline')->nullable();
            $table->boolean('allow_comments')->default(false);
            $table->boolean('notify_responses')->default(false);
            $table->timestamps();
            
            $table->index(['event_id']);
        });

        Schema::create('event_poll_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained('event_polls')->onDelete('cascade');
            $table->string('option_text');
            $table->string('option_value');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            
            $table->index(['poll_id']);
        });

        Schema::create('event_poll_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained('event_polls')->onDelete('cascade');
            $table->foreignId('option_id')->constrained('event_poll_options')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('comment')->nullable();
            $table->timestamps();
            
            $table->unique(['poll_id', 'user_id']);
            $table->index(['poll_id']);
            $table->index(['option_id']);
            $table->index(['user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_poll_responses');
        Schema::dropIfExists('event_poll_options');
        Schema::dropIfExists('event_polls');
    }
};