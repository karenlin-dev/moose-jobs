<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained()
                ->onDelete('cascade');

            $table->string('provider')->default('stripe');

            $table->decimal('amount', 10, 2);
            $table->decimal('fee', 10, 2)->default(0);
            $table->decimal('total', 10, 2);

            $table->string('status')->default('pending');
            // pending / requires_payment_method / processing / succeeded / failed / refunded

            $table->string('payment_intent_id')->nullable();
            $table->string('client_secret')->nullable();

            $table->text('error_message')->nullable();

            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};