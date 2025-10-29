<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('donor_name')->nullable();
            $table->enum('donation_type', ['monetary', 'food', 'materials', 'medical', 'other'])->default('monetary');
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('payment_method')->nullable();
            $table->string('receipt_number')->nullable();
            $table->string('item_name')->nullable();
            $table->decimal('quantity', 10, 2)->nullable();
            $table->string('unit')->nullable();
            $table->string('condition')->nullable();
            $table->date('expiry_date')->nullable();
            $table->date('donation_date');
            $table->boolean('is_recurring')->default(false);
            $table->string('category')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};