 @php use Illuminate\Support\Str; @endphp
<x-app-layout>
<div class="max-w-3xl mx-auto py-8"> 
    <h1 class="text-2xl font-bold mb-6">Edit Worker Profile</h1>
    <a href="{{ route('dashboard') }}" class="text-indigo-600 hover:underline mb-4 inline-block">
        ← Back to Dashboard
    </a>
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-2 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('workers.update') }}" enctype="multipart/form-data" id="profileForm">
        @csrf

        {{-- Avatar --}}
        <div class="mb-4">
            <label class="block mb-1 font-medium">Avatar</label>
            <img src="{{ $profile->avatar ? asset('storage/' . $profile->avatar) : asset('images/default-avatar.png') }}"
                 class="w-24 h-24 rounded-full mb-2 object-cover">
            <input type="file" name="avatar" accept="image/*" class="border p-1 rounded">
            @php
                $avatarErrors = collect($errors->get('avatar'))->flatten()->all();
            @endphp
            @if($avatarErrors)
                <x-input-error :messages="$avatarErrors" />
            @endif
        </div>

        {{-- City --}}
        <div class="mb-4">
            <x-input-label for="city" value="City" />
            <x-text-input id="city" name="city" type="text"
                class="mt-1 block w-full"
                value="{{ old('city', $profile->city ?? '') }}" />
            @php
                $cityErrors = collect($errors->get('city'))->flatten()->all();
            @endphp
            @if($cityErrors)
                <x-input-error :messages="$cityErrors" />
            @endif
        </div>

        {{-- Phone --}}
        <div class="mb-4">
            <x-input-label for="phone" value="Phone" />
            <x-text-input id="phone" name="phone" type="text"
                class="mt-1 block w-full"
                value="{{ old('phone', $profile->phone ?? '') }}" />
            @php
                $phoneErrors = collect($errors->get('phone'))->flatten()->all();
            @endphp
            @if($phoneErrors)
                <x-input-error :messages="$phoneErrors" />
            @endif
        </div>

        {{-- Categories --}}
        <div class="mb-4">
            <label class="block mb-1 font-medium">Categories</label>
            <div class="grid grid-cols-2 gap-2">
                @foreach($categories as $cat)
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox"
                               name="category_ids[]"
                               value="{{ $cat->id }}"
                               class="category-checkbox"
                               @checked(in_array($cat->id, old('category_ids', $profile->categories->pluck('id')->all())))>
                        <span>{{ $cat->name }}</span>
                    </label>
                @endforeach
            </div>
            @php
                $categoryErrors = collect($errors->get('category_ids'))->flatten()->all();
            @endphp
            @if($categoryErrors)
                <x-input-error :messages="$categoryErrors" />
            @endif
        </div>

        {{-- Skills (auto generated from categories) --}}
        <div class="mb-4">
            <x-input-label for="skills" value="Skills (auto from Categories)" />
            <x-text-input id="skills" name="skills" type="text"
                class="mt-1 block w-full"
                value="{{ old('skills', $profile->skills ?? '') }}" readonly />
        </div>

        {{-- Bio --}}
        <div class="mb-4">
            <label class="block mb-1 font-medium">Bio</label>
            <textarea id="bio" name="bio"
                class="w-full border p-2 rounded"
                rows="4">{{ old('bio', $profile->bio ?? '') }}</textarea>
            @php
                $bioErrors = collect($errors->get('bio'))->flatten()->all();
            @endphp
            @if($bioErrors)
                <x-input-error :messages="$bioErrors" />
            @endif
        </div>

        {{-- Photos --}}
        <div class="mb-6">
            <label class="block mb-1 font-medium">Profile Photos (max 10)</label>
            <input type="file" name="photos[]" id="photosInput" class="border p-2 rounded w-full mb-2" accept="image/*,video/mp4" multiple>
            <p id="photos-error" class="text-xs text-gray-500 mb-2">
                Upload up to 10 files: JPG/PNG/WEBP images (max 5MB) or MP4 videos (max 50MB).
            </p>

            {{-- 错误显示 --}}
            @php
                $photoErrors = collect($errors->get('photos'))->flatten()->all();
                $photoSubErrors = collect($errors->get('photos.*'))->flatten()->all();
            @endphp
            @if(!empty($photoErrors))
                <x-input-error :messages="$photoErrors" />
            @endif
            @if(!empty($photoSubErrors))
                <x-input-error :messages="$photoSubErrors" />
            @endif
        </div>
       {{-- Photo Grid --}}
        <div id="photo-grid" class="grid grid-cols-3 gap-3 mb-6">
            @foreach($photos as $photo)
            <div class="relative group cursor-move" data-id="{{ $photo->id }}">
                @if($photo->isVideo())
                    <video controls class="w-full h-32 object-cover rounded">
                        <source src="{{ asset('storage/'.$photo->path) }}" type="video/mp4">
                    </video>
                @else
                    <img src="{{ asset('storage/'.$photo->path) }}"
                        class="w-full h-32 object-cover rounded"
                        onclick="openPreview(this.src)">
                @endif
                <button type="button"
                        class="absolute top-1 right-1 bg-black/60 text-white text-xs px-2 py-1 rounded hidden group-hover:block"
                        onclick="deletePhoto({{ $photo->id }})">✕
                </button>
            </div>
            @endforeach
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Save
        </button>
    </form>
