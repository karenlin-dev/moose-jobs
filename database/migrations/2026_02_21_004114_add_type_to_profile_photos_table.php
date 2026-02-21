<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('profile_photos', function (Blueprint $table) {
            $table->string('type')
                  ->default('image')
                  ->after('path'); // 可选：放在 path 后面
        });
    }

    public function down(): void
    {
        Schema::table('profile_photos', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};