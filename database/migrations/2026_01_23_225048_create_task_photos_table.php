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
        Schema::create('task_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_job_id')->constrained('task_jobs')->cascadeOnDelete();
            $table->string('path');               // task_photos/xxx.jpg
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();

            $table->index(['task_job_id', 'sort']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_photos');
    }
};
