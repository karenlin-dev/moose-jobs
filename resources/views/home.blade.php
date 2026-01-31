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
    <section class="grid md:grid-cols-2 gap-8 items-center">
        <div>
            <h1 class="text-3xl md:text-4xl font-bold leading-tight">
                Hire trusted local helpers — fast.
            </h1>
            <p class="mt-4 text-gray-700">
                Moving, decorating, maintenance, web design, baking… post a task and get bids from service provider.
            </p>

            <div class="mt-6 flex gap-3">
                <a href="{{ url('/tasks') }}" class="px-5 py-3 rounded-xl bg-black text-white text-sm">
                    Browse Tasks
                </a>
                @auth
                    @if(auth()->user()->isEmployer())
                        <a href="{{ url('/tasks/create') }}" class="px-5 py-3 rounded-xl border text-sm">
                            Post a Task
                        </a>
                    @endif
                @endauth

            </div>

            <div class="mt-6 text-sm text-gray-500">
                Tip: After login, you should land on <code>/</code> (home) instead of <code>/home</code>.
            </div>
        </div>

    </section>

    <section class="mt-12">
        <div class="flex items-end justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold">Featured Service Providers</h2>
                <p class="text-sm text-gray-600 mt-1">A few providers to get you started.</p>
            </div>
            <a href="{{ url('/workers') }}" class="text-sm hover:underline">View all</a>
        </div>
        <div x-data="{ selected: 'all' }">
        {{-- 分类区 --}}
            <div class="bg-white border rounded-2xl p-6">
                <div class="text-sm font-semibold">Popular Categories</div>

                <div class="mt-4 flex flex-wrap gap-2 text-sm">
                    <button type="button"
                        @click="selected='all'"
                        class="px-4 py-2 rounded-xl border"
                        :class="selected==='all' ? 'bg-black text-white border-black' : 'bg-gray-50 hover:bg-gray-100'">
                        All
                    </button>

                    @foreach($categories as $cat)
                        <button type="button"
                            @click="selected='{{ $cat->id }}'"
                            class="px-4 py-2 rounded-xl border"
                            :class="selected==='{{ $cat->id }}' ? 'bg-black text-white border-black' : 'bg-gray-50 hover:bg-gray-100'">
                            {{ $cat->name }}
                        </button>
                    @endforeach
                </div>
            </div>
        
            <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse($workers as $p)
                    @php
                        $profile = $p->profile;
                        $catIds = $profile?->categories?->pluck('id')->values()->all() ?? [];
                    @endphp
                    <div class="bg-white border rounded-2xl p-5" 
                        x-show="selected==='all' || @js($catIds).includes(parseInt(selected))"
                        x-transition>
                        <div class="flex items-center gap-4">
                            <div class="h-12 w-12 rounded-full overflow-hidden bg-gray-100 flex items-center justify-center">
                                @php
                                    $avatarPath = $p->profile?->avatar; // 注意：profile 可能为空
                                    $avatar = $avatarPath
                                        ? asset('storage/' . $avatarPath)
                                        : asset('images/default-avatar.png');
                                @endphp

                                @if($avatar)
                                    <img src="{{ $avatar }}" class="h-full w-full object-cover" alt="avatar"
                                        onerror="this.style.display='none'">
                                @else
                                    <span class="text-xs text-gray-400">Avatar</span>
                                @endif
                            </div>

                            <div class="min-w-0">
                                <div class="font-semibold truncate">
                                    {{ $p->display_name ?? $p->name ?? 'Service Provider' }}
                                </div>
                                <div class="text-sm text-gray-600 truncate">
                                    {{ $p->profile?->city ?? 'Moose Jaw' }}
                                </div>

                            </div>
                        </div>
                        <div class="mt-4 text-sm text-gray-700">
                           {{ $p->profile?->phone ?? 'Service Provider' }}
                        </div>
                        <div class="mt-4 text-sm text-gray-700">
                            {{ $p->profile?->bio ?: 'Experienced local helper. Fast response and fair pricing.' }}
                        </div>
                        @php
                            $skillsRaw = $p->profile?->skills ?? '';
                            $skills = collect(array_filter(array_map('trim', explode(',', $skillsRaw))))->take(6);
                        @endphp

                        @if($skills->isNotEmpty())
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach($skills as $s)
                                    <span class="text-xs px-2 py-1 rounded-full border bg-gray-50 text-gray-700">
                                        {{ $s }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                        <div class="mt-4 flex items-center justify-between">
                            <a href="{{ url('/workers/'.$p->id) }}" class="text-sm font-medium hover:underline">
                                View
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-gray-600 bg-white border rounded-2xl p-6">
                        No workers yet. Register as a service workers to appear here.
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</main>

<footer class="border-t bg-white">
    <div class="max-w-6xl mx-auto px-4 py-8 text-sm text-gray-600">
        © {{ date('Y') }} Moose Jobs — Built with Laravel
    </div>
</footer>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

</body>
</html>
