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

            // 🚀 任务类型：instant / bidding
            $table->string('task_type')
                ->default('bidding')
                ->index()
                ->after('service_type');

            // ✈️ 接机时间
            $table->dateTime('pickup_time')
                ->nullable()
                ->after('task_type');

        });
    }

    public function down(): void
    {
        Schema::table('task_jobs', function (Blueprint $table) {
            $table->dropColumn(['task_type', 'pickup_time']);
        });
    }
};
