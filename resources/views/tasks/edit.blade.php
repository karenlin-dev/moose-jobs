{{-- Task Edit Blade --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Edit Task
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-8">
        <form method="POST"
              action="{{ route('tasks.update', $task) }}"
              enctype="multipart/form-data"
              class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Title --}}
            <div>
                <x-input-label for="title" value="Task Title" />
                <x-text-input id="title" name="title" value="{{ old('title', $task->title) }}" required class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('title')" />
            </div>

            {{-- Description --}}
            <div>
                <x-input-label for="description" value="Description" />
                <textarea id="description" name="description" rows="5" class="mt-1 block w-full border rounded-md" required>{{ old('description', $task->description) }}</textarea>
                <x-input-error :messages="$errors->get('description')" />
            </div>

            {{-- City --}}
            <div>
                <x-input-label for="city" value="City" />
                <x-text-input id="city" name="city" value="{{ old('city', $task->city) }}" required class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('city')" />
            </div>

            {{-- Category --}}
            <div>
                <x-input-label for="category_id" value="Category" />
                <select name="category_id" class="mt-1 block w-full rounded-md border">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" data-slug="{{ $category->slug }}" @selected(old('category_id', $task->category_id) == $category->id)>
                        {{ $category->name }}
                    </option>

                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('category_id')" />
            </div>

            {{-- 跑腿专属字段 --}}
            <div id="errand-fields" class="@if($task->category?->slug !== 'errand') hidden @endif">
                <div class="mt-2">
                    <x-input-label for="pickup_address" value="Pickup Address" />
                    <x-text-input id="pickup_address" name="pickup_address" value="{{ old('pickup_address', $task->pickup_address) }}" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('pickup_address')" />
                </div>

                <div class="mt-2">
                    <x-input-label for="dropoff_address" value="Drop-off Address" />
                    <x-text-input id="dropoff_address" name="dropoff_address" value="{{ old('dropoff_address', $task->dropoff_address) }}" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('dropoff_address')" />
                </div>
            </div>


            {{-- Photos Upload --}}
            <div>
                <x-input-label value="Task Photos (up to 10)" />
                <input id="photosInput" type="file" name="photos[]" multiple accept="image/*" class="mt-1 block w-full border rounded-md" />
                <p class="text-xs text-gray-500 mt-1">JPG / PNG / WEBP, max 5MB each.</p>
                <x-input-error :messages="$errors->get('photos')" />
                <x-input-error :messages="$errors->get('photos.*')" />
            </div>

            {{-- Photo Grid --}}
            <div id="photo-grid" class="grid grid-cols-3 gap-3 mt-4">
                @foreach($photos as $photo)
                    <div class="relative group cursor-move" data-id="{{ $photo->id }}">
                        <img src="{{ asset('storage/'.$photo->path) }}" class="w-full h-32 object-cover rounded" onclick="openPreview(this.src)">
                        <button type="button" class="absolute top-1 right-1 bg-black/60 text-white text-xs px-2 py-1 rounded hidden group-hover:block" onclick="deletePhoto({{ $photo->id }})">✕</button>
                    </div>
                @endforeach
            </div>

            {{-- Budget --}}
            <div>
                <x-input-label for="budget" value="Budget ($)" />
                <x-text-input id="budget" name="budget" type="number" step="0.01" value="{{ old('budget', $task->budget) }}" required class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('budget')" />
            </div>

            <x-primary-button>
                Save Changes
            </x-primary-button>
        </form>
    </div>

    {{-- Preview Modal --}}
    <div id="photo-preview" class="fixed inset-0 bg-black/80 hidden items-center justify-center z-50" onclick="this.classList.add('hidden')">
        <img id="preview-img" class="max-w-full max-h-full rounded">
    </div>



    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
    const grid = document.getElementById('photo-grid');
    const photosInput = document.getElementById('photosInput');

    // 打开预览
    function openPreview(src) {
        document.getElementById('preview-img').src = src;
        document.getElementById('photo-preview').classList.remove('hidden');
    }

    // 删除已有照片
    function deletePhoto(photoId) {
        if(!confirm('Are you sure you want to delete this photo?')) return;

        fetch(`{{ url('/tasks/photos') }}/${photoId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'  // 必须带上
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                const el = document.querySelector(`[data-id='${photoId}']`);
                if(el) el.remove();
            } else {
                alert(data.message || 'Delete failed.');
            }
        })
        .catch(err => alert('Delete failed.'));
    }

    // 拖拽排序
    new Sortable(grid, {
        animation: 150,
        onEnd() {
            const order = [...grid.querySelectorAll('[data-id]')].map((el, index) => ({
                id: el.dataset.id,
                sort: index
            }));

            fetch('{{ route("tasks.photos.reorder") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ order })
            });
        }
    });
    </script>
     <script>
            document.addEventListener('DOMContentLoaded', () => {
            const categorySelect = document.getElementById('category_id');
            const errandFields = document.getElementById('errand-fields');

            function toggleErrandFields() {
                const selectedOption = categorySelect.options[categorySelect.selectedIndex];
                const slug = selectedOption.dataset.slug; // 读取 data-slug
                errandFields.classList.toggle('hidden', slug !== 'errand');
            }

            // 初始化显示状态
            toggleErrandFields();

            // 监听 change
            categorySelect.addEventListener('change', toggleErrandFields);
        });
        </script>
</x-app-layout>