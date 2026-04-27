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
        Schema::table('task_jobs', function (Blueprint $table) {

            // ⭐ 核心：业务类型
            $table->string('service_type')->default('errand');

            // ⭐ 接送机字段
            $table->timestamp('scheduled_at')->nullable();
            $table->integer('passengers')->nullable();
            $table->integer('luggage')->nullable();

            // ⭐ 支付
            $table->string('payment_status')->default('unpaid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
