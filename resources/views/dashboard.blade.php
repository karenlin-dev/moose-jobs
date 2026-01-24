    <x-slot name="header">
        <div class="flex justify-between items-center w-full">
            {{-- Employer 才能发任务 --}}
            @if(auth()->user()->isEmployer())
                <a href="{{ route('tasks.create') }}"
                   class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    Post Task
                </a>
            @endif
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto py-8 space-y-8">
        {{-- Employer Dashboard --}}
        @if(auth()->user()->isEmployer())
            <x-dashboard.employer :tasks="$tasks" :categories="$categories"/>

        {{-- Worker Dashboard --}}
        @else
            <x-dashboard.worker
                :user="$user"
                :tasks="$tasks"
                :bids="$bids"
                :assignments="$assignments"
                :categories="$categories"
            />
        @endif
    </div>
