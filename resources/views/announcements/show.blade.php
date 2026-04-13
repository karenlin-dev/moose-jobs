<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $announcement->title }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50">

<div class="max-w-3xl mx-auto px-4 py-10">

    <!-- 🔙 返回 -->
    <a href="/"
       class="text-sm text-gray-500 hover:underline">
        ← Back to Home
    </a>

    <!-- 🏷 类型标签 -->
    <div class="mt-4">
        <span class="text-xs px-3 py-1 rounded-full
            @if($announcement->type === 'job') bg-blue-100 text-blue-600
            @elseif($announcement->type === 'event') bg-green-100 text-green-600
            @else bg-purple-100 text-purple-600
            @endif
        ">
            {{ strtoupper($announcement->type) }}
        </span>
    </div>

    <!-- 📰 标题 -->
    <h1 class="text-3xl font-bold mt-4 leading-tight">
        {{ $announcement->title }}
    </h1>

    <!-- 🕒 时间 -->
    <div class="text-sm text-gray-500 mt-2">
        Published {{ $announcement->created_at->format('M d, Y') }}
    </div>

    <!-- 🖼 图片 -->
    @if($announcement->image)
        <img src="{{ asset('storage/'.$announcement->image) }}"
             class="mt-6 w-full rounded-xl shadow object-cover max-h-[420px]">
    @endif

    <!-- 📄 内容 -->
    <div class="mt-6 text-gray-700 leading-relaxed text-[15px] whitespace-pre-line">
        {{ $announcement->content }}
    </div>

    <!-- 🔗 CTA -->
    <div class="mt-10 p-5 bg-white rounded-xl shadow-sm border">

        <div class="font-semibold mb-2">
            Need local help?
        </div>

        <div class="text-sm text-gray-600 mb-4">
            Post a task or contact a local worker on MooseJobs.
        </div>

        <div class="flex gap-3">

            <a href="/tasks/create"
               class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">
                Post Task
            </a>

            <a href="/workers"
               class="px-4 py-2 border rounded-lg text-sm">
                Find Workers
            </a>

        </div>

    </div>

</div>

</body>
</html>