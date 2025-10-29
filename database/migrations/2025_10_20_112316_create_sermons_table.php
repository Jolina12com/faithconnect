<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sermon_series', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
        });

        Schema::create('sermon_topics', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('sermons', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('scripture_reference')->nullable();
            $table->string('video_path')->nullable();
            $table->string('audio_path')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->integer('duration')->nullable()->comment('Duration in seconds');
            $table->date('date_preached')->nullable();
            $table->boolean('featured')->default(false);
            $table->integer('view_count')->default(0);
            $table->integer('download_count')->default(0);
            $table->string('speaker_name')->nullable();
            $table->foreignId('series_id')->nullable()->constrained('sermon_series')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('sermon_sermon_topic', function (Blueprint $table) {
            $table->foreignId('sermon_id')->constrained()->onDelete('cascade');
            $table->foreignId('sermon_topic_id')->constrained()->onDelete('cascade');
            $table->primary(['sermon_id', 'sermon_topic_id']);
        });

        Schema::create('sermon_favorites', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('sermon_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->primary(['user_id', 'sermon_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sermon_favorites');
        Schema::dropIfExists('sermon_sermon_topic');
        Schema::dropIfExists('sermons');
        Schema::dropIfExists('sermon_topics');
        Schema::dropIfExists('sermon_series');
    }
};