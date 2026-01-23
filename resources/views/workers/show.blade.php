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
        @if($worker->profile->photos && $worker->profile->photos->count())
            <div class="mb-6">
                <div class="font-medium mb-2">Current Photos</div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($worker->profile->photos as $photo)
                        <div class="border rounded-lg overflow-hidden bg-white">
                            <img
                                src="{{ asset('storage/'.$photo->path) }}"
                                class="w-full h-32 object-cover"
                                alt="photo"
                            />

                            <div class="p-2 flex justify-end">
                                <form method="POST" action="{{ route('workers.photos.destroy', $photo->id) }}"
                                    onsubmit="return confirm('Delete this photo?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-sm text-red-600 hover:underline">Delete</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</x-app-layout>


