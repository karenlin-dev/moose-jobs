<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('pickup_location', 'pickup_address');
            $table->renameColumn('dropoff_location', 'dropoff_address');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('pickup_address', 'pickup_location');
            $table->renameColumn('dropoff_address', 'dropoff_location');
        });
    }
};
