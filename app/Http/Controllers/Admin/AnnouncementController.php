<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Announcement;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::latest()->get();
        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'title' => 'required',
                'content' => 'required',
                'type' => 'required',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'is_pinned' => 'nullable|boolean',
            ]);

            $data['is_pinned'] = $data['is_pinned'] ?? false;

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('announcements', 'public');
            }

            \App\Models\Announcement::create($data);

            return redirect()
                ->route('admin.announcements.index')
                ->with('success', '✅ Announcement created successfully');

        } catch (\Exception $e) {

            return back()
                ->withInput()
                ->with('error', '❌ Failed: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $announcement = \App\Models\Announcement::findOrFail($id);

        return view('admin.announcements.edit', compact('announcement'));
    }
    public function update(Request $request, $id)
    {
        $announcement = \App\Models\Announcement::findOrFail($id);

        $data = $request->validate([
            'title' => 'required',
            'content' => 'required',
            'type' => 'required',
            'image' => 'nullable|image|max:2048'
        ]);

        // 📸 如果上传了新图片
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('announcements', 'public');
        }

        $announcement->update($data);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Updated successfully');
    }

    public function destroy($id)
    {
        $announcement = \App\Models\Announcement::findOrFail($id);

        $announcement->delete();

        return back()->with('success', 'Deleted successfully');
    }

    public function show($id)
    {
        $announcement = \App\Models\Announcement::findOrFail($id);

        return view('announcements.show', compact('announcement'));
    }
}