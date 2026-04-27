<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">
                {{ $task->title }}
            </h2>

            <a href="{{ route('tasks.index') }}" class="text-sm text-gray-600 hover:underline">
                ← Back to Tasks
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto py-8 space-y-4">

        <div class="bg-white p-6 rounded shadow space-y-3">
            <div class="text-sm text-gray-500">
                City: {{ $task->city ?? 'Moose Jaw' }}
                · Budget: ${{ $task->budget ?? 'Negotiable' }}
                · Status: {{ strtoupper($task->status ?? 'OPEN') }}
            </div>

            @if($task->category)
                <span class="inline-block text-xs bg-gray-100 px-2 py-1 rounded">
                    {{ $task->category->name }}
                </span>
            @endif
            @if($task->service_type === 'airport')
            <div class="mt-3 bg-gray-50 p-4 rounded space-y-2 text-sm">

                <div class="font-semibold text-gray-700">
                    ✈ Airport Pickup Details
                </div>

                <div>
                    <strong>Pickup Location:</strong>
                    {{ $task->pickup_address ?? 'N/A' }}
                </div>

                <div>
                    <strong>Dropoff Location:</strong>
                    {{ $task->dropoff_address ?? 'N/A' }}
                </div>

                <div>
                    <strong>Scheduled Time:</strong>
                    {{ $task->scheduled_at ? \Carbon\Carbon::parse($task->scheduled_at)->format('Y-m-d H:i') : 'N/A' }}
                </div>

                <div>
                    <strong>Passengers:</strong>
                    {{ $task->passengers ?? 0 }}
                </div>

                <div>
                    <strong>Luggage:</strong>
                    {{ $task->luggage ?? 0 }}
                </div>

            </div>
        @endif
            @if($task->worker_id)
                <div class="bg-blue-50 p-4 rounded space-y-2">

                    <div class="font-semibold text-blue-700">
                        🚗 Driver Assigned
                    </div>

                    <div>
                        Name: {{ $task->worker?->name }}
                    </div>

                    <div>
                        Phone: {{ $task->worker?->phone ?? 'N/A' }}
                    </div>

                    {{-- 💳 Payment --}}
                    @if($task->order)

                        @if($task->order->status !== 'paid')

                            @if(auth()->id() === $task->user_id)

                                <a href="{{ route('orders.pay', $task->order->id) }}"
                                class="inline-block mt-2 px-4 py-2 bg-green-600 text-white rounded">
                                    Pay Now
                                </a>

                            @endif

                        @else

                            <span class="text-green-600 font-semibold">
                                Paid ✅
                            </span>

                        @endif

                    @endif

                </div>

            @else

                <span class="text-gray-500">
                    Waiting for driver to accept...
                </span>
            @endif
             {{-- 跑腿任务地址 --}}
            @if($task->category?->slug === 'errand')
                <div class="mt-2 text-sm text-gray-600">
                    <p><strong>Pickup Address:</strong> {{ $task->pickup_address ?? 'N/A' }}</p>
                    <p><strong>Drop-off Address:</strong> {{ $task->dropoff_address ?? 'N/A' }}</p>
                </div>
            @endif
            <hr>

            <h3 class="font-semibold">Description</h3>
            <p class="text-gray-700 whitespace-pre-line">
                {{ $task->description }}
            </p>
            @if($task->photos->count())
                <div class="mt-6">
                    <h3 class="font-semibold mb-2">Photos</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach($task->photos as $photo)
                            <img src="{{ asset('storage/'.$photo->path) }}"
                                class="w-full h-40 object-cover rounded-lg border"
                                alt="task photo">
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

       {{-- Delivery Tracking (only for delivery tasks) --}}
        @php
            $isDelivery = $task->type === 'delivery'
                || $task->category === 'delivery'
                || $task->pickup_address
                || $task->dropoff_address;
        @endphp

        @if($isDelivery)

            @php
                $statusOrder = ['pending', 'in_transit', 'delivered'];

                $labels = [
                    'pending' => 'Waiting Pickup',
                    'in_transit' => 'On the Way',
                    'delivered' => 'Delivered',
                ];

                $currentIndex = array_search($task->delivery_status ?? 'pending', $statusOrder);
            @endphp

            <!-- 📦 Status Card -->
            <div class="mt-6 bg-white border rounded-2xl p-5 shadow-sm">

                <h3 class="font-semibold text-gray-800 mb-4">
                    🚚 Delivery Status
                </h3>

                <!-- Progress Bar -->
                <div class="flex items-center justify-between relative">

                    @foreach($statusOrder as $index => $key)

                        <div class="flex flex-col items-center z-10">

                            <!-- Circle -->
                            <div
                                class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold
                                transition-all
                                {{ $index < $currentIndex ? 'bg-green-500 text-white' : '' }}
                                {{ $index === $currentIndex ? 'bg-blue-500 text-white scale-110 shadow-md' : '' }}
                                {{ $index > $currentIndex ? 'bg-gray-200 text-gray-500' : '' }}"
                            >
                                @if($index < $currentIndex)
                                    ✓
                                @else
                                    {{ $index + 1 }}
                                @endif
                            </div>

                            <!-- Label -->
                            <div class="text-xs mt-2 text-center
                                {{ $index <= $currentIndex ? 'text-gray-800 font-medium' : 'text-gray-400' }}">
                                {{ $labels[$key] }}
                            </div>

                        </div>

                    @endforeach

                    <!-- Line Background -->
                    <div class="absolute top-5 left-0 right-0 h-1 bg-gray-200 -z-0"></div>

                </div>
            </div>

        @endif
        @if($task->pickup_address && $task->dropoff_address)
            <div class="mt-6 space-y-4">
                <h3 class="font-semibold">Delivery Route</h3>

                <div class="w-full h-80 rounded overflow-hidden border">
                    <iframe
                        width="100%"
                        height="100%"
                        style="border:0"
                        loading="lazy"
                        allowfullscreen
                        src="https://www.google.com/maps?saddr={{ urlencode($task->pickup_address) }}&daddr={{ urlencode($task->dropoff_address) }}&output=embed">
                    </iframe>
                </div>
            </div>
        @endif
        @if($task->distance_km)
            <p class="text-xs text-gray-500">
                Distance: {{ $task->distance_km }} km
            </p>
        @endif
    
        @if($task->weight_kg || $task->size_level)
            <div class="text-sm text-gray-600 mt-2">
                📦
                {{ $task->weight_kg ? $task->weight_kg.' kg' : '' }}
                {{ $task->size_level ? ucfirst($task->size_level).' size' : '' }}
            </div>
        @endif

        {{-- ===================== --}}
        {{-- 投标入口（Worker） --}}
        {{-- ===================== --}}
       {{-- ===================== --}}
        {{-- Worker Action --}}
        {{-- ===================== --}}
        <div class="bg-white p-6 rounded shadow">

            @auth

                @if(auth()->user()->role === 'worker')

                    {{-- 🔥 Instant Task --}}
                    @if($task->is_instant)

                        <div class="flex items-center justify-between">

                            <div>
                                <div class="font-semibold text-blue-600">
                                    Instant Task
                                </div>
                                <div class="text-sm text-gray-600">
                                    You can accept this task immediately.
                                </div>
                            </div>

                            {{-- 已接单 --}}
                            @if($task->worker_id)
                                <button disabled
                                    class="px-4 py-2 rounded bg-gray-300 text-gray-600 text-sm">
                                    Already Accepted
                                </button>
                            @else
                                <form method="POST" action="{{ route('tasks.acceptAirport', $task) }}">
                                    @csrf
                                    <button class="px-4 py-2 rounded bg-blue-600 text-white text-sm">
                                        Accept Task
                                    </button>
                                </form>
                            @endif

                        </div>

                    {{-- 🔥 Bidding Task --}}
                    @else

                        @if($task->status === 'open')

                            <div class="flex items-center justify-between">

                                <div>
                                    <div class="font-semibold">
                                        Bidding Task
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        Submit your price and message.
                                    </div>
                                </div>

                                <a href="{{ route('bids.create', $task) }}"
                                class="px-4 py-2 rounded bg-indigo-600 text-white text-sm">
                                    Place a Bid
                                </a>

                            </div>

                        @else

                            <div class="text-sm text-gray-600">
                                This task is not open.
                            </div>

                        @endif

                    @endif

                @else

                    <div class="text-sm text-gray-600">
                        Only workers can perform this action.
                    </div>

                @endif

            @else

                <div class="flex items-center justify-between">

                    <div class="text-sm text-gray-600">
                        Login to participate as a worker.
                    </div>

                    <a href="{{ route('login') }}"
                    class="px-4 py-2 rounded bg-black text-white text-sm">
                        Login
                    </a>

                </div>

            @endauth

        </div>

</x-app-layout>
