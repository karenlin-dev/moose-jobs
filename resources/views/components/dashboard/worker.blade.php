<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Worker Dashboard
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-8 grid grid-cols-1 md:grid-cols-3 gap-8">

        {{-- 左侧：Worker 资料 --}}
        <div class="space-y-6">
            <div class="bg-white p-6 rounded shadow text-left">
                @if($user->profile?->avatar)
                    <img src="{{ asset('storage/' . $user->profile->avatar) }}" alt="Avatar" class="w-32 h-32 rounded-full mx-auto mb-4">
                @else
                    <img src="{{ asset('images/default-avatar.png') }}" alt="Avatar" class="w-32 h-32 rounded-full mx-auto mb-4">
                @endif
                {{-- Info --}}
                    <div class="flex flex-col justify-start">
                        <h2 class="text-xl font-bold">{{ $user->name }}</h2>
                        <p class="text-gray-600"><b>Email:</b> {{ $user->email }}</p>
                        <p class="text-gray-600"><b>City:</b>{{ $user->profile->city ?? 'Moose Jaw' }}</p>
                        <p class="text-gray-600"><b>Phone:</b> {{ $user->profile->phone ?? '-'  }}</p>
                        <p class="text-gray-600"><b>Skills:</b> {{ $user->profile->skills ?? '-' }}</p>
                        <p class="text-gray-600 mt-2"><b>Bio:</b> {{ $user->profile->bio ?? '-'  }}</p>
                        <p class="text-gray-600"><b>Rating:</b> {{ number_format($user->profile->rating ?? 0, 1) }}/5</p>
                        <p class="text-gray-600 mt-2"><b>Total Reviews:</b> {{ $user->profile->total_reviews ?? 0 }}</p>
                    </div>
                {{-- 编辑资料按钮 --}}
                <a href="{{ route('workers.edit') }}"
                   class="mt-4 inline-block px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                   Edit Profile
                </a>
            </div>
        </div>

        {{-- 右侧：任务列表 --}}
        <div class="md:col-span-2 space-y-8"
            x-data="{
                selectedCat: 'all',
                // 统一取 category id（支持 category_id 或 category->id）
                catIdOf(task) {
                    return task.category_id ?? (task.category ? task.category.id : null);
                },
                match(task) {
                    if (this.selectedCat === 'all') return true;
                    const id = this.catIdOf(task);
                    return String(id) === String(this.selectedCat);
                }
            }"
        >

            {{-- Flash message --}}
            @if(session('success'))
                <div class="bg-green-100 text-green-800 p-4 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Filter Bar --}}
            <div class="bg-white border rounded-xl p-4 flex flex-col sm:flex-row sm:items-center gap-3 justify-between">
                <div class="font-semibold text-gray-800">Task Filters</div>

                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600">Category:</label>
                    <select class="border rounded-lg px-3 py-2 text-sm"
                            x-model="selectedCat">
                        <option value="all">All</option>

                        {{-- 这里假设你 controller 里有 $categories --}}
                        @foreach($categories ?? [] as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- 1. 浏览可投标任务 --}}
            <div class="bg-white border rounded-2xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-lg font-semibold">
                        Available Tasks
                        <span class="text-sm text-gray-500 font-normal">({{ $tasks->count() }})</span>
                    </h3>
                </div>

                @if($tasks->isEmpty())
                    <p class="text-gray-500">No tasks available to bid.</p>
                @else
                    <ul class="divide-y divide-gray-200">
                        @foreach($tasks as $task)
                            <li class="py-3 flex justify-between items-center"
                                x-show="match(@js($task))"
                                x-transition
                            >
                                <div class="min-w-0">
                                    {{-- 你原来这里 route('tasks.index', $task) 是不对的（index 不接收 task）
                                        一般应该是 tasks.show。
                                        如果你没有 show route，就先保留原写法。
                                    --}}
                                    <a href="{{ route('tasks.show', $task) }}"
                                    class="text-indigo-600 hover:underline font-medium truncate block">
                                        {{ $task->title }}
                                    </a>

                                    <div class="text-gray-500 text-sm mt-1 flex flex-wrap gap-x-3 gap-y-1">
                                        <span>Budget: ${{ $task->budget }}</span>
                                        <span>City: {{ $task->city }}</span>

                                        {{-- 分类显示（支持 category / category_id） --}}
                                        <span class="text-gray-400">
                                            Category:
                                            {{ $task->category->name ?? ($categories->firstWhere('id', $task->category_id)->name ?? '-') }}
                                        </span>
                                    </div>
                                </div>

                                @php
                                    $alreadyBid = $bids->pluck('job_id')->contains($task->id);
                                @endphp

                                <div class="shrink-0 ml-4">
                                    @if(!$alreadyBid)
                                        <a href="{{ route('bids.create', $task) }}"
                                        class="text-white bg-indigo-600 px-4 py-2 rounded-lg hover:bg-indigo-700 text-sm">
                                            Submit Bid
                                        </a>
                                    @else
                                        <span class="text-gray-500 px-4 py-2 rounded-lg border text-sm">Already Bid</span>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- 2. 已投标任务 --}}
            <div class="bg-white border rounded-2xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-lg font-semibold">
                        My Bids
                        <span class="text-sm text-gray-500 font-normal">({{ $bids->count() }})</span>
                    </h3>
                </div>

                @if($bids->isEmpty())
                    <p class="text-gray-500">You have not placed any bids yet.</p>
                @else
                    <ul class="divide-y divide-gray-200">
                        @foreach($bids as $bid)
                            @php $t = $bid->task; @endphp

                            <li class="py-3 flex justify-between items-center"
                                @if($t) x-show="match(@js($t))" @endif
                                x-transition
                            >
                                <div class="min-w-0">
                                    @if($t)
                                        <a href="{{ route('bids.show', $bid) }}"
                                        class="text-indigo-600 hover:underline font-medium truncate block">
                                            {{ $t->title }}
                                        </a>

                                        <div class="text-gray-500 text-sm mt-1 flex flex-wrap gap-x-3 gap-y-1">
                                            <span>Status: {{ $bid->status }}</span>
                                            <span>{{ $bid->created_at->diffForHumans() }}</span>
                                            <span class="text-gray-400">
                                                Category: {{ $t->category->name ?? '-' }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-gray-400 italic">Task has been deleted</span>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- 3. 已成交任务 --}}
            <div class="bg-white border rounded-2xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-lg font-semibold">
                        My Assignments
                        <span class="text-sm text-gray-500 font-normal">({{ $assignments->count() }})</span>
                    </h3>
                </div>

                @if($assignments->isEmpty())
                    <p class="text-gray-500">You have no assignments yet.</p>
                @else
                    <ul class="divide-y divide-gray-200">
                        @foreach($assignments as $assignment)
                            @php $t = $assignment->task; @endphp

                            <li class="py-3 flex justify-between items-center"
                                @if($t) x-show="match(@js($t))" @endif
                                x-transition
                            >
                                <div class="min-w-0">
                                    <a href="{{ route('assignments.show', $assignment) }}"
                                    class="text-indigo-600 hover:underline font-medium truncate block">
                                        {{ $t->title }}
                                    </a>

                                    <div class="text-gray-500 text-sm mt-1 flex flex-wrap gap-x-3 gap-y-1">
                                        <span>Started: {{ $assignment->started_at->format('Y-m-d') }}</span>
                                        <span class="text-gray-400">Category: {{ $t->category->name ?? '-' }}</span>
                                        <span class="text-gray-400">Status: {{ $t->status }}</span>
                                    </div>
                                </div>

                                <div class="shrink-0 ml-4">
                                    @if($t->status !== 'completed')
                                        <form method="POST" action="{{ route('assignments.complete', $assignment) }}">
                                            @csrf
                                            @method('PATCH')
                                            <x-primary-button>Mark Completed</x-primary-button>
                                        </form>
                                    @else
                                        <span class="text-green-600 font-semibold">Completed</span>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

        </div>

</x-app-layout>
