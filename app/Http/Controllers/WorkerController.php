<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
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

        return view('workers.edit', compact('profile', 'worker'));
    }


    // 更新资料
    public function update(Request $request)
    {
        $user = Auth::user();
        // $profile = $worker->profile ?? new Profile(['user_id' => $worker->id]);

        $data = $request->validate([
            'city' => 'required|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|max:2048', // 图片大小限制 2MB
            'phone' => 'nullable|string|max:255',
            'skills' => 'nullable|string|max:1000',
        ]);

        // 处理头像上传
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $avatarPath;
        }

        // 如果 profile 不存在则创建
        if (!$user->profile) {
            $user->profile()->create($data);
        } else {
            $user->profile->update($data);
        }

        return redirect()->route('workers.edit')->with('success', 'Profile updated successfully!');

        // $data = $request->validate([
        //     'name' => 'required|string|max:255',
        //     'city' => 'nullable|string|max:255',
        //     'bio' => 'nullable|string|max:2000',
        //     'skills' => 'nullable|string|max:255',
        //     'phone' => 'nullable|string|max:50',
        //     'avatar' => 'nullable|image|max:2048', // 上传图片限制 2MB
        // ]);

        // // 上传头像
        // if ($request->hasFile('avatar')) {
        //     if ($profile->avatar) Storage::disk('public')->delete($profile->avatar);
        //     $profile->avatar = $request->file('avatar')->store('avatars', 'public');
        // }

        // $profile->bio = $data['bio'] ?? $profile->bio;
        // $profile->skills = $data['skills'] ?? $profile->skills;
        // $profile->phone = $data['phone'] ?? $profile->phone;
        // $profile->save();

        // // 更新用户基础信息
        // $worker->update(['name' => $data['name']]);

        // return redirect()->route('workers.edit')->with('success', 'Profile updated successfully.');
    }
}
