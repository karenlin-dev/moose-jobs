<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\TaskJob;
class ApplicationController extends Controller
{
    // 获取所有接单记录
    public function index()
    {
        return Application::with('job', 'user')->get();
    }

    // 创建接单记录
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'job_id' => 'required|exists:task_jobs,id'
    //     ]);

    //     $application = Application::create([
    //         'job_id' => $request->job_id,
    //         'user_id' => $request->user()->id,
    //         'status' => 'applied'
    //     ]);

    //     return response()->json($application, 201);
    // }

    public function store(Request $request)
    {
        //$user = $request()->user();
        $user = auth()->user();
        $job = TaskJob::find($request->job_id);

        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        if (!$job) {
            return response()->json(['message' => 'Job not found'], 404);
        }

        $application = Application::create([
            'user_id' => $user->id,
            'job_id' => $job->id,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Application created successfully',
            'application' => $application
        ]);
    }


    // 查看单个接单记录
    // public function show(Application $application)
    // {
    //     $application = Application::with(['user', 'task_jobs'])->find($id);

    //     if (!$application) {
    //         return response()->json(['message' => 'Application not found'], 404);
    //     }

    //     return response()->json($application);
    // }
   public function show($id)
    {
        $application = Application::with('job', 'user')->find($id);
        if (!$application) {
            return response()->json(['message' => 'Application not found'], 404);
        }
        return $application;
    }

    // 更新接单状态（接受/拒绝）
    public function update(Request $request, $id)
    {
         $application = Application::find($id);

        if (!$application) {
            return response()->json(['message' => 'Application not found'], 404);
        }
        $request->validate([
            'status' => 'required|in:applied,accepted,rejected,pending,in_progress,completed'
        ]);

        // $application->update([
        //     'status' => $request->status
        // ]);
        $application->status = $request->status;
        $application->save();

        return response()->json([
            'message' => 'Application updated successfully',
            'application' => $application
        ]);
    }

    // 删除接单记录
    public function destroy($id)
    {
        $application = Application::find($id);

        if (!$application) {
            return response()->json(['message' => 'Application not found'], 404);
        }

        $application->delete();

        return response()->json([
            'message' => 'Application deleted successfully'
        ]);
    }
}
