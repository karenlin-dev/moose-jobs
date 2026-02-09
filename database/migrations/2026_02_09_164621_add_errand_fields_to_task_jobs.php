<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('task_jobs', function (Blueprint $table) {
            // 距离（公里）
            $table->decimal('distance_km', 8, 2)
                  ->nullable()
                  ->after('dropoff_address');

            // 重量（kg）
            $table->decimal('weight_kg', 6, 2)
                  ->nullable()
                  ->after('distance_km');

            // 体积等级
            $table->enum('size_level', ['small', 'medium', 'large'])
                  ->nullable()
                  ->after('weight_kg');
        });
    }

    public function down(): void
    {
        Schema::table('task_jobs', function (Blueprint $table) {
            $table->dropColumn([
                'distance_km',
                'weight_kg',
                'size_level'
            ]);
        });
    }
};

