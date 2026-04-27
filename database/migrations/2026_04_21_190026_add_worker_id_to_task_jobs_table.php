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
            $table->unsignedBigInteger('worker_id')->nullable()->after('user_id');

            // 如果 worker 也是 users 表里的用户
            $table->foreign('worker_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete(); // worker 删除后任务不报错
        });
    }

    public function down(): void
    {
        Schema::table('task_jobs', function (Blueprint $table) {
            $table->dropForeign(['worker_id']);
            $table->dropColumn('worker_id');
        });
    }
};
