<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Bid;
use App\Models\TaskJob;
use App\Models\JobAssignment;
use App\Enums\BidStatus;
use App\Enums\JobStatus;

class BidController extends Controller
{
    // 显示单个 Bid 详情
    public function show(Bid $bid)
    {
        // 预加载 task
        $bid->load('task');

        return view('bids.show', compact('bid'));
    }
    
    public function create(TaskJob $task)
    {
        abort_if(Auth::user()->role !== 'worker', 403);
        return view('bids.create', compact('task'));
    }

    public function store(Request $request, TaskJob $task)
    {
        abort_if(Auth::user()->role !== 'worker', 403);

        $user = $request->user();

        // 检查是否已投过标
        $exists = $user->bids()->where('job_id', $task->id)->exists();
        if ($exists) {
            return redirect()->route('dashboard')
                            ->with('error', 'You have already placed a bid for this task.');
        }

        if ($task->status !== JobStatus::OPEN) {
            return redirect()->route('dashboard')
                            ->with('error', 'Task is not open for bidding.');
        }

        $request->validate([
            'price' => 'required|numeric|min:0',
            'message' => 'nullable|string'
        ]);

        Bid::create([
            'job_id'  => $task->id,
            'user_id' => $user->id,
            'price'   => $request->price,
            'message' => $request->message,
            'status'  => BidStatus::PENDING,
        ]);

        return redirect()->route('dashboard')
                        ->with('success', 'Bid submitted successfully.');
    }


    public function accept(Bid $bid, Request $request)
    {
        //dd($bid->task);
        // 获取任务
        $task = $bid->task; // 确保 Bid 模型有 task() 关联
        
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        // 权限校验：只能雇主本人操作
        if ((int)$task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // 状态校验
        if ($task->status !== JobStatus::OPEN) {
            return response()->json(['message' => 'Task not open'], 400);
        }

        // Bid 状态校验
        if ($bid->status !== BidStatus::PENDING) {
            return response()->json(['message' => 'Bid already processed'], 400);
        }

        // 使用事务防并发
        DB::transaction(function () use ($bid, $task) {

            // 锁定任务
            $task->lockForUpdate();

            // 接受当前 bid
            $bid->update([
                'status' => BidStatus::ACCEPTED,
            ]);

            // 拒绝其他 pending bids
            Bid::where('job_id', $task->id)
                ->where('status', BidStatus::PENDING)
                ->where('id', '!=', $bid->id)
                ->update([
                    'status' => BidStatus::REJECTED,
                ]);

            // 创建任务分配记录
            JobAssignment::create([
                'job_id'   => $task->id,
                'employer_id'   => $task->user_id,
                'worker_id'     => $bid->user_id,
                'agreed_price'  => $bid->price,
                'started_at'    => now(),
            ]);

            // 更新任务状态
            $task->update([
                'status' => JobStatus::IN_PROGRESS,
            ]);
        });

        return response()->json(['message' => 'Bid accepted successfully']);
    }

    public function myBids(Request $request)
    {
        $bids = Bid::where('user_id', $request->user()->id)
                    ->with(['job', 'job.user', 'job.assignment'])
                    ->get();

        return view('bids.my', compact('bids'));
    }


}
