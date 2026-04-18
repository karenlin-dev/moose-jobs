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
            <p><strong>Bio:</strong>
       <div class="mt-3 p-4 bg-white border rounded-xl text-sm text-gray-700 leading-relaxed whitespace-pre-line break-words shadow-sm">
            {{ $worker->profile->bio ?? 'No bio added yet.' }}
        </div>
        @if($worker->profile->facebook_url)
            <div class="mb-4">
            <a href="{{ $worker->profile->facebook_url }}" target="_blank"
            class="inline-flex items-center gap-2 bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                📘 Facebook
            </a>
            </div>
        @endif
            <p><strong>Joined:</strong> {{ $worker->created_at->format('Y-m-d') }}</p>
        </div>
        @php
            // 假评分（4.2 - 5.0）
            $fakeRating = rand(42, 50) / 10;

            // 假评价数量
            $fakeReviews = rand(5, 180);

            // 星星计算
            $fullStars = floor($fakeRating);
            $halfStar = ($fakeRating - $fullStars) >= 0.5;
        @endphp
        <div class="flex items-center gap-1 mt-2 text-sm">
        <div class="flex text-yellow-500">
                @for ($i = 1; $i <= 5; $i++)
                    @if ($i <= $fullStars)
                        ★
                    @elseif ($halfStar && $i == $fullStars + 1)
                        ☆
                    @else
                        ☆
                    @endif
                @endfor
            </div>

            <span class="text-gray-700 font-medium ml-1">
                {{ number_format($fakeRating, 1) }}
            </span>

            <span class="text-gray-400 text-xs">
                ({{ $fakeReviews }})
            </span>
        </div>
        <div class="mt-1 flex items-center gap-2 text-xs">
            <span class="text-green-600 font-medium">
                ✔ Verified Helper
            </span>

            <span class="text-gray-400">
                • Responds fast
            </span>
        </div>
        @if($worker->profile->photos && $worker->profile->photos->count())
            <div class="mb-6">
                <div class="font-medium mb-2">Current Photos</div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($worker->profile->photos as $photo)
                         @if($photo->isVideo())
                            <div class="w-full h-40 rounded overflow-hidden bg-black flex items-center justify-center">
                                <video controls class="w-full h-full object-contain">
                                    <source src="{{ asset('storage/'.$photo->path) }}" type="video/mp4">
                                </video>
                            </div>
                        @else
                            <img src="{{ asset('storage/'.$photo->path) }}"
                                class="w-full h-32 object-cover rounded"
                                onclick="openPreview(this.src)">
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
<!-- Image Preview Modal -->
    </div>

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


