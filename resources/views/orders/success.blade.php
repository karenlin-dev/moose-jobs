<x-app-layout>
<div class="max-w-xl mx-auto py-8 text-center">

    <h1 class="text-2xl font-bold text-green-600">
        Payment Successful 🎉
    </h1>

    <a href="{{ route('orders.show', $order) }}"
       class="mt-4 inline-block text-blue-600">
        View Order
    </a>
    <p>Order #{{ $order->id }}</p>

    @if($order->status === 'paid')
        <p class="text-green-600">Payment confirmed</p>
    @else
        <p class="text-yellow-600">Waiting for confirmation...</p>
    @endif
</div>
</x-app-layout>