@extends('admin.layout')

@section('title', 'Create Announcement')

@section('content')

<div class="max-w-xl bg-white p-6 rounded-xl shadow-sm">

    <!-- 成功提示 -->
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    
<form method="POST"
      action="{{ route('admin.announcements.store') }}"
      enctype="multipart/form-data">
    @csrf

    <!-- Title -->
    <div>
        <label class="text-sm font-medium">Title</label>
        <input name="title" class="w-full border p-2 rounded mt-1" required>

        @error('title')
            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
        @enderror
    </div>

    <!-- Content -->
    <div class="mt-3">
        <label class="text-sm font-medium">Content</label>
        <textarea name="content" class="w-full border p-2 rounded mt-1 h-28" required></textarea>

        @error('content')
            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
        @enderror
    </div>

    <!-- Type -->
    <div class="mt-3">
        <label class="text-sm font-medium">Type</label>
        <select name="type" class="w-full border p-2 rounded mt-1">
            <option value="promo">Promo</option>
            <option value="job">Job</option>
            <option value="event">Event</option>
        </select>

        @error('type')
            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
        @enderror
    </div>

    <!-- ✅ 单图片上传（已修复） -->
    <div class="mt-3">
        <label class="text-sm font-medium">Image</label>

        <input type="file" name="image"
            class="w-full mt-1 border p-2 rounded">

        @error('image')
            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
        @enderror
    </div>

    <!-- Submit -->
    <button class="w-full mt-4 bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700">
        Publish
    </button>

</form>

</div>

@endsection