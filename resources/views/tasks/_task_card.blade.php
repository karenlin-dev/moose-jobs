<div class="bg-white p-6 rounded shadow flex justify-between items-start gap-6">
    <div class="space-y-2 min-w-0">
        <h3 class="text-lg font-semibold truncate">
            <a href="{{ route('tasks.show', $task) }}" class="hover:underline">{{ $task->title }}</a>
        </h3>

        <p class="text-sm text-gray-600 line-clamp-2">{{ $task->description }}</p>

        <p class="text-sm text-gray-500">
            Budget: ${{ $task->budget ?? 'Negotiable' }} · Status: {{ $task->status }}
        </p>

        @if($task->category)
            <span class="inline-block text-xs bg-gray-100 px-2 py-1 rounded">{{ $task->category->name }}</span>
        @endif

        {{-- 如果是跑腿任务，显示发货和送货地址 --}}
        @if($task->category?->slug === 'errand')
            <div class="mt-1 text-sm text-gray-600">
                <p>Pickup: {{ $task->pickup_address ?? 'N/A' }}</p>
                <p>Drop-off: {{ $task->dropoff_address ?? 'N/A' }}</p>
            </div>
        @endif
    </div>

    <div class="shrink-0 flex flex-col items-end gap-2">
        <a href="{{ route('tasks.show', $task) }}" class="text-gray-700 hover:underline text-sm">View →</a>

        {{-- 快速操作按钮 --}}
        @auth
            @if(auth()->id() === $task->user_id)
                @if($task->status !== 'completed')
                    <button class="text-sm text-green-600 hover:text-green-800 hover:underline"
                            onclick="quickComplete({{ $task->id }})">Mark Complete ✓</button>
                @endif
                @if($task->bids->where('status', 'pending')->count() > 0)
                    <button class="text-sm text-indigo-600 hover:text-indigo-800 hover:underline"
                            onclick="quickAccept({{ $task->id }})">Accept Bid</button>
                @endif
            @endif
        @endauth
    </div>
</div>
