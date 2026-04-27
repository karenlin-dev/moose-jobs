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
use Illuminate\Support\Facades\DB;
use App\Services\OrderService;
use App\Models\Order;

class TaskJobController extends Controller
{
    public function index(Request $request)
    {
        $slug = $request->query('category'); // URL: /tasks?category=errand

        // 获取所有分类，用于 Blade 的选项卡
        $categories = \App\Models\Category::orderBy('name')->get();

        $tasks = TaskJob::select('id', 'title', 'description', 'user_id', 'status', 'budget','pickup_address','dropoff_address','service_type')
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

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'city' => 'required|string|max:100',
            'budget' => 'required|numeric|min:0',
            'category_id' => 'nullable|integer|exists:categories,id',

            'service_type' => 'nullable|string',
            'scheduled_at' => 'nullable|date',
            'pickup_time' => 'nullable|date',
            'passengers' => 'nullable|integer|min:1|max:10',
            'luggage' => 'nullable|integer|min:0|max:10',

            'pickup_address' => 'nullable|string|max:255',
            'dropoff_address' => 'nullable|string|max:255',
            'distance_km' => 'nullable|numeric|min:0',

            'weight_kg' => 'nullable|numeric|min:0|max:25',
            'size_level' => 'nullable|in:small,medium,large',

            'photos' => ['nullable', 'array', 'max:10'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $task = TaskJob::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'city' => $validated['city'],
            'budget' => $validated['budget'],
            'category_id' => $validated['category_id'] ?? null,

            'service_type' => $validated['service_type'] ?? null,
            // ✅ 直接在这里写
            'task_type' => TaskJob::resolveTaskType($validated['service_type'] ?? null),
            'pickup_time' => $validated['pickup_time'] ?? null,
            // ✈️ airport fields
            'worker_id' => null,
            'can_accept' => true,
            'scheduled_at' => $validated['scheduled_at'] ?? null,
            'passengers' => $validated['passengers'] ?? null,
            'luggage' => $validated['luggage'] ?? null,
            'payment_status' => 'pending',

            // 🧳 pickup fields
            'pickup_time' => $validated['pickup_time'] ?? null,

            'pickup_address' => $validated['pickup_address'] ?? null,
            'dropoff_address' => $validated['dropoff_address'] ?? null,
            'distance_km' => $validated['distance_km'] ?? null,

            'weight_kg' => $validated['weight_kg'] ?? null,
            'size_level' => $validated['size_level'] ?? null,

            'status' => JobStatus::OPEN,
        ]);

        // 保存图片
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $img) {
                $path = $img->store('task_photos', 'public');
                $task->photos()->create([
                    'path' => $path,
                    'sort' => 0,
                ]);
            }
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Task created successfully.');
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

            'service_type' => 'nullable|string', // ✅ 必须加
            'pickup_time' => 'nullable|date',
            'pickup_address' => 'nullable|string|max:255',
            'dropoff_address' => 'nullable|string|max:255',

            'delivery_status' => 'nullable|in:pending,in_transit,delivered',

            'distance_km' => 'nullable|numeric|min:0',
            'weight_kg' => 'nullable|numeric|min:0|max:25',
            'size_level' => 'nullable|in:small,medium,large',

            'photos.*' => 'image|max:5120',
        ]);

        $serviceType = $data['service_type'] ?? $task->service_type;

        $data['task_type'] = TaskJob::resolveTaskType($serviceType);

        $task->update($data);

        // 📸 图片处理（保持你原来的）
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


    public function acceptAirport(TaskJob $task, OrderService $orderService)
    {
        $success = false;

        DB::transaction(function () use ($task, &$success, $orderService) {

            $freshTask = TaskJob::where('id', $task->id)
                ->lockForUpdate()
                ->first();

            if (
                !$freshTask ||
                $freshTask->worker_id ||
                $freshTask->status !== JobStatus::OPEN
            ) {
                return;
            }

            $freshTask->update([
                'worker_id' => auth()->id(),
                'status' => JobStatus::IN_PROGRESS,
            ]);

            // ✅ 防重复创建订单（最安全）
            $order = Order::firstOrCreate(
                ['task_job_id' => $freshTask->id],
                [
                    'employer_id' => $freshTask->user_id,
                    'worker_id' => $freshTask->worker_id,
                    'amount' => $freshTask->budget ?? 0,
                    'service_type' => 'airport',
                    'pickup_address' => $freshTask->pickup_address ?? null,
                    'dropoff_address' => $freshTask->dropoff_address ?? null,
                    'scheduled_at' => $freshTask->scheduled_at ?? null,
                    'passengers' => $freshTask->passengers ?? null,
                    'luggage' => $freshTask->luggage ?? null,
                    'status' => 'pending',
                ]
            );

            $freshTask->update([
                'order_id' => $order->id,
            ]);

            $success = true;
        });

        return $success
            ? redirect()->route('tasks.show', $task->id)->with('success', 'Order accepted!')
            : back()->with('error', 'Too late, already taken.');
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


    public function destroy(TaskJob $task)
    {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Task deleted');
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
