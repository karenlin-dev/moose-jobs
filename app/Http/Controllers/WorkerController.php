<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WorkerController extends Controller
{
    // 查看 Worker 资料
    public function show(User $worker)
    {
        if ($worker->role !== 'worker') abort(404);
        $worker->load('profile'); // 预加载 profile

        return view('workers.show', compact('worker'));
    }

    // 编辑自己的资料

    public function edit()
    {
        //dd('workers.edit route hit');

        $worker = Auth::user();

        // 获取已有 profile 或创建新的默认 profile
        $profile = Profile::firstOrCreate(
            ['user_id' => $worker->id],
            [
                'city' => 'Moose Jaw',
                'rating' => 0,
                'total_reviews' => 0,
                'avatar' => null,
            ]
        );
        // 关键：加载多图关系（Profile::photos() 你需要已定义）
        $profile->load('photos');

        $profile->load('categories');
        $categories = Category::orderBy('name')->get();

        return view('workers.edit', compact('profile', 'worker', 'categories'));
    }


    // 更新资料
    public function update(Request $request)
    {
        $user = Auth::user();
        // // 如果 profile 不存在则创建
        // $profile = $user->profile ?? new Profile(['user_id' => $user->id]);

        $data = $request->validate([
            'city' => 'required|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|max:2048', // 图片大小限制 2MB
            'phone' => 'nullable|string|max:255',
            'skills' => 'nullable|string|max:1000',
            'photos' => ['nullable', 'array', 'max:10'],                 // 最多10张
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], // 每张<=5MB
            'category_ids' => 'nullable|array|max:10',
            'category_ids.*' => 'integer|exists:categories,id',
        ]); 

         // 先拿到或创建 profile（确保有 ID）
        $profile = \App\Models\Profile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'city' => 'Moose Jaw',
                'rating' => 0,
                'total_reviews' => 0,
                'avatar' => null,
            ]
        );

        // 处理头像上传
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $avatarPath;
        }

         // 更新 profile 基础字段（不含 photos）
        unset($data['photos']); // 避免 update 时把 photos 当字段
        $profile->update($data);

        // 同步多对多分类（没选就清空）
        $profile->categories()->sync($request->input('category_ids', []));

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $img) {
                $path = $img->store('profile_photos', 'public'); // 存到 storage/app/public/profile_photos
                $profile->photos()->create([
                    'path' => $path,
                    'sort' => 0,
                ]);
            }
        }

        return redirect()->route('workers.edit')->with('success', 'Profile updated successfully!');
      
    }
}
