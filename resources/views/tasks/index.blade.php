<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">
                Available Tasks
            </h2>

            @auth
                @if(auth()->user()->role === 'employer')
                    <a href="{{ route('tasks.create') }}"
                       class="px-4 py-2 rounded bg-black text-white text-sm">
                        Post a Task
                    </a>
                @endif
            @endauth
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto py-8 space-y-4">
        @forelse ($tasks as $task)
            <div class="bg-white p-6 rounded shadow flex justify-between items-start gap-6">
                <div class="space-y-2 min-w-0">
                    <h3 class="text-lg font-semibold truncate">
                        <a href="{{ route('tasks.show', $task) }}" class="hover:underline">
                            {{ $task->title }}
                        </a>
                    </h3>

                    <p class="text-sm text-gray-600 line-clamp-2">
                        {{ $task->description }}
                    </p>

                    <p class="text-sm text-gray-500">
                        City: {{ $task->city ?? 'Moose Jaw' }} ·
                        Budget: ${{ $task->budget ?? 'Negotiable' }}
                    </p>

                    @if($task->category)
                        <span class="inline-block text-xs bg-gray-100 px-2 py-1 rounded">
                            {{ $task->category->name }}
                        </span>
                    @endif
                </div>

                <div class="shrink-0 flex flex-col items-end gap-2">
                    <a href="{{ route('tasks.show', $task) }}"
                    class="text-gray-700 hover:underline text-sm">
                        View →
                    </a>

                    {{-- Edit：只有 task owner 才能看到 --}}
                    @auth
                        @if(
                            auth()->user()->role === 'employer' &&
                            auth()->id() === $task->user_id
                        )
                            <a href="{{ route('tasks.edit', $task) }}"
                            class="text-sm text-gray-500 hover:text-black hover:underline">
                                Edit ✎
                            </a>
                        @endif
                    @endauth
                    {{-- 只有登录且是 worker 才能投标 --}}
                    @auth
                        @if(auth()->user()->role === 'worker')
                            <a href="{{ route('bids.create', $task) }}"
                               class="text-indigo-600 hover:underline text-sm">
                                Bid →
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}"
                           class="text-indigo-600 hover:underline text-sm">
                            Login to bid
                        </a>
                    @endauth
                </div>
            </div>
        @empty
            <p class="text-gray-500">No tasks available.</p>
        @endforelse

        <div class="pt-4">
            {{ $tasks->links() }}
        </div>
    </div>
</x-app-layout>
