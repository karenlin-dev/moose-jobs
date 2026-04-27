<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\TaskJob;
use App\Models\Category;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
       public function index(Request $request)
    {
        $user = Auth::user();

        // 给 dashboard 的过滤器用（worker / employer 都可以用）
        $categories = Category::orderBy('name')->get();
        $categoryId = $request->query('category'); // null 表示 All

        if ($user->role === 'employer') {

            $tasksQuery = $user->postedTasks()->with('bids.worker');

            if ($categoryId) {
                $tasksQuery->where('category_id', $categoryId);
            }

            $tasks = $tasksQuery->latest()->get();

            return view('dashboard', compact('user', 'tasks', 'categories', 'categoryId'));
        }

        // -------------------------
        // worker dashboard
        // -------------------------

        // worker 的 bids（带 task + task.category）
        $bids = $user->bids()
            ->with(['task.category', 'worker'])
            ->latest()
            ->get();

        // assignments（带 task + task.category）
        $assignments = $user->assignments()
            ->with(['task.category'])
            ->latest()
            ->get();

         // ✅ 可接单任务（核心修复）
        $tasksQuery = TaskJob::query()
            ->where('status', 'open')
            ->whereNull('worker_id')   // 🔥 关键
            ->where('user_id', '!=', $user->id)
            ->with(['category']);

        // 分类过滤
        if ($categoryId) {
            $tasksQuery->where('category_id', $categoryId);
        }

        $tasks = $tasksQuery->latest()->get();
        return view('dashboard', compact(
            'user',
            'tasks',
            'bids',
            'assignments',
            'categories',
            'categoryId'
        ));

    }

}
   