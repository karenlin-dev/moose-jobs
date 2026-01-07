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
        <div class="md:col-span-2 space-y-8">

            {{-- Flash message --}}
            @if(session('success'))
                <div class="bg-green-100 text-green-800 p-4 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- 1. 浏览可投标任务 --}}
            <div>
                <h3 class="text-lg font-semibold mb-2">Available Tasks</h3>

                @if($tasks->isEmpty())
                    <p class="text-gray-500">No tasks available to bid.</p>
                @else
                    <ul class="divide-y divide-gray-200">
                        @foreach($tasks as $task)
                            <li class="py-2 flex justify-between items-center">
                                <div>
                                    <a href="{{ route('tasks.index', $task) }}" class="text-indigo-600 hover:underline font-medium">
                                        {{ $task->title }}
                                    </a>
                                    <span class="text-gray-400 text-sm ml-2">
                                        Budget: ${{ $task->budget }} · City: {{ $task->city }}
                                    </span>
                                </div>

                                @php
                                    $alreadyBid = $bids->pluck('job_id')->contains($task->id);
                                @endphp

                                @if(!$alreadyBid)
                                    <a href="{{ route('bids.create', $task) }}"
                                    class="text-white bg-indigo-600 px-4 py-1 rounded hover:bg-indigo-700">
                                        Submit Bid
                                    </a>
                                @else
                                    <span class="text-gray-500 px-4 py-1 rounded border">Already Bid</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- 2. 已投标任务 --}}
            <div>
                <h3 class="text-lg font-semibold mb-2">My Bids</h3>

                @if($bids->isEmpty())
                    <p class="text-gray-500">You have not placed any bids yet.</p>
                @else
                    <ul class="divide-y divide-gray-200">
                        @foreach($bids as $bid)
                        <li class="py-2 flex justify-between items-center">
                            <div>
                            @if($bid->task)
                                <a href="{{ route('bids.show', $bid) }}" class="text-indigo-600 hover:underline">
                                    {{ $bid->task->title }}
                                </a>
                                <span class="text-gray-400 text-sm ml-2">
                                    {{ $bid->status }} - {{ $bid->created_at->diffForHumans() }}
                                </span>
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
            <div>
                <h3 class="text-lg font-semibold mb-2">My Assignments</h3>

                @if($assignments->isEmpty())
                    <p class="text-gray-500">You have no assignments yet.</p>
                @else
                    <ul class="divide-y divide-gray-200">
                        @foreach($assignments as $assignment)
                            <li class="py-2 flex justify-between items-center">
                                <div>
                                    <a href="{{ route('assignments.show', $assignment) }}" class="text-indigo-600 hover:underline">
                                        {{ $assignment->task->title }}
                                    </a>
                                    <span class="text-gray-400 text-sm ml-2">
                                        Started: {{ $assignment->started_at->format('Y-m-d') }}
                                    </span>
                                </div>

                                @if($assignment->task->status !== 'completed')
                                    <form method="POST" action="{{ route('assignments.complete', $assignment) }}">
                                        @csrf
                                        @method('PATCH')
                                        <x-primary-button>Mark Completed</x-primary-button>
                                    </form>
                                @else
                                    <span class="text-green-600 font-semibold">Completed</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
