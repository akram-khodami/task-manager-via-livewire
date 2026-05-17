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
        Schema::create('hafez_fal_payments', function (Blueprint $table) {
            $table->id();
            $table->integer('chat_id');
            $table->string('payload');
            $table->string('status')->default(null);//        'status', // pending, paid, failed
            $table->integer('amount');
            $table->integer('bale_payment_id')->default(null);
            $table->date('paid_at');
            $table->timestamps();
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
