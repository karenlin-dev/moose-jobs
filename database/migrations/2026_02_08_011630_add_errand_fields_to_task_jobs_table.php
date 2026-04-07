<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_jobs', function (Blueprint $table) {
            if (!Schema::hasColumn('task_jobs', 'delivery_status')) {
                $table->string('delivery_status')->default('pending');
            }
        });
    }

    public function down(): void
    {
        Schema::table('task_jobs', function (Blueprint $table) {
            $table->dropColumn(['delivery_status']);
        });
    }
};
