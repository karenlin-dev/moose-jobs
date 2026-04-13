@extends('admin.layout')

@section('title', 'Announcements')

@section('content')

<div class="bg-white rounded-2xl shadow-sm border">

    <!-- Header -->
    <div class="flex justify-between items-center p-5 border-b">
        <h2 class="text-lg font-semibold">Announcements</h2>

        <a href="{{ route('admin.announcements.create') }}"
           class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
            + New
        </a>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">

        <table class="w-full text-sm">

            <thead class="bg-gray-50 text-gray-600">
                <tr class="text-left">
                    <th class="p-3">Image</th>
                    <th class="p-3">Title</th>
                    <th class="p-3">Type</th>
                    <th class="p-3">Created</th>
                    <th class="p-3 text-right">Actions</th>
                </tr>
            </thead>

            <tbody>

            @forelse($announcements as $a)
                <tr class="border-t hover:bg-gray-50 transition">

                    <!-- 图片 -->
                    <td class="p-3">
                        @if($a->image)
                            <img src="{{ asset('storage/'.$a->image) }}"
                                 class="w-16 h-12 object-cover rounded">
                        @else
                            <div class="w-16 h-12 bg-gray-100 rounded flex items-center justify-center text-xs text-gray-400">
                                No Image
                            </div>
                        @endif
                    </td>

                    <!-- 标题 -->
                    <td class="p-3">
                        <div class="font-medium">
                            {{ $a->title }}
                        </div>
                        <div class="text-xs text-gray-400 mt-1">
                            {{ \Illuminate\Support\Str::limit($a->content, 40) }}
                        </div>
                    </td>

                    <!-- 类型 -->
                    <td class="p-3">
                        <span class="text-xs px-2 py-1 rounded-full
                            @if($a->type === 'job') bg-blue-100 text-blue-600
                            @elseif($a->type === 'event') bg-green-100 text-green-600
                            @else bg-purple-100 text-purple-600
                            @endif
                        ">
                            {{ strtoupper($a->type) }}
                        </span>
                    </td>

                    <!-- 时间 -->
                    <td class="p-3 text-gray-500">
                        {{ $a->created_at->format('Y-m-d') }}
                    </td>

                    <!-- 操作 -->
                    <td class="p-3 text-right">

                        <div class="flex justify-end gap-2">

                            <a href="{{ route('admin.announcements.edit', $a->id) }}"
                               class="text-blue-600 hover:underline text-xs">
                                Edit
                            </a>

                            <!-- 🗑️ Delete -->
                            <form method="POST"
                                action="{{ route('admin.announcements.destroy', $a->id) }}">

                                @csrf
                                @method('DELETE')

                                <button onclick="return confirm('Are you sure?')"
                                        class="text-red-500 hover:text-red-700 text-xs">
                                    Delete
                                </button>

                            </form>

                        </div>

                    </td>

                </tr>

            @empty
                <tr>
                    <td colspan="5" class="text-center p-10 text-gray-400">
                        No announcements yet
                    </td>
                </tr>
            @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection