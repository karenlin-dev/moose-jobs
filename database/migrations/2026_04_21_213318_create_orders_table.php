<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('task_job_id')->constrained()->onDelete('cascade');
            $table->foreignId('employer_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('worker_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            $table->string('service_type');
            // airport / errand / delivery

            $table->string('pickup_location')->nullable();
            $table->string('dropoff_location')->nullable();
            $table->dateTime('scheduled_at')->nullable();

            $table->integer('passengers')->nullable();
            $table->integer('luggage')->nullable();

            $table->decimal('amount', 10, 2);

            $table->string('status')->default('pending');
            // pending / paid / in_progress / completed / cancelled

            $table->timestamp('paid_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};