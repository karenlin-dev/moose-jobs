<x-app-layout>
<div class="max-w-xl mx-auto py-8">

    <h1 class="text-xl font-bold mb-4">Order #{{ $order->id }}</h1>

    <div class="bg-white p-6 rounded shadow space-y-2">

        <p><strong>Pickup:</strong> {{ $order->pickup_address }}</p>
        <p><strong>Dropoff:</strong> {{ $order->dropoff_address }}</p>
        <p><strong>Time:</strong> {{ $order->scheduled_at }}</p>
        <p><strong>Passengers:</strong> {{ $order->passengers }}</p>
        <p><strong>Luggage:</strong> {{ $order->luggage }}</p>

        <hr>

        <p class="text-lg font-bold">Price: ${{ $order->amount }} CAD</p>

        <p>Status:
            <span class="px-2 py-1 text-sm rounded
                {{ $order->status == 'paid' ? 'bg-green-200' : 'bg-yellow-200' }}">
                {{ strtoupper($order->status) }}
            </span>
        </p>

        @if($order->status == 'pending')
            <a href="{{ route('orders.pay', $order) }}"
               class="block text-center bg-blue-600 text-white py-2 rounded">
                Pay Now
            </a>
        @endif

    </div>

</div>
</x-app-layout>