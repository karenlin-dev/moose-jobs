<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_jobs', function (Blueprint $table) {
            $table->enum('delivery_status', ['pending', 'in_transit', 'delivered'])
                  ->default('pending')
                  ->after('dropoff_address');
        });
    }

    public function down(): void
    {
        Schema::table('task_jobs', function (Blueprint $table) {
            $table->dropColumn(['delivery_status']);
        });
    }
};
