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
            <div class="h-10 w-10 rounded-xl overflow-hidden bg-gray-100 flex items-center justify-center">
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
            images: [
                '{{ asset('images/web.jpg') }}',
                '{{ asset('images/heating.jpg') }}',
                '{{ asset('images/handyman.jpg') }}',
                '{{ asset('images/delivery.jpg') }}'
            ],
            current: 0,
            start() {
                setInterval(() => {
                    this.current = (this.current + 1) % this.images.length
                }, 3000)
            }
        }"
        x-init="start()"
        class="relative"
    >

        <!-- 图片 -->
        <template x-for="(img, index) in images" :key="index">
            <img 
                :src="img"
                x-show="current === index"
                x-transition:enter="transition ease-out duration-700"
                x-transition:enter-start="opacity-0 scale-105"
                x-transition:enter-end="opacity-100 scale-100"
                class="rounded-2xl shadow-lg object-cover w-full h-[320px] absolute inset-0"
            >
        </template>

        <!-- 占位高度（防止塌陷） -->
        <div class="h-[320px]"></div>

        <!-- 浮动卡片 -->
        <div class="absolute bottom-4 left-4 bg-white p-4 rounded-xl shadow-lg text-sm">
            <div x-text="[
                '💻 Web Development',
                '🔥 Heating Service',
                '🔧 Handyman Help',
                '📦 Delivery Service'
            ][current]"></div>
            <div class="text-gray-500 text-xs mt-1">Moose Jaw</div>
        </div>

    </div>

</section>

    <!-- 🧩 CATEGORIES -->
   <section class="mt-14">
        <h2 class="text-xl font-semibold">Popular Services</h2>

        <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div class="p-4 rounded-xl bg-pink-50 text-pink-700 text-center">💻 Web Dev</div>
            <div class="p-4 rounded-xl bg-red-50 text-red-700 text-center">🔥 Heating</div>
            <div class="p-4 rounded-xl bg-yellow-50 text-yellow-700 text-center">🔨 Home Renovation</div>
             <div class="p-4 rounded-xl bg-purple-50 text-purple-700 text-center">🔨 Ceiling Maintenance</div>
            <div class="p-4 rounded-xl bg-gray-100 text-gray-700 text-center">🛒 Errands</div>   
            <div class="p-4 rounded-xl bg-blue-50 text-blue-700 text-center">🚚 Moving</div>
            <div class="p-4 rounded-xl bg-green-50 text-green-700 text-center">🧹 Cleaning</div>
            <div class="p-4 rounded-xl bg-teal-50 text-teal-700 text-center">🌿 Yard Work</div>
            

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
            <a href="{{ url('/workers') }}" class="text-sm hover:underline">View all</a>
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
                        <button @click="selected='{{ $cat->id }}'"
                            class="px-4 py-2 rounded-xl border"
                            :class="selected==='{{ $cat->id }}' ? 'bg-black text-white' : 'bg-gray-50'">
                            {{ $cat->name }}
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
                    @endphp

                    <div class="bg-white border rounded-2xl p-5 shadow-sm hover:shadow-md transition"
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
                        <div class="mt-3 text-sm text-gray-700">
                            {{ $profile?->bio ?: 'Experienced local helper. Fast response and fair pricing.' }}
                        </div>

                        <!-- 🏷️ 技能标签 -->
                        @php
                            $skillsRaw = $profile?->skills ?? '';
                            $skills = collect(array_filter(array_map('trim', explode(',', $skillsRaw))))->take(6);
                        @endphp

                        @if($skills->isNotEmpty())
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach($skills as $s)
                                    <span class="text-xs px-2 py-1 rounded-full bg-indigo-50 text-indigo-600">
                                        {{ $s }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

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

        <input type="text" name="name" placeholder="Name"
            class="w-full border p-2 rounded" required>

        <input type="email" name="email" placeholder="Email"
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
