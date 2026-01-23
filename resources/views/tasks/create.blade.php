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
                    <x-input-label for="photos" value="Task Photos (up to 10)" />
                    <input id="photos"
                        type="file"
                        name="photos[]"
                        multiple
                        accept="image/*"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                    <p class="text-xs text-gray-500 mt-1">JPG/PNG/WEBP, max 5MB each.</p>

                    <x-input-error :messages="$errors->get('photos')" />
                    <x-input-error :messages="$errors->get('photos.*')" />
                </div>

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
</x-app-layout>
