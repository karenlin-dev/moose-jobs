<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    // 获取所有分类
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    // 创建新分类
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $category = Category::create([
            'name' => $request->name
        ]);

        return response()->json($category, 201);
    }

    // 查看单个分类
    public function show(Category $category)
    {
        return $category;
    }

    // 更新分类
   public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $category = \App\Models\Category::findOrFail($id);
        $category->name = $request->name;
        $category->save();

        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    // 删除分类

public function destroy($id)
{
    $category = \App\Models\Category::findOrFail($id);
    $category->delete();

    return response()->json([
        'success' => true,
        'message' => "Category with id {$id} deleted."
    ]);
}

}
