<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_jobs', function (Blueprint $table) {
            $table->boolean('can_accept')
                ->default(true)
                ->index()
                ->after('worker_id');
        });
    }

    public function down(): void
    {
        Schema::table('task_jobs', function (Blueprint $table) {
            $table->dropColumn('can_accept');
        });
    }
};
