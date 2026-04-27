<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Services\OrderService;
use App\Services\PaymentService;

class OrderController extends Controller
{
    public function create()
    {
        return view('airport.create');
    }

    public function store(Request $request, OrderService $orderService, PaymentService $paymentService)
    {
        $validated = $request->validate([
            'service_type' => 'required|string|in:airport,errand,delivery',
            'pickup_address' => 'required_if:service_type,airport|string',
            'dropoff_address' => 'required_if:service_type,airport|string',
            'scheduled_at' => 'required_if:service_type,airport|date',

            'passengers' => 'nullable|integer|min:1',
            'luggage' => 'nullable|integer|min:0',
        ]);

        $order = $orderService->create(auth()->id(), $validated);

        $paymentUrl = $paymentService->createCheckoutSession($order);

        return redirect($paymentUrl);
    }

    public function show(Order $order)
    {
        if ($order->employer_id !== auth()->id()) {
            abort(403);
        }

        return view('orders.show', compact('order'));
    }

    public function pay(Order $order, PaymentService $paymentService)
    {
        if ($order->status === 'paid') {
            return redirect()->route('orders.show', $order);
        }

        return redirect($paymentService->createCheckoutSession($order));
    }

    public function createIntent(Order $order, PaymentService $paymentService)
    {
        $intent = $paymentService->createCheckoutSession($order);

        return response()->json([
            'client_secret' => $intent->client_secret
        ]);
    }

    public function success(Order $order)
    {
        // ❗ 不在这里改状态
        return view('orders.success', compact('order'));
    }
}
