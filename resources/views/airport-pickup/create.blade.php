<x-app-layout>
<div class="max-w-2xl mx-auto py-8">

    <h1 class="text-2xl font-bold mb-6">Airport Pickup Service</h1>

    <div class="bg-white p-6 rounded-xl shadow">

        <form method="POST" action="{{ route('airport-pickup.store') }}">
            @csrf

            {{-- Pickup Location --}}
            <div class="mb-4">
                <x-input-label for="pickup_address" value="Pickup Location (Airport)" />
                <x-text-input id="pickup_address"
                              name="pickup_address"
                              type="text"
                              class="mt-1 block w-full"
                              placeholder="e.g. Regina Airport (YQR)"
                              value="{{ old('pickup_address') }}" />
                <x-input-error :messages="$errors->get('pickup_address')" />
            </div>

            {{-- Dropoff --}}
            <div class="mb-4">
                <x-input-label for="dropoff_address" value="Drop-off Location" />
                <x-text-input id="dropoff_address"
                              name="dropoff_address"
                              type="text"
                              class="mt-1 block w-full"
                              placeholder="Hotel / Home address"
                              value="{{ old('dropoff_address') }}" />
                <x-input-error :messages="$errors->get('dropoff_address')" />
            </div>

            {{-- Date & Time --}}
            <div class="mb-4">
                <x-input-label for="scheduled_at" value="Pickup Date & Time" />
                <input type="datetime-local"
                       name="scheduled_at"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                       value="{{ old('scheduled_at') }}">
                <x-input-error :messages="$errors->get('scheduled_at')" />
            </div>

            <div class="grid grid-cols-2 gap-4">

                {{-- Passengers --}}
                <div class="mb-4">
                    <x-input-label for="passengers" value="Passengers" />
                    <input type="number"
                           name="passengers"
                           min="1"
                           class="mt-1 block w-full border-gray-300 rounded-md"
                           value="{{ old('passengers', 1) }}">
                    <x-input-error :messages="$errors->get('passengers')" />
                </div>

                {{-- Luggage --}}
                <div class="mb-4">
                    <x-input-label for="luggage" value="Luggage" />
                    <input type="number"
                           name="luggage"
                           min="0"
                           class="mt-1 block w-full border-gray-300 rounded-md"
                           value="{{ old('luggage', 0) }}">
                    <x-input-error :messages="$errors->get('luggage')" />
                </div>

            </div>

            {{-- Price Preview --}}
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600">Fixed Price (MVP)</p>
                <p class="text-xl font-bold text-gray-900">$50 CAD</p>
            </div>

            {{-- Submit --}}
            <button type="submit"
                    class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">
                Book Airport Pickup
            </button>

        </form>

    </div>
</div>
</x-app-layout>