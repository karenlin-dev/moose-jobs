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
        Schema::create('task_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('city');                                                   // add 12.13
            $table->decimal('budget', 8, 2)->nullable();                              // add 12.13
            $table->enum('status', ['open', 'in_progress', 'completed', 'cancelled']) // add 12.13
                  ->default('open');
            $table->unsignedBigInteger('application_id')->nullable();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('user_id'); // 发布者employer
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_jobs');
    }
};
