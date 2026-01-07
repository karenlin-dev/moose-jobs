<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Bid on Task</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-8 space-y-6">
        {{-- Flash messages --}}
        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-2 rounded">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 text-red-800 p-2 rounded">{{ session('error') }}</div>
        @endif

        {{-- Task info --}}
        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-lg font-semibold">{{ $task->title }}</h3>
            <p class="text-gray-600 mt-2">{{ $task->description }}</p>
            <p class="mt-2 text-sm text-gray-500">
                Budget: ${{ $task->budget }}
            </p>
        </div>

        {{-- Bid form --}}
        <form method="POST" action="{{ route('bids.store', $task) }}" class="space-y-6">
            @csrf
            <div>
                <x-input-label for="price" value="Your Price ($)" />
                <x-text-input id="price" name="price" type="number" step="0.01"
                    class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('price')" />
            </div>

            <div>
                <x-input-label for="message" value="Message (optional)" />
                <textarea id="message" name="message"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                    rows="4">{{ old('message') }}</textarea>
            </div>

            <x-primary-button>Submit Bid</x-primary-button>
        </form>
    </div>
</x-app-layout>
