<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Post a New Task
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-8">
        <form method="POST" action="{{ route('tasks.store') }}" class="space-y-6" enctype="multipart/form-data">

            @csrf

            <div>
                <x-input-label for="title" value="Task Title" />
                <x-text-input id="title" name="title" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('title')" />
            </div>

            <div>
                <x-input-label for="description" value="Description" />
                <textarea id="description" name="description"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                    rows="5" required></textarea>
                <x-input-error :messages="$errors->get('description')" />
            </div>
            <div>
                <x-input-label for="city" value="City" />
                <x-text-input 
                    id="city" 
                    name="city" 
                    class="mt-1 block w-full" 
                    value="{{ old('city', 'Moose Jaw') }}" 
                    required 
                />
                <x-input-error :messages="$errors->get('city')" />
            </div>

            <div>
                <x-input-label for="category_id" value="Category" />
                <select id="category_id" name="category_id" class="mt-1 block w-full rounded-md border-gray-300">
                    <option value="">-- Select Category --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('category_id')" />
            </div>
            {{-- Task Photos --}}
            <div>
                <x-input-label value="Task Photos (up to 10)" />

                <input
                    id="photos"
                    type="file"
                    name="photos[]"
                    multiple
                    accept="image/*"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                />

                <p class="text-xs text-gray-500 mt-1">
                    JPG / PNG / WEBP, max 5MB each.
                </p>

                <x-input-error :messages="$errors->get('photos')" />
                <x-input-error :messages="$errors->get('photos.*')" />
            </div>

            {{-- Photo Grid --}}
            <div id="photo-grid" class="grid grid-cols-3 gap-3 mt-4"></div>


            <div>
                <x-input-label for="budget" value="Budget ($)" />
                <x-text-input id="budget" name="budget" type="number" step="0.01"
                    class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('budget')" />
            </div>

            <x-primary-button>
                Publish Task
            </x-primary-button>
        </form>
    </div>
    {{-- Preview Modal --}}
    <div id="photo-preview"
        class="fixed inset-0 bg-black/80 hidden items-center justify-center z-50"
        onclick="this.classList.add('hidden')">
        <img id="preview-img" class="max-w-full max-h-full rounded">
    </div>

    <script>
    function openPreview(src) {
        document.getElementById('preview-img').src = src;
        document.getElementById('photo-preview').classList.remove('hidden');
    }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <script>
    let files = [];

    const input = document.getElementById('photosInput');
    const grid = document.getElementById('photo-grid');

    input.addEventListener('change', () => {
        for (let file of input.files) {
            if (files.length >= 10) {
                alert('You can upload up to 10 photos.');
                break;
            }

            if (!['image/jpeg','image/png','image/webp'].includes(file.type)) {
                alert('Only JPG / PNG / WEBP allowed.');
                continue;
            }

            if (file.size > 5 * 1024 * 1024) {
                alert('Each image must be under 5MB.');
                continue;
            }

            files.push(file);
        }

        input.value = '';
        renderGrid();
    });

    function renderGrid() {
        grid.innerHTML = '';

        files.forEach((file, index) => {
            const url = URL.createObjectURL(file);

            grid.innerHTML += `
                <div class="relative group cursor-move" data-index="${index}">
                    <img src="${url}"
                        class="w-full h-32 object-cover rounded"
                        onclick="openPreview('${url}')">

                    <button type="button"
                            class="absolute top-1 right-1 bg-black/60 text-white text-xs px-2 py-1 rounded hidden group-hover:block"
                            onclick="removePhoto(${index})">
                        ✕
                    </button>
                </div>
            `;
        });
    }

    // 删除
    function removePhoto(index) {
        files.splice(index, 1);
        renderGrid();
    }

    // 拖拽排序
    new Sortable(grid, {
        animation: 150,
        onEnd() {
            const reordered = [];
            grid.querySelectorAll('[data-index]').forEach(el => {
                reordered.push(files[el.dataset.index]);
            });
            files = reordered;
            renderGrid();
        }
    });
    </script>

</x-app-layout>