</div>

{{-- JS --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    let timer;

    document.querySelector('#bio').addEventListener('input', function () {

        clearTimeout(timer);

        timer = setTimeout(() => {

            fetch('/worker/bio', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    bio: this.value
                })
            });

        }, 800); // debounce
    });    

    function saveSkillsToServer() {

        const selectedIds = Array.from(
            document.querySelectorAll('.category-checkbox:checked')
        ).map(el => el.value);

        fetch('/worker/skills', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                category_ids: selectedIds
            })
        });
    }
    function getSelectedSkills() {
        return Array.from(document.querySelectorAll('.category-checkbox:checked'))
            .map(el => el.value);
    }

    function uploadAvatar(file) {

        let formData = new FormData();
        formData.append('avatar', file);

        fetch('/worker/media', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        });
    }

    function uploadPhotos(files) {

        let formData = new FormData();

        for (let file of files) {
            formData.append('photos[]', file);
        }

        fetch('/worker/media', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        });
    }

    document.querySelector('input[name="avatar"]').addEventListener('change', function () {
        uploadAvatar(this.files[0]);
    });

    document.querySelector('#photosInput').addEventListener('change', function () {
        uploadPhotos(this.files);
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // // 动态生成 skills
    const skillInput = document.getElementById('skills');
    const checkboxes = document.querySelectorAll('.category-checkbox');
    function updateSkillsUI() {
        let selected = [];
        checkboxes.forEach(cb => { if(cb.checked) selected.push(cb.nextElementSibling.textContent); });
        skillInput.value = selected.join(', ');
    }
    // checkboxes.forEach(cb => cb.addEventListener('change', updateSkillsUI));
    checkboxes.forEach(cb => cb.addEventListener('change', () => {
        updateSkillsUI();
        saveSkillsToServer();
    }));
    updateSkillsUI();

    // 限制上传 10 张 + 文件类型/大小
    const photosInput = document.getElementById('photosInput');
    photosInput.addEventListener('change', function() {
        const files = this.files;
        const existingCount = document.querySelectorAll('#photo-grid [data-id]').length;
        if (files.length + existingCount > 10) {
            alert('You can upload up to 10 photos only.');
            this.value = '';
            return;
        }
        const allowedTypes = [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/webp',
            'video/mp4'
        ];

        for (let f of files) {
            if (!allowedTypes.includes(f.type)) {
                alert('Only JPG, PNG, WEBP images or MP4 videos allowed.');
                this.value = '';
                return;
            }
            if (f.type === 'video/mp4' && f.size > 50 * 1024 * 1024) {
                alert('Each video must be under 50MB.');
                this.value = '';
                return;
            }

            if (f.type.startsWith('image/') && f.size > 5 * 1024 * 1024) {
                alert('Each image must be under 5MB.');
                this.value = '';
                return;
            }
        }
    });

    // 预览
    window.openPreview = function(src) {
        const preview = document.getElementById('photo-preview');
        const img = document.getElementById('preview-img');
        img.src = src;
        preview.classList.remove('hidden');
    };
    
    // 拖拽排序
    new Sortable(document.getElementById('photo-grid'), {
        animation: 150,
        onEnd() {
            const order = [...document.querySelectorAll('#photo-grid [data-id]')].map((el, index) => ({
                id: el.dataset.id,
                sort: index
            }));
            fetch('{{ route("workers.photos.reorder") }}', {
                method: 'POST',
                headers: {'Content-Type': 'application/json','X-CSRF-TOKEN': '{{ csrf_token() }}'},
                body: JSON.stringify({ order })
            });
        }
    });

    // 删除图片函数
    window.deletePhoto = function(id) {
        if (!confirm('Are you sure to delete this photo?')) return;

        // 动态生成 URL
        const url = `{{ route('profile.photos.destroy', ['photo' => 'PHOTO_ID']) }}`.replace('PHOTO_ID', id);

        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({}) // DELETE 方法有时需要空 body
        })
        .then(async res => {
            let data;
            try {
                data = await res.json();
            } catch(e) {
                throw new Error(`Invalid JSON response, status: ${res.status}`);
            }

            if (res.ok && data.success) {
                const el = document.querySelector(`[data-id='${id}']`);
                if (el) el.remove();
            } else {
                alert(data.message || `Delete failed, status: ${res.status}`);
            }
        })
        .catch(err => {
            console.error(err);
            alert(err.message || 'Delete failed.');
        });
    };
});
</script>

{{-- 预览弹窗 --}}
<div id="photo-preview" class="fixed inset-0 bg-black/80 hidden items-center justify-center z-50" onclick="this.classList.add('hidden')">
    <img id="preview-img" class="max-w-full max-h-full rounded">
</div>
</x-app-layout>
