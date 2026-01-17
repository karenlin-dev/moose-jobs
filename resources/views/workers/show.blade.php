<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">{{ $worker->name }}'s Profile</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-8 space-y-4">
        <div class="bg-white p-6 rounded shadow">
            @if($worker->profile?->avatar)
                <img src="{{ asset('storage/' . $worker->profile->avatar) }}" alt="Avatar" class="w-32 h-32 rounded-full mb-4">
            @endif

            <p><strong>Name:</strong> {{ $worker->name }}</p>
            <p><strong>Email:</strong> {{ $worker->email }}</p>
            <p><strong>Phone:</strong> {{ $worker->profile->phone ?? '-' }}</p>
            <p><strong>Skills:</strong> {{ $worker->profile->skills ?? '-' }}</p>
            <p><strong>Bio:</strong> {{ $worker->profile->bio ?? '-' }}</p>
            <p><strong>Joined:</strong> {{ $worker->created_at->format('Y-m-d') }}</p>
            {{-- 新增评分显示 --}}
            <p><strong>Rating:</strong> {{ number_format($worker->profile->rating ?? 0, 1) }} / 5</p>
            <p><strong>Total Reviews:</strong> {{ $worker->profile->total_reviews ?? 0 }}</p>
        </div>
    </div>
</x-app-layout>


