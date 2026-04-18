<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->get();
                
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|image|mimes:jpg,jpeg,png,svg,webp|max:2048',
        ]);
        $slug = Str::slug($request->name);

        $count = Category::where('slug', 'like', $slug.'%')->count();

        if ($count > 0) {
            $slug = $slug . '-' . ($count + 1);
        }
        Category::create([
            'name' => $request->name,
            'slug' => $slug,
            'color' => $request->color ?? 'gray',
            'icon' => $request->icon,
        ]);

    return redirect()->route('admin.categories.index');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'color' => $request->color ?? 'gray',
            'icon' => $request->icon
        ]);


        return redirect()->route('admin.categories.index')
            ->with('success', 'Updated');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return back()->with('success', 'Deleted');
    }

}
