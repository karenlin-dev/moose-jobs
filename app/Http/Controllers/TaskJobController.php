<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaskJob;
use App\Models\TaskPhoto;
use App\Models\Category;
use App\Enums\BidStatus;
use Illuminate\Support\Str;
use App\Enums\JobStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; 

class TaskJobController extends Controller
{
    public function index(Request $request)
    {
        $slug = $request->query('category'); // URL: /tasks?category=errand

        // 获取所有分类，用于 Blade 的选项卡
        $categories = \App\Models\Category::orderBy('name')->get();

        $tasks = TaskJob::select('id', 'title', 'description', 'user_id', 'status', 'budget')
            ->with(['user', 'category', 'bids']) // 加 bids 方便快速接受按钮显示
            ->where('status', JobStatus::OPEN)
            ->when($slug, function($query) use ($slug) {
                $query->whereHas('category', function($q) use ($slug) {
                    $q->where('slug', $slug);
                });
            })
            ->latest()
            ->paginate(12);

        return view('tasks.index', compact('tasks', 'categories', 'slug'));
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
            'pickup_address' => 'nullable|string|max:255',
            'dropoff_address' => 'nullable|string|max:255',
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
            'pickup_address' => $request->pickup_address,
            'dropoff_address' => $request->dropoff_address,
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

    public function complete(TaskJob $task, Request $request)
    {
        if ((int)$task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        // 状态检查（可选，避免重复完成）
        if ($task->status === JobStatus::COMPLETED) {
            return response()->json(['message' => 'Task already completed'], 400);
        }

        $task->update(['status' => JobStatus::COMPLETED]);

        $task->assignment?->update([
            'completed_at' => now()
        ]);

        return response()->json([
            'message' => 'Task marked as completed',
            'task_id' => $task->id
        ]);
    }

    public function quickAccept(TaskJob $task, Request $request)
    {
        // 权限校验：雇主本人
        if ((int)$task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // 找第一个 pending bid
        $bid = $task->bids()->where('status', BidStatus::PENDING)->first();
        if (!$bid) {
            return response()->json(['message' => 'No pending bids'], 400);
        }

        // 调用现有 accept 方法逻辑
        return app(BidController::class)->accept($bid, $request);
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
            'pickup_address' => 'nullable|string|max:255',
            'dropoff_address' => 'nullable|string|max:255',
            'dropoff_address' => 'nullable|string|max:255',
            'delivery_status' => 'required|in:pending,in_transit,delivered',
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
        $task = TaskJob::with(['photos', 'category'])->findOrFail($id);

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

    public function errands(Request $request)
    {
        $categories = Category::orderBy('name')->get();

        // 获取 Errand 分类
        $errandCategory = Category::where('slug', 'errand')->first();

        $tasks = TaskJob::with(['user', 'category', 'bids'])
            ->where('status', JobStatus::OPEN)
            ->when($errandCategory, fn($q) => $q->where('category_id', $errandCategory->id))
            ->latest()
            ->paginate(12);

        return view('tasks.errands', compact('tasks', 'categories'));
    }


}
