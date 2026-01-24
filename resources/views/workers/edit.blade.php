<x-app-layout>
<div class="max-w-2xl mx-auto py-8">

    <h1 class="text-2xl font-bold mb-4">Edit Worker Profile</h1>

    <div class="flex justify-between items-center mb-4">
        <a href="{{ route('dashboard') }}" class="text-indigo-600 hover:underline">
           ← Back to Dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-2 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('workers.update') }}" enctype="multipart/form-data">
        @csrf

        {{-- Avatar --}}
        <div class="mb-4">
            <label class="block mb-1 font-medium">Avatar</label>
            <img src="{{ $profile->avatar
                ? asset('storage/' . $profile->avatar)
                : asset('images/default-avatar.png') }}"
                class="w-24 h-24 rounded-full mb-2">
            <input type="file" name="avatar" class="border p-1 rounded" accept="image/*">
            <x-input-error :messages="$errors->get('avatar')" />
        </div>

        {{-- City --}}
        <div class="mb-4">
            <x-input-label for="city" value="City" />
            <x-text-input id="city" name="city" type="text"
                class="mt-1 block w-full"
                value="{{ old('city', $profile->city ?? 'Moose Jaw') }}" />
            <x-input-error :messages="$errors->get('city')" />
        </div>

        {{-- Phone --}}
        <div class="mb-4">
            <x-input-label for="phone" value="Phone" />
            <x-text-input id="phone" name="phone" type="text"
                class="mt-1 block w-full"
                value="{{ old('phone', $profile->phone ?? '') }}" />
            <x-input-error :messages="$errors->get('phone')" />
        </div>

        {{-- Skills --}}
        <div class="mb-4">
            <x-input-label for="skills" value="Skills (comma separated)" />
            <x-text-input id="skills" name="skills" type="text"
                class="mt-1 block w-full"
                value="{{ old('skills', $profile->skills ?? '') }}" />
            <x-input-error :messages="$errors->get('skills')" />
        </div>

        {{-- Categories (multi-select) --}}
        <div class="mb-4">
            <label class="block mb-1 font-medium">Categories</label>

            <div class="grid grid-cols-2 gap-2">
                @foreach($categories as $cat)
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox"
                            name="category_ids[]"
                            value="{{ $cat->id }}"
                            @checked(in_array($cat->id, old('category_ids', $profile->categories->pluck('id')->all())))
                        >
                        <span>{{ $cat->name }}</span>
                    </label>
                @endforeach
            </div>

            <x-input-error :messages="$errors->get('category_ids')" />
        </div>


        {{-- Bio --}}
        <div class="mb-4">
            <label class="block mb-1 font-medium">Bio</label>
            <textarea name="bio"
                      class="w-full border p-2 rounded"
                      rows="4">{{ old('bio', $profile->bio ?? '') }}</textarea>
            <x-input-error :messages="$errors->get('bio')" />
        </div>
        {{-- Gallery Photos --}}
        <div class="mb-6">
            <label class="block mb-1 font-medium">Photos (up to 10)</label>
            <input type="file" name="photos[]"
                class="border p-2 rounded w-full"
                accept="image/*"
                multiple>
            <p class="text-xs text-gray-500 mt-1">JPG/PNG/WEBP, max 5MB each.</p>
            <x-input-error :messages="$errors->get('photos')" />
            <x-input-error :messages="$errors->get('photos.*')" />
        </div>


        {{-- Rating & Total Reviews (只读) --}}
        <div class="mb-4 text-sm text-gray-600">
            ⭐ Rating: {{ $profile->rating ?? 0 }}
            ({{ $profile->total_reviews ?? 0 }} reviews)
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Save
        </button>
    </form>
</div>
</x-app-layout>
