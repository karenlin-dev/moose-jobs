<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TaskJob;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'employer') {
            // 雇主发布的任务（含投标人）
            $tasks = $user->postedTasks()
                ->with('bids.worker')
                ->latest()
                ->get();

            return view('dashboard', compact('user', 'tasks'));
        }

        // worker
        $bids = $user->bids()->with('task')->latest()->get();

        $assignments = $user->assignments()
            ->with('task')
            ->latest()
            ->get();

        $tasks = TaskJob::where('status', 'open')
            ->where('user_id', '!=', $user->id)
            ->latest()
            ->get();

        return view('dashboard', compact('user', 'bids', 'assignments', 'tasks'));

    }
}
    // public function dashboard()
    // {
    //     $user = Auth::user();

    //     if ($user->role !== 'worker') {
    //         abort(403);
    //     }

    //     // 可投标任务
    //     $tasks = \App\Models\TaskJob::where('status', 'open')
    //                 ->where('user_id', '!=', $user->id) // 排除自己发布的任务
    //                 ->latest()
    //                 ->get();

    //     // 获取已投标任务 ID
    //     $bidTaskIds = $user->bids()->pluck('job_id')->toArray();
    //     // 在每个 task 上标记
    //     $tasks->map(function($task) use ($bidTaskIds) {
    //         $task->alreadyBid = in_array($task->id, $bidTaskIds);
    //         return $task;
    //     });
    //     // 已投标任务
    //     $bids = $user->bids()->with('task')
    //             ->whereHas('task') // 只取存在任务的投标
    //             ->latest()
    //             ->get();


    //     // 已成交任务
    //     $assignments = $user->assignments()->with('task', 'employer')->latest()->get();
        

    //     return view('worker.dashboard', compact('user', 'tasks', 'bids', 'assignments'));
    // }
    
    // public function index()
    // {
    //     $user = Auth::user();
    //     if ($user->role === 'employer') {
    //         // 获取雇主发布的任务，并加载投标和投标人信息
    //         $tasks = $user->postedTasks()->with('bids.worker')->latest()->get();
    //         return view('dashboard.employer', compact('user', 'tasks'));
    //     } 
    //     else if ($user->role === 'worker') {
    //         $bids = $user->bids()->latest()->get(); // 工人申请的任务
    //         //return view('components.dashboard.worker', compact('user', 'bids'));
    //         // 工人已被雇主接受的任务（Assignments）
    //         $assignments = $user->assignments()->with('task')->latest()->get();
    //         // 获取可投标任务
    //         $tasks = \App\Models\TaskJob::where('status', 'open')
    //                     ->where('user_id', '!=', $user->id) // 排除自己发布的任务
    //                     ->latest()
    //                     ->get();
    //         return view('worker.dashboard', compact('user', 'bids', 'assignments','tasks'));
    //     }

    //     return view('dashboard.index', compact('user'));
    // }

    // public function worker()
    // {
    //     $user = auth()->user();

    //     if ($user->role !== 'worker') {
    //         abort(403);
    //     }

    //     // 加载 worker 的投标和任务分配
    //     $bids = $user->bids()->with('task')->latest()->get();
    //     $assignments = $user->assignments()->with('task', 'employer')->latest()->get();

    //     return view('worker.dashboard', compact('user', 'bids', 'assignments'));
    // }

// }
