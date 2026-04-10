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
                         @if($photo->isVideo())
                            <video controls class="w-full h-32 object-cover rounded">
                                <source src="{{ asset('storage/'.$photo->path) }}" type="video/mp4">
                            </video>
                        @else
                            <img src="{{ asset('storage/'.$photo->path) }}"
                                class="w-full h-32 object-cover rounded"
                                onclick="openPreview(this.src)">
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

    </div>
    <!-- Image Preview Modal -->
    <div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50">
        <span class="absolute top-5 right-5 text-white text-3xl cursor-pointer" onclick="closePreview()">×</span>

        <img id="modalImg" class="max-w-[90%] max-h-[90%] rounded shadow-lg">
    </div>

    <script>
    function openPreview(src) {
        document.getElementById('imageModal').classList.remove('hidden');
        document.getElementById('modalImg').src = src;
    }

    function closePreview() {
        document.getElementById('imageModal').classList.add('hidden');
    }
    </script>
</x-app-layout>


