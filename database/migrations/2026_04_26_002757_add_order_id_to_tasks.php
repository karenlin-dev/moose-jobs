<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_jobs', function (Blueprint $table) {

            // 如果你的表叫 task_jobs，请改成 task_jobs
            $table->unsignedBigInteger('order_id')->nullable()->after('worker_id');

            // 外键（推荐加）
            $table->foreign('order_id')
                  ->references('id')
                  ->on('orders')
                  ->onDelete('set null'); // 删除订单时，不影响任务
        });
    }

    public function down(): void
    {
        Schema::table('task_jobs', function (Blueprint $table) {

            $table->dropForeign(['order_id']);
            $table->dropColumn('order_id');
        });
    }
};
