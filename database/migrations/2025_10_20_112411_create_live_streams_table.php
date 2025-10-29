<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_streams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title')->nullable();
            $table->string('room_name');
            $table->string('stream_id')->unique();
            $table->string('cloudinary_public_id')->nullable();
            $table->text('replay_url')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->integer('duration')->default(0);
            $table->bigInteger('file_size')->default(0);
            $table->enum('status', ['live', 'processing', 'ended'])->default('live');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
            
            $table->index(['status']);
            $table->index(['user_id']);
        });

        Schema::create('stream_viewers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stream_id')->constrained('live_streams')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('viewer_name')->nullable();
            $table->string('participant_identity');
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamp('left_at')->nullable();
            $table->integer('duration_seconds')->default(0);
            $table->timestamps();
            
            $table->index(['stream_id', 'joined_at']);
        });

        Schema::create('livestream_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('content');
            $table->string('room_name');
            $table->timestamps();
        });

        Schema::create('livestream_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type');
            $table->string('room_name');
            $table->string('participant_identity');
            $table->timestamp('timestamp')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livestream_reactions');
        Schema::dropIfExists('livestream_comments');
        Schema::dropIfExists('stream_viewers');
        Schema::dropIfExists('live_streams');
    }
};