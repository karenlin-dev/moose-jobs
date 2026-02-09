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

                <div class="mt-6 space-y-2">
                    <h3 class="font-semibold">Delivery Route Preview</h3>
                    <div
                        id="map"
                        class="w-full rounded border"
                        style="height: 320px;"
                    ></div>
                </div>
                <input id="distance_km" name="distance_km">
                <div class="mt-4">
                    <label class="block text-sm font-medium">Package Weight (kg)</label>
                    <input
                        id="weight_kg"
                        name="weight_kg"
                        type="number"
                        step="0.1"
                        min="0"
                        class="w-full border rounded px-3 py-2"
                        value="{{ old('weight_kg', $task->weight_kg ?? '') }}"
                        placeholder="e.g. 5"
                    >
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium">Package Size</label>
                    <select
                        id="size_level"
                        name="size_level"
                        class="w-full border rounded px-3 py-2"
                    >
                        <option value="">Select size</option>
                        <option value="small" @selected(old('size_level', $task->size_level ?? '') === 'small')>
                            Small (fits in a backpack)
                        </option>
                        <option value="medium" @selected(old('size_level', $task->size_level ?? '') === 'medium')>
                            Medium (fits in car trunk)
                        </option>
                        <option value="large" @selected(old('size_level', $task->size_level ?? '') === 'large')>
                            Large (needs SUV / van)
                        </option>
                    </select>
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
        let map, directionsService, directionsRenderer;
        let pickupAutocomplete, dropoffAutocomplete;

        const MOOSE_JAW = { lat: 50.3933, lng: -105.5516 };

        // 定价参数（统一在这里）
        const BASE_PRICE = 8;
        const PRICE_PER_KM = 1.5;

        function initMap() {
            console.log('✅ initMap fired');

            const mapEl = document.getElementById('map');
            if (!mapEl) return;

            map = new google.maps.Map(mapEl, {
                zoom: 12,
                center: MOOSE_JAW
            });

            directionsService = new google.maps.DirectionsService();
            directionsRenderer = new google.maps.DirectionsRenderer({ map });

            initAutocomplete();
            drawRoute();
        }

        function initAutocomplete() {
            const options = {
                bounds: new google.maps.LatLngBounds(
                    { lat: 50.33, lng: -105.65 },
                    { lat: 50.45, lng: -105.45 }
                ),
                strictBounds: true,
                componentRestrictions: { country: 'ca' }
            };

            const pickupInput = document.getElementById('pickup_address');
            const dropoffInput = document.getElementById('dropoff_address');

            if (pickupInput) {
                pickupAutocomplete = new google.maps.places.Autocomplete(pickupInput, options);
                pickupAutocomplete.addListener('place_changed', drawRoute);
            }

            if (dropoffInput) {
                dropoffAutocomplete = new google.maps.places.Autocomplete(dropoffInput, options);
                dropoffAutocomplete.addListener('place_changed', drawRoute);
            }

            // 重量 / 体积变化时重新算价格
            document.getElementById('weight_kg')?.addEventListener('change', recalcBudget);
            document.getElementById('size_level')?.addEventListener('change', recalcBudget);
        }

        function drawRoute() {
            const pickup = document.getElementById('pickup_address')?.value;
            const dropoff = document.getElementById('dropoff_address')?.value;

            if (!pickup || !dropoff) return;

            document.getElementById('map')?.classList.remove('hidden');

            directionsService.route({
                origin: pickup + ', Moose Jaw, SK',
                destination: dropoff + ', Moose Jaw, SK',
                travelMode: google.maps.TravelMode.DRIVING
            }, (result, status) => {
                if (status !== 'OK') return;

                directionsRenderer.setDirections(result);

                const meters = result.routes[0].legs[0].distance.value;
                const km = meters / 1000;

                document.getElementById('distance_km').value = km.toFixed(2);

                updateBudget(km);
            });
        }

        function recalcBudget() {
            const km = parseFloat(document.getElementById('distance_km').value);
            if (!km) return;
            updateBudget(km);
        }

        function updateBudget(distanceKm) {
            const weight = parseFloat(document.getElementById('weight_kg')?.value || 0);
            const size = document.getElementById('size_level')?.value;

            let price = BASE_PRICE + distanceKm * PRICE_PER_KM;

            // 重量加价
            if (weight > 5 && weight <= 10) price += 5;
            else if (weight > 10) price += 10;

            // 体积加价
            if (size === 'medium') price += 5;
            if (size === 'large') price += 12;

            document.getElementById('budget').value = price.toFixed(2);
        }
</script>


<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDGATCJGWfLxnjcMnvNz_TWigdxfX4x0Xg&libraries=places&callback=initMap"
    async
    defer>
</script>

</x-app-layout>