<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 先确保列存在
        if (!Schema::hasColumn('categories', 'slug')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->string('slug')->nullable()->after('name');
            });
        }

        // 更新特定 category 的 slug
        $mapping = [
            'Moving'      => 'moving',
            'Cleaning'    => 'cleaning',
            'Maintenance' => 'maintenance',
            'Renovation'  => 'renovation',
            'Errand'      => 'errand',
        ];

        foreach ($mapping as $name => $slug) {
            DB::table('categories')
                ->where('name', $name)
                ->update(['slug' => $slug]);
        }

        // 安全地添加唯一索引：try/catch
        try {
            Schema::table('categories', function (Blueprint $table) {
                $table->unique('slug');
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // 如果索引已存在就忽略
        }
    }

    public function down(): void
    {
        // 回退时可以置空这些 slug
        $names = ['Moving', 'Cleaning', 'Maintenance', 'Renovation', 'Errand'];
        DB::table('categories')->whereIn('name', $names)->update(['slug' => null]);

        // 删除唯一索引
        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique(['slug']);
        });
    }
};
