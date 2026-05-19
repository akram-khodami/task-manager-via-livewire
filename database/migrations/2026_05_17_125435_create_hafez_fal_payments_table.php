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
        // مطمئن شو migration درست اجرا شده
        Schema::create('hafez_fal_payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('chat_id');
            $table->string('payload')->unique();
            $table->integer('amount');
            $table->string('status')->default('pending'); // pending, paid, failed
            $table->string('bale_payment_id')->nullable();
            $table->text('payment_info')->nullable(); // JSON string
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['chat_id', 'status']);
            $table->index('payload');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hafez_fal_payments');
    }
};
