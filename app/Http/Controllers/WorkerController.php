<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateWorkerProfileRequest;
use App\Models\ProfilePhoto;

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
        // // 关键：加载 profile 的 photos（多图）
        // $photos = TaskPhoto::where('task_job_id', $profile->id)
        //             ->orderBy('sort')
        //             ->get();
        $photos = $profile->photos;

        // 加载分类
        $profile->load('categories');
        $categories = Category::orderBy('name')->get();

        return view('workers.edit', compact('profile', 'worker', 'categories', 'photos'));
    }

    public function update(UpdateWorkerProfileRequest $request)
    {
        $user = Auth::user();
        // // 如果 profile 不存在则创建
        $profile = $user->profile ?? new Profile(['user_id' => $user->id]);

        $data = $request->validated();

        // skills = categories 文本拼接
        $data['skills'] = isset($data['category_ids'])
            ? Category::whereIn('id', $data['category_ids'])
                ->pluck('name')
                ->implode(', ')
            : '';

        // avatar
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // 更新基础 profile 字段
        $profile->update($data);

        // 同步多对多分类
        $profile->categories()->sync($request->input('category_ids', []));

        // photos 上传
        if ($request->hasFile('photos')) {
            // 找到已有最大 sort
            $maxSort = $profile->photos()->max('sort') ?? 0;

            foreach ($request->file('photos') as $index => $photo) {
                $path = $photo->store('profile_photos', 'public');

                // TaskPhoto::create([
                //     'task_job_id' => $profile->id,  // 复用 task_job_id 存 profile id
                //     'path' => $path,
                //     'sort' => $maxSort + $index + 1,
                // ]);
                ProfilePhoto::create([
                    'profile_id' => $profile->id,
                    'path'       => $path,
                    'sort'       => $maxSort + $index + 1,
                ]);
            }
        }

        return back()->with('success', 'Profile updated successfully.');
    }

    // 删除单张图片
    public function destroyPhoto(ProfilePhoto $photo)
    {
        $profile = auth()->user()->profile;

        if ($photo->profile_id !== $profile->id) {
            abort(403);
        }

        Storage::disk('public')->delete($photo->path);
        $photo->delete();

        return response()->json(['success' => true]);
    }

    public function reorderPhotos(Request $request)
    {
        $profile = auth()->user()->profile;
        $order = $request->input('order', []);

        foreach ($order as $item) {
            $photo = ProfilePhoto::find($item['id']);

            if ($photo && $photo->profile_id === $profile->id) {
                $photo->update([
                    'sort' => $item['sort'],
                ]);
            }
        }

        return response()->json(['success' => true]);
    }
    


}
