<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            {{ $assignment->task->title }}
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-8 space-y-4">
        <p><strong>Task:</strong> {{ $assignment->task->title }}</p>
        <p><strong>Description:</strong> {{ $assignment->task->description }}</p>
        <p><strong>Budget:</strong> ${{ $assignment->task->budget }}</p>
        <p><strong>Assigned By:</strong> {{ $assignment->employer->name }}</p>
        <p><strong>Started At:</strong> {{ $assignment->started_at->format('Y-m-d') }}</p>
        @if($assignment->task->status !== 'completed')
            <form method="POST" action="{{ route('assignments.complete', $assignment) }}">
                @csrf
                @method('PATCH')
                <x-primary-button>Mark Completed</x-primary-button>
            </form>
        @else
            <span class="text-green-600 font-semibold">Completed</span>
        @endif
    </div>
</x-app-layout>
