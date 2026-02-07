<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaskJob;
use App\Models\TaskPhoto;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Enums\JobStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskJobController extends Controller
{
    public function index()
    {
        $tasks = TaskJob::select('id', 'title', 'description', 'user_id', 'status', 'budget')
            ->with(['user', 'category'])
            ->where('status', JobStatus::OPEN)
            ->latest()
            ->paginate(12);

        return view('tasks.index', compact('tasks'));
    }


    public function create()
    {
        // 只允许雇主
        abort_if(Auth::user()->role !== 'employer', 403);
        $categories = Category::all(); // 传给 Blade
        return view('tasks.create', compact('categories'));
    }

    public function store(Request $request)
    {

        if ($request->user()->role !== 'employer') {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'city' => 'required|string|max:100',
            'budget' => 'required|numeric|min:0',
            'category_id' => 'nullable|integer|exists:categories,id',
            'photos' => ['nullable', 'array', 'max:10'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $task = TaskJob::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'city' => $request->city,
            'budget' => $request->budget, 
            'category_id' => $request->category_id,
            'status' => JobStatus::OPEN,
        ]);
        // 保存多张图片
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $img) {
                $path = $img->store('task_photos', 'public'); // 存到 storage/app/public/task_photos
                $task->photos()->create([
                    'path' => $path,
                    'sort' => 0,
                ]);
            }
        }
        return redirect()->route('dashboard')->with('success', 'Task created successfully.');
  
    }
  

    public function myJobs(Request $request)
    {
        return TaskJob::where('user_id', $request->user()->id)
                    ->with('bids.user.profile')
                    ->latest()
                    ->get();
    }

    public function complete(TaskJob $taskJob, Request $request)
    {
        if ($taskJob->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $taskJob->update(['status' => 'completed']);

        $taskJob->assignment?->update([
            'completed_at' => now()
        ]);

        return response()->json(['message' => 'Task completed']);
    }

    public function update(Request $request, TaskJob $task)
    {
        abort_unless((int)auth()->id() === (int)$task->user_id, 403);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'city' => 'required|string',
            'budget' => 'nullable|numeric',
            'category_id' => 'nullable|exists:categories,id',
            'photos.*' => 'image|max:5120',
        ]);

        $task->update($data);

        // 新图片
        if ($request->hasFile('photos')) {
            $maxSort = $task->photos()->max('sort') ?? 0;

            foreach ($request->file('photos') as $i => $photo) {
                TaskPhoto::create([
                    'task_job_id' => $task->id,
                    'path' => $photo->store('task_photos', 'public'),
                    'sort' => $maxSort + $i + 1,
                ]);
            }
        }
        return redirect()->route('dashboard')
                     ->with('success', 'Task updated successfully!');
    }



    public function edit(TaskJob $task)
    {
        if ((int)$task->user_id !== (int)auth()->id()) {
                abort(403);
        }
        $photos = TaskPhoto::where('task_job_id', $task->id)
            ->orderBy('sort')
            ->get();

        $categories = Category::orderBy('name')->get();

        return view('tasks.edit', compact('task', 'photos', 'categories'));
    }



    public function show($id)
    {
        //$task = TaskJob::findOrFail($id);
        // eager load
        $task = TaskJob::with('photos')->findOrFail($id);

        return view('tasks.show', compact('task'));
    }


    public function destroy($id) {
        $job = TaskJob::findOrFail($id);
        $job->delete();

        return response()->json([
            'message' => 'Job deleted successfully'
        ]);
    }

   public function destroyPhoto(TaskPhoto $photo)
    {
        // 确保 task 存在
        $taskJob = $photo->taskJob;
        if (!$taskJob || (int)$taskJob->user_id !== (int)auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // 删除文件
        if ($photo->path && Storage::disk('public')->exists($photo->path)) {
            Storage::disk('public')->delete($photo->path);
        }
        $photo->delete();

        return response()->json(['success' => true]);
    }



    public function reorderPhotos(Request $request)
    {
        $order = $request->input('order', []);

        foreach ($order as $item) {
            $photo = TaskPhoto::find($item['id']);

            if (!$photo) continue;

            // 核心：通过 task → user 校验
            if ($photo->task->user_id !== auth()->id()) {
                abort(403);
            }

            $photo->update(['sort' => $item['sort']]);
        }

        return response()->json(['success' => true]);
    }


}
