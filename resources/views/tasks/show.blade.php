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

            <hr>

            <h3 class="font-semibold">Description</h3>
            <p class="text-gray-700 whitespace-pre-line">
                {{ $task->description }}
            </p>
        </div>

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
