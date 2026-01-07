<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Available Tasks
        </h2>
    </x-slot>

    <div class="max-w-5xl mx-auto py-8 space-y-4">
        @forelse ($tasks as $task)
            <div class="bg-white p-6 rounded shadow flex justify-between items-start">
                <div class="space-y-1">
                    <h3 class="text-lg font-semibold">
                        {{ $task->title }}
                    </h3>

                    <p class="text-sm text-gray-600">
                        {{ $task->description }}
                    </p>

                    <p class="text-sm text-gray-500">
                        City: {{ $task->city }} ·
                        Budget: ${{ $task->budget }}
                    </p>

                    @if($task->category)
                        <span class="inline-block text-xs bg-gray-100 px-2 py-1 rounded">
                            {{ $task->category->name }}
                        </span>
                    @endif
                </div>

                {{-- 工人才能投标 --}}
                @if(auth()->user()->role === 'worker')
                    <a href="{{ route('bids.create', $task) }}"
                       class="text-indigo-600 hover:underline mt-1">
                        Bid →
                    </a>
                @endif
            </div>
        @empty
            <p class="text-gray-500">No tasks available.</p>
        @endforelse
    </div>
</x-app-layout>
