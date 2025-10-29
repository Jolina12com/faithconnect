<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('event_type')->default('regular');
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('groom_id')->nullable();
            $table->unsignedBigInteger('bride_id')->nullable();
            $table->string('groom_name')->nullable();
            $table->string('bride_name')->nullable();
            $table->datetime('event_date');
            $table->time('event_time')->nullable();
            $table->string('location');
            $table->string('officiating_minister')->nullable();
            $table->string('witnesses')->nullable();
            $table->unsignedBigInteger('person_id')->nullable();
            $table->string('person_name')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('godparents')->nullable();
            $table->string('parents')->nullable();
            $table->boolean('is_child')->nullable();
            $table->string('status')->default('scheduled');
            $table->text('notes')->nullable();
            $table->string('color', 30)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};