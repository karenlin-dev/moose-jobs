@extends('admin.layout')

@section('title', 'Edit Announcement')

@section('content')

<div class="max-w-xl bg-white p-6 rounded-xl shadow-sm">

    <form method="POST"
          action="{{ route('admin.announcements.update', $announcement->id) }}"
          enctype="multipart/form-data"
          class="space-y-4">

        @csrf
        @method('PUT')

        <!-- 标题 -->
        <div>
            <label class="text-sm font-medium">Title</label>
            <input name="title"
                   value="{{ $announcement->title }}"
                   class="w-full border p-2 rounded mt-1">
        </div>

        <!-- 内容 -->
        <div>
            <label class="text-sm font-medium">Content</label>
            <textarea name="content"
                      class="w-full border p-2 rounded mt-1 h-28">{{ $announcement->content }}</textarea>
        </div>

        <!-- 类型 -->
        <div>
            <label class="text-sm font-medium">Type</label>
            <select name="type"
                class="w-full border p-2 rounded mt-1">

                <option value="promo" @selected($announcement->type=='promo')>Promo</option>
                <option value="job" @selected($announcement->type=='job')>Job</option>
                <option value="event" @selected($announcement->type=='event')>Event</option>

            </select>
        </div>

        <!-- 当前图片 -->
        @if($announcement->image)
            <div>
                <img src="{{ asset('storage/'.$announcement->image) }}"
                     class="w-40 rounded mb-2">
            </div>
        @endif

        <!-- 新图片 -->
        <input type="file" name="image">

        <!-- 提交 -->
        <button class="w-full bg-indigo-600 text-white py-2 rounded-lg">
            Update
        </button>

    </form>

</div>

@endsection