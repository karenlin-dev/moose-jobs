<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Moose Jobs</title>

    {{-- 如果你项目已经有 layouts/app.blade.php 并且里面有 @vite，可以改成继承布局。
         这里为了“100%能跑”，先写成独立页面。
    --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 text-gray-900">
<header class="border-b bg-white">
    <div class="max-w-6xl mx-auto px-4 py-5 flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            {{-- 你的 logo 如果放在 images/logo.png --}}
            <div class="h-24 w-24 rounded-xl overflow-hidden bg-gray-100 flex items-center justify-center">
                <img src="{{ asset('images/logo.PNG') }}" alt="Moose Jobs" class="h-full w-full object-cover">
            </div>
            <div>
                <div class="text-lg font-bold leading-tight">Moose Jobs</div>
                <div class="text-sm text-gray-600">Local labor platform in Moose Jaw</div>
            </div>
        </div>

        <nav class="flex items-center gap-2">
            @auth
                <a href="{{ url('/dashboard') }}" class="px-4 py-2 rounded-lg bg-black text-white text-sm">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="px-4 py-2 rounded-lg border text-sm">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg border text-sm">Login</a>
                <a href="{{ route('register') }}" class="px-4 py-2 rounded-lg bg-black text-white text-sm">Register</a>
            @endauth
        </nav>
    </div>
</header>

<main class="max-w-6xl mx-auto px-4 py-10">

    <!-- 🔥 HERO SECTION -->
    <section class="grid md:grid-cols-2 gap-10 items-center">

    <!-- 左侧文字 -->
    <div>
        <h1 class="text-4xl md:text-5xl font-bold leading-tight">
            <span class="text-indigo-600">Local Jobs</span><br>
            Local People.<br>
            Get Things Done Fast.
        </h1>

        <p class="mt-5 text-gray-600 text-lg">
            Find trusted local help or start earning today, -
            such as developers, handymen, HVAC technicians, and delivery runners.
        </p>

        <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ url('/tasks') }}"
               class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium shadow">
                Find Tasks
            </a>

            <a href="{{ url('/tasks/create') }}"
               class="px-6 py-3 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium shadow">
                Post a Task
            </a>
        </div>

        <div class="mt-6 text-sm text-gray-500">
            ✔ Fast • ✔ Local • ✔ Flexible
        </div>
    </div>

    <div 
    x-data="{
    current: 0,
    total: {{ $announcements->count() }},
    ids: @json($announcements->pluck('id')),
    activeId: null,
    timer: null,

    start() {
        if (this.total <= 1) return;

        this.syncId();

        this.timer = setInterval(() => {
            this.next()
        }, 3000)
    },

    stop() {
        clearInterval(this.timer)
    },

    next() {
        this.current = (this.current + 1) % this.total
        this.syncId()
    },

    prev() {
        this.current = (this.current - 1 + this.total) % this.total
        this.syncId()
    },

    syncId() {
        this.activeId = this.ids[this.current]
    }
}"  
    x-init="start()"
    @mouseenter="stop()"
    @mouseleave="start()"
    class="relative w-full h-[320px] overflow-hidden rounded-2xl shadow-lg"
>

    @foreach($announcements as $a)
        <a href="{{ route('admin.announcements.show', $a->id) }}"
        x-show="current === {{ $loop->index }}"
        class="absolute inset-0">

            <img 
                src="{{ asset('storage/' . $a->image) }}"
                class="w-full h-full object-cover cursor-pointer"
            >
        </a>
    @endforeach
         <!-- 🟢 左下角浮动卡片（新增的就在这里👇） -->
    <div class="absolute bottom-4 left-4 z-20">

        @foreach($announcements as $index => $a)
            <div
                x-show="current === {{ $index }}"
                x-transition
                class="bg-white bg-opacity-90 backdrop-blur p-3 rounded-xl shadow-lg w-[220px]"
            >

                <!-- 类型 -->
                <div class="text-xs text-indigo-600 font-semibold">
                    {{ $a->type }}
                </div>

                <!-- 标题 -->
                <div class="text-sm font-medium text-gray-900 mt-1 line-clamp-2">
                    {{ $a->title }}
                </div>

                <!-- 地点 -->
                <div class="text-xs text-gray-500 mt-1">
                    Moose Jaw
                </div>

            </div>
        @endforeach

    </div>
    <!-- 🟢 左下角浮动卡片结束 -->
    <!-- ⬅ 左右按钮 -->
    <button @click="prev"
        class="absolute left-3 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white px-2 py-1 rounded-full shadow">
        ◀
    </button>

    <button @click="next"
        class="absolute right-3 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white px-2 py-1 rounded-full shadow">
        ▶
    </button>

    <!-- ⏺ 小圆点 -->
    <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-2">
        @foreach($announcements as $index => $a)
            <div 
                @click="current = {{ $index }}"
                :class="current === {{ $index }} ? 'bg-white' : 'bg-white/50'"
                class="w-2.5 h-2.5 rounded-full cursor-pointer transition">
            </div>
        @endforeach
    </div>

</div>

</section>

    <!-- 👇 你原来的 worker 区（稍微优化标题） -->
    <section class="mt-14">
        <div class="flex items-end justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold">Top Local Helpers</h2>
                <p class="text-sm text-gray-600 mt-1">
                    Trusted people in your community.
                </p>
            </div>
        </div>

        <div x-data="{ selected: 'all' }">

            <!-- 分类 -->
            <div class="bg-white border rounded-2xl p-5 shadow-sm hover:shadow-md transition">
                <div class="text-sm font-semibold">Filter by Category</div>
                <div class="mt-4 flex flex-wrap gap-2 text-sm">
                        <button @click="selected='all'"
                            class="px-4 py-2 rounded-xl border"
                            :class="selected==='all' ? 'bg-black text-white' : 'bg-gray-50'">
                            All
                        </button>
                    @foreach($categories as $cat)
                    <button
                    @click="selected={{ $cat->id }}"
                    class="px-4 py-2 rounded-xl border flex items-center gap-2 transition"

                    :class="selected == {{ $cat->id }}
                        ? 'bg-black text-white'
                        : '{{ $cat->color_classes['bg'] }} {{ $cat->color_classes['text'] }}'">

                        {{-- 🎯 icon --}}
                        @if($cat->icon)
                             {{-- icon（emoji） --}}
                            <span class="text-xs">
                                {{ $cat->icon_emoji }}
                            </span>
                        @else
                            <span class="text-xs">📦</span>
                        @endif

                        {{-- 🎨 color dot --}}
                         <span class="w-2 h-2 rounded-full {{ $cat->color_classes['dot'] }}"></span>


                        {{-- 🏷️ name --}}
                        <span class="truncate">
                            {{ $cat->name }}
                        </span>

                    </button>
                @endforeach

                </div>
            </div>

            <!-- workers -->
            <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse($workers as $p)

                    @php
                        $profile = $p->profile;
                        $catIds = $profile?->categories?->pluck('id')->values()->all() ?? [];

                        $avatarPath = $profile?->avatar;
                        $avatar = $avatarPath
                            ? asset('storage/' . $avatarPath)
                            : asset('images/man.jpg');
                          
                        // 假评分（4.2 - 5.0）
                        $fakeRating = rand(42, 50) / 10;

                        // 假评价数量
                        $fakeReviews = rand(5, 180);

                        // 星星计算
                        $fullStars = floor($fakeRating);
                        $halfStar = ($fakeRating - $fullStars) >= 0.5;
                    @endphp

                    <div class="bg-white border rounded-2xl p-5 hover:shadow-xl hover:-translate-y-1 transition-all duration-300""
                        x-show="selected==='all' || @js($catIds).includes(parseInt(selected))"
                        x-transition>

                        <!-- 👤 头像 + 名字 -->
                        <div class="flex items-center gap-4">
                            <div class="h-12 w-12 rounded-full overflow-hidden bg-gray-100">
                                <img src="{{ $avatar }}"
                                    class="h-full w-full object-cover"
                                    alt="avatar"
                                    onerror="this.src='{{ asset('images/man.jpg') }}'">
                            </div>

                            <div class="min-w-0">
                                <div class="font-semibold truncate">
                                    {{ $p->display_name ?? $p->name ?? 'Service Provider' }}
                                </div>
                                <div class="text-sm text-gray-600 truncate">
                                    {{ $profile?->city ?? 'Moose Jaw' }}
                                </div>
                            </div>
                        </div>

                        <!-- 📞 电话 -->
                        <div class="mt-4 text-sm text-gray-700">

                            @if($profile?->phone)

                                <div class="flex gap-3 mt-2">

                                    {{-- WhatsApp --}}
                                    <a href="https://wa.me/{{ $profile->phone }}?text={{ rawurlencode('Hi, I found you on MooseJobs. Are you available?') }}"
                                    target="_blank"
                                    class="text-green-600 font-semibold hover:underline">
                                        🟢 Contact via WhatsApp
                                    </a>

                                    {{-- SMS --}}
                                    <a href="sms:{{ $profile->phone }}?body={{ rawurlencode('Hi, I found you on MooseJobs.') }}"
                                    class="text-gray-600 hover:underline">
                                        💬 SMS
                                    </a>

                                </div>

                            @else

                                <span class="text-gray-400">
                                    Contact available after request
                                </span>

                            @endif

                        </div>

                    <p class="text-sm text-gray-500 mt-2">
                        Usually responds within 1 hour
                    </p>
                        <!-- 📝 简介 -->
                        <div x-data="{ expanded: false }" class="mt-3">

                            <div class="p-3 bg-gray-50 rounded-xl text-sm text-gray-700 leading-snug transition-all duration-300 hover:shadow-md">

                                <!-- bio 内容 -->
                                <div
                                    :class="expanded ? '' : 'max-h-20 overflow-hidden'"
                                    class="whitespace-pre-line transition-all duration-300 "
                                >
                                    {{ $profile?->bio ?: 'Experienced local helper. Fast response and fair pricing.' }}
                                </div>

                                <!-- 渐隐效果（未展开时） -->
                                <div x-show="!expanded" class="h-5 -mt-5 bg-gradient-to-t from-gray-50 to-transparent"></div>

                                <!-- 按钮 -->
                                <button
                                    @click="expanded = !expanded"
                                    class="mt-1 text-xs font-medium text-indigo-600 hover:underline"
                                >
                                    <span x-show="!expanded">Read more</span>
                                    <span x-show="expanded">Show less</span>
                                </button>

                            </div>

                        </div>
                        
                        <!-- 🏷️ 技能标签 -->
                        @php
                            $skillsRaw = $profile?->skills ?? '';
                            $skills = collect(array_filter(array_map('trim', explode(',', $skillsRaw))))->take(6);
                        @endphp
                       @if($profile?->categories?->isNotEmpty())
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach($profile->categories as $cat)
                                    <span class="px-2 py-1 rounded-full text-xs flex items-center gap-1
                                        {{ $cat->color_classes['bg'] }}
                                        {{ $cat->color_classes['text'] }}">

                                         {{-- icon（emoji） --}}
                                        <span class="text-xs">
                                            {{ $cat->icon_emoji }}
                                        </span>

                                        {{-- dot --}}
                                        <span class="w-1.5 h-1.5 rounded-full {{ $cat->color_classes['dot'] }}"></span>

                                        {{ $cat->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    
                        <div class="flex items-center gap-1 mt-2 text-sm">
                            <div class="flex text-yellow-500">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $fullStars)
                                        ★
                                    @elseif ($halfStar && $i == $fullStars + 1)
                                        ☆
                                    @else
                                        ☆
                                    @endif
                                @endfor
                            </div>

                            <span class="text-gray-700 font-medium ml-1">
                                {{ number_format($fakeRating, 1) }}
                            </span>

                            <span class="text-gray-400 text-xs">
                                ({{ $fakeReviews }})
                            </span>
                        </div>
                        <div class="mt-1 flex items-center gap-2 text-xs">
                            <span class="text-green-600 font-medium">
                                ✔ Verified Helper
                            </span>

                            <span class="text-gray-400">
                                • Responds fast
                            </span>
                        </div>
                        <!-- 👉 操作 -->
                        <div class="mt-4 flex items-center justify-between">
                            <a href="{{ url('/workers/'.$p->id) }}"
                            class="text-sm font-medium text-indigo-600 hover:underline">
                                View Profile →
                            </a>
                        </div>

                    </div>

                @empty
                    <div class="text-gray-600 bg-white border rounded-2xl p-6">
                        No workers yet. Be the first to join!
                    </div>
                @endforelse
            </div>
    </section>

    <!-- 🚀 CTA -->
    <section class="mt-16 text-center bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl py-10 px-6">
        <h2 class="text-2xl font-semibold">
            Get Things Done Locally
        </h2>

        <p class="mt-3 text-indigo-100">
            Post a task or start earning today.
        </p>

        <div class="mt-6 flex justify-center gap-3">
            <a href="{{ url('/tasks/create') }}"
            class="px-6 py-3 bg-white text-indigo-600 rounded-xl text-sm font-medium shadow">
                Post a Task
            </a>

            <a href="{{ url('/tasks') }}"
            class="px-6 py-3 border border-white rounded-xl text-sm font-medium">
                Browse Tasks
            </a>
        </div>
    </section>

</main>
<div class="max-w-2xl mx-auto mt-10 p-6 bg-white shadow rounded-lg">

    <!-- 提示 -->
    <div class="mb-6 text-sm text-gray-600">
        This website is only an auxiliary tool. For specific requests, please contact me directly.
    </div>

    <!-- 成功提示 -->
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    <!-- Form -->
    <form method="POST" action="/contact" class="space-y-4">
        @csrf

        <input type="text" name="name" placeholder="Your Name"
            class="w-full border p-2 rounded" required>

        <input type="email" name="email" placeholder="Your Email"
            class="w-full border p-2 rounded" required>

        <textarea name="message" placeholder="Message"
            class="w-full border p-2 rounded h-32" required></textarea>

        <button class="w-full bg-indigo-600 text-white py-2 rounded">
            Send
        </button>
    </form>
</div>
<footer class="border-t bg-white">
    <div class="max-w-6xl mx-auto px-4 py-8 text-sm text-gray-600">
        © {{ date('Y') }} Moose Jobs — Built with Laravel
    </div>
</footer>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

</body>
</html>
