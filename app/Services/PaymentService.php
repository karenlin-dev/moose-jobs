<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Checkout\Session;

class PaymentService
{
    public function createCheckoutSession($order)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $session = Session::create([
            'payment_method_types' => ['card'],

            'line_items' => [[
                'price_data' => [
                    'currency' => 'cad',
                    'product_data' => [
                        'name' => 'Airport Pickup Service',
                    ],
                    'unit_amount' => $order->amount * 100,
                ],
                'quantity' => 1,
            ]],

            'mode' => 'payment',

            'metadata' => [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'service_type' => $order->service_type,
            ],

            'success_url' => route('orders.success', $order),
            'cancel_url' => route('orders.show', $order),
        ]);

        return $session->url;
    }
}