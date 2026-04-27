<x-app-layout>
<div class="max-w-xl mx-auto py-10">

    <h2 class="text-2xl font-bold mb-4">Complete Payment</h2>

    <div class="bg-white p-6 rounded shadow space-y-3">

        <p><b>Task:</b> {{ $order->task->title ?? 'N/A' }}</p>

        <p><b>Worker:</b> {{ $order->worker->name ?? 'Not assigned' }}</p>

        <p class="text-2xl font-bold text-indigo-600">
            ${{ number_format($order->amount, 2) }}
        </p>

    </div>

    {{-- payment button --}}
    <button id="payBtn"
        class="mt-6 w-full bg-indigo-600 text-white py-3 rounded hover:bg-indigo-700 disabled:opacity-50">

        Pay Now
    </button>

</div>

{{-- Stripe --}}
<script src="https://js.stripe.com/v3/"></script>

<script>
const stripe = Stripe("{{ env('STRIPE_KEY') }}");

const btn = document.getElementById('payBtn');

btn.addEventListener('click', async () => {

    try {
        btn.disabled = true;
        btn.innerText = "Processing...";

        const res = await fetch("{{ route('orders.intent', $order) }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            }
        });

        const data = await res.json();

        const result = await stripe.confirmCardPayment(data.client_secret);

        if (result.error) {
            alert(result.error.message);
            btn.disabled = false;
            btn.innerText = "Pay Now";
            return;
        }

        if (result.paymentIntent.status === 'succeeded') {
            window.location.href = "{{ route('orders.success', $order) }}";
        }

    } catch (err) {
        console.error(err);
        alert('Payment failed. Try again.');
        btn.disabled = false;
        btn.innerText = "Pay Now";
    }
});
</script>

</x-app-layout>