<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">跑腿任务列表</h2>
    </x-slot>

    <div class="max-w-5xl mx-auto py-8 space-y-6">
        @forelse($tasks as $task)
            @include('tasks._task_card', ['task' => $task])
        @empty
            <p class="text-gray-500">暂无跑腿任务。</p>
        @endforelse
        <div class="pt-4">{{ $tasks->links() }}</div>
    </div>
</x-app-layout>
