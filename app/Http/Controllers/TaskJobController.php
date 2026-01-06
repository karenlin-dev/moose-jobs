<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaskJob;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Enums\JobStatus;
use Illuminate\Support\Facades\Auth;

class TaskJobController extends Controller
{
    public function index() {
        //dd('web route hit');
        $tasks = TaskJob::with('user')
            ->where('status', JobStatus::OPEN)
            ->latest()
            ->get();

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
        // 验证请求数据
        // $validated = $request->validate([
        //     'title' => 'required|string|max:255',
        //     'description' => 'required|string',
        //     'city' => 'required',
        //     'budget' => 'nullable|numeric|min:0',
        //     'user_id' => 'required|exists:users,id'
        // ]);

        if ($request->user()->role !== 'employer') {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'city' => 'required|string|max:100',
            'budget' => 'required|numeric|min:0',
            'category_id' => 'nullable|integer|exists:categories,id',
        ]);

        TaskJob::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'city' => $request->city,
            'budget' => $request->budget, 
            'category_id' => $request->category_id,
            'status' => JobStatus::OPEN,

        ]);
        return redirect()->route('dashboard')->with('success', 'Task created successfully.');
        // $data = TaskJob::create([
        //     'user_id' => Auth::id(), 
        //     'title' => $request->title,
        //     'description' => $request->description,
        //     'city' => 'required|string|max:100',
        //     'budget' => 'required|numeric|min:0',
        //     'category_id' => 'nullable|integer|exists:categories,id',
        //     //'user_id' => auth()->id(), // 自动填
        //     //'status' => 'open',
        // ]);
        
        // TaskJob::create([
        //     ...$data,
        //     //'user_id' => $request->user()->id,
        //     'status'  => JobStatus::OPEN,
        // ]);

        // //return redirect()->route('jobs.index');
        // return redirect()->route('dashboard')
        //     ->with('success', 'Task created successfully.');
        // 创建记录
        //$job = \App\Models\TaskJob::create($job);

        // return response()->json([
        //     'message' => 'Job created successfully',
        //     'data' => $job
        // ], 201);
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

    // public function update(Request $request, $id) {
    //     $job = TaskJob::findOrFail($id);

    //     $validated = $request->validate([
    //         'title' => 'sometimes|string|max:255',
    //         'description' => 'sometimes|string',
    //         'price' => 'sometimes|numeric',
    //         'category_id' => 'sometimes|exists:categories,id',
    //     ]);

    //     $job->update($validated);

    //     return response()->json([
    //         'message' => 'Job updated successfully',
    //         'data' => $job
    //     ]);
    // }
    public function update(Request $request, $id)
    {
        $job = TaskJob::find($id);

        if (!$job) {
            return response()->json(['message' => 'Job not found'], 404);
        }

        //$job->update($request->all());
        // 只修改 status 字段
        $job->status = $request->input('status');
        $job->save();

        return response()->json([
            'message' => 'Job updated successfully',
            'job' => $job
        ]);
    }

    public function show($id)
    {
        $job = TaskJob::find($id);

        if (!$job) {
            return response()->json(['message' => 'Job not found'], 404);
        }

        return response()->json($job);
    }

//     public function show(TaskJob $job)
// {
//     $job->load(['bids.user']);

//     return view('jobs.show', compact('job'));
// }



    public function destroy($id) {
        $job = TaskJob::findOrFail($id);
        $job->delete();

        return response()->json([
            'message' => 'Job deleted successfully'
        ]);
    }

}
