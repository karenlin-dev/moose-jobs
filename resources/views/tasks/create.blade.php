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
                            <option value="{{ $category->id }}" data-slug="{{ $category->slug }}">
                                {{ $category->name }}
                            </option>

                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('category_id')" />
                </div>
                {{-- 跑腿专属字段 --}}
                <div id="errand-fields" class="hidden">
                    <input id="pickup_address" name="pickup_address" placeholder="Pickup address (Moose Jaw only)">
                    <input id="dropoff_address" name="dropoff_address" placeholder="Dropoff address (Moose Jaw only)">
                    {{-- 编辑页增加状态 --}}
                    @if(isset($task))
                        <x-input-label for="delivery_status" value="Delivery Status" class="mt-2"/>
                        <select name="delivery_status" id="delivery_status" class="mt-1 block w-full rounded-md border">
                            @foreach(['pending','in_transit','delivered'] as $status)
                                <option value="{{ $status }}" @selected(($task->delivery_status ?? 'pending') === $status)>{{ ucfirst(str_replace('_',' ',$status)) }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
              
                <div class="mt-6 space-y-2">
                    <h3 class="font-semibold">Delivery Route Preview</h3>

                    <div
                        id="map"
                        class="w-full rounded border"
                        style="height: 320px;"
                    ></div>
                </div>

                

                {{-- Task Photos --}}
                <div>
                    <x-input-label for="photos" value="Task Photos (up to 10)" />

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

        const input = document.getElementById('photos');
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
        
<script>
    let map, directionsService, directionsRenderer;
    let pickupAutocomplete, dropoffAutocomplete;

    const MOOSE_JAW = { lat: 50.3933, lng: -105.5516 };

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
                { lat: 50.33, lng: -105.65 },   // Moose Jaw SW
                { lat: 50.45, lng: -105.45 }    // Moose Jaw NE
            ),
            strictBounds: true,
            componentRestrictions: { country: 'ca' },
            fields: ['formatted_address']
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
    }

    function drawRoute() {
        const pickup = document.getElementById('pickup_address')?.value;
        const dropoff = document.getElementById('dropoff_address')?.value;

        if (!pickup || !dropoff) return;

        document.getElementById('map').classList.remove('hidden');

        directionsService.route({
            origin: pickup,
            destination: dropoff,
            travelMode: google.maps.TravelMode.DRIVING
        }, (result, status) => {
            if (status === 'OK') {
                directionsRenderer.setDirections(result);
            }
        });
    }
</script>

<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDGATCJGWfLxnjcMnvNz_TWigdxfX4x0Xg&libraries=places&callback=initMap"
    async
    defer>
</script>
    </x-app-layout>
