<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobAssignment;

class JobAssignmentController extends Controller
{
    public function show(JobAssignment $assignment)
    {
        $assignment->load('task', 'worker', 'employer');
        return view('assignments.show', compact('assignment'));
    }

    public function complete(JobAssignment $assignment, Request $request)
    {
        // 权限校验：只能工人完成
        if ($request->user()->id !== $assignment->worker_id) {
            abort(403);
        }

        $assignment->task->update(['status' => 'completed']);
        return redirect()->route('dashboard')->with('success', 'Task marked as completed.');
    }

    public function myAssignments(Request $request)
    {
        $assignments = JobAssignment::where('worker_id', $request->user()->id)
                                    ->with(['job', 'employer'])
                                    ->get();

        return view('assignments.my', compact('assignments'));
    }

}
