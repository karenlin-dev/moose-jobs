<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">
                {{ $task->title }}
            </h2>

            <a href="{{ route('tasks.index') }}" class="text-sm text-gray-600 hover:underline">
                ← Back to Tasks
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto py-8 space-y-4">

        <div class="bg-white p-6 rounded shadow space-y-3">
            <div class="text-sm text-gray-500">
                City: {{ $task->city ?? 'Moose Jaw' }}
                · Budget: ${{ $task->budget ?? 'Negotiable' }}
                · Status: {{ strtoupper($task->status ?? 'OPEN') }}
            </div>

            @if($task->category)
                <span class="inline-block text-xs bg-gray-100 px-2 py-1 rounded">
                    {{ $task->category->name }}
                </span>
            @endif
             {{-- 跑腿任务地址 --}}
            @if($task->category?->slug === 'errand')
                <div class="mt-2 text-sm text-gray-600">
                    <p><strong>Pickup Address:</strong> {{ $task->pickup_address ?? 'N/A' }}</p>
                    <p><strong>Drop-off Address:</strong> {{ $task->dropoff_address ?? 'N/A' }}</p>
                </div>
            @endif
            <hr>

            <h3 class="font-semibold">Description</h3>
            <p class="text-gray-700 whitespace-pre-line">
                {{ $task->description }}
            </p>
            @if($task->photos->count())
                <div class="mt-6">
                    <h3 class="font-semibold mb-2">Photos</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach($task->photos as $photo)
                            <img src="{{ asset('storage/'.$photo->path) }}"
                                class="w-full h-40 object-cover rounded-lg border"
                                alt="task photo">
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

        {{-- Delivery Status --}}
        @if($task->pickup_address || $task->dropoff_address)
            @php
                $statusOrder = ['pending', 'in_transit', 'delivered'];
                $labels = [
                    'pending' => 'Waiting for Pickup',
                    'in_transit' => 'In Transit',
                    'delivered' => 'Delivered',
                ];
                $currentIndex = array_search($task->delivery_status ?? 'pending', $statusOrder);
            @endphp

            <div class="mt-6">
                <h3 class="font-semibold mb-4">Delivery Status</h3>

                <div class="flex items-center">
                    @foreach($statusOrder as $index => $key)
                        {{-- Step --}}
                        <div class="flex items-center">
                            {{-- 圆点 --}}
                            <div
                                class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                                {{ $index < $currentIndex ? 'bg-green-500 text-white' : '' }}
                                {{ $index === $currentIndex && $key !== 'pending' ? 'bg-blue-500 text-white' : '' }}
                                {{ $index > $currentIndex || ($key === 'pending' && $currentIndex === 0) ? 'bg-gray-300 text-gray-600' : '' }}"
                            >
                                {{ $index + 1 }}
                            </div>

                            {{-- 连接线 --}}
                            @if(!$loop->last)
                                <div class="w-16 h-1
                                    {{ $index < $currentIndex ? 'bg-green-500' : 'bg-gray-300' }}">
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- 状态文字 --}}
                <div class="flex justify-between text-xs text-gray-600 mt-2">
                    @foreach($statusOrder as $key)
                        <span>{{ $labels[$key] }}</span>
                    @endforeach
                </div>
            </div>
        @endif
        @if($task->pickup_address && $task->dropoff_address)
            <div class="mt-6 space-y-4">
                <h3 class="font-semibold">Delivery Route</h3>

                <div class="w-full h-80 rounded overflow-hidden border">
                    <iframe
                        width="100%"
                        height="100%"
                        style="border:0"
                        loading="lazy"
                        allowfullscreen
                        src="https://www.google.com/maps?saddr={{ urlencode($task->pickup_address) }}&daddr={{ urlencode($task->dropoff_address) }}&output=embed">
                    </iframe>
                </div>
            </div>
        @endif

        {{-- ===================== --}}
        {{-- 投标入口（Worker） --}}
        {{-- ===================== --}}
        <div class="bg-white p-6 rounded shadow">
            @auth
                @if(auth()->user()->role === 'worker')
                    @if(($task->status ?? 'open') === 'open')
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-semibold">Want to bid?</div>
                                <div class="text-sm text-gray-600">Submit your price and message.</div>
                            </div>

                            <a href="{{ route('bids.create', $task) }}"
                               class="px-4 py-2 rounded bg-indigo-600 text-white text-sm">
                                Place a Bid
                            </a>
                        </div>
                    @else
                        <div class="text-sm text-gray-600">This task is not open for bids.</div>
                    @endif
                @else
                    <div class="text-sm text-gray-600">
                        Only service providers (workers) can bid.
                    </div>
                @endif
            @else
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        Login to place a bid as a worker.
                    </div>
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded bg-black text-white text-sm">
                        Login
                    </a>
                </div>
            @endauth
        </div>

        {{-- ===================== --}}
        {{-- 投标列表（Employer 自己） --}}
        {{-- ===================== --}}
        @auth
            @if(auth()->user()->role === 'employer' && auth()->id() === $task->user_id)
                <div class="bg-white p-6 rounded shadow space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold">Bids</h3>
                        <span class="text-sm text-gray-500">
                            {{ $task->bids?->count() ?? 0 }} total
                        </span>
                    </div>

                    @forelse($task->bids as $bid)
                        <div class="border rounded p-4 space-y-1">
                            <div class="text-sm text-gray-700">
                                <span class="font-semibold">Worker:</span>
                                {{ $bid->user->name ?? 'Worker' }}
                            </div>
                            <div class="text-sm text-gray-700">
                                <span class="font-semibold">Price:</span> ${{ $bid->price }}
                            </div>
                            <div class="text-sm text-gray-700">
                                <span class="font-semibold">Status:</span> {{ strtoupper($bid->status) }}
                            </div>

                            @if($bid->status === 'pending' && ($task->status ?? 'open') === 'open')
                                <form method="POST" action="{{ route('bids.accept', $bid) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="mt-2 px-3 py-1 rounded bg-black text-white text-sm" type="submit">
                                        Accept
                                    </button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-500">No bids yet.</p>
                    @endforelse
                </div>
            @endif
        @endauth

    </div>
</x-app-layout>
