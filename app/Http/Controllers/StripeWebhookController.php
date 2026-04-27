<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Symfony\Component\HttpFoundation\Response;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('🔥 Webhook hit');
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');

        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endpointSecret
            );
        } catch (\UnexpectedValueException $e) {
            // payload 无效
            Log::error('Stripe Webhook Invalid Payload');
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // 签名失败
            Log::error('Stripe Webhook Invalid Signature');
            return response('Invalid signature', 400);
        }

        /*
        |--------------------------------------------------------------------------
        | 🎯 支付完成事件（核心）
        |--------------------------------------------------------------------------
        */
        if ($event->type === 'checkout.session.completed') {

            $session = $event->data->object;

            $orderId = $session->metadata->order_id ?? null;

            if (!$orderId) {
                Log::warning('Webhook missing order_id');
                return response('Missing order_id', 200);
            }

            $order = Order::find($orderId);

            if (!$order) {
                Log::warning("Order not found: {$orderId}");
                return response('Order not found', 200);
            }

            // 🔒 防重复执行（非常重要）
            if ($order->status === 'paid') {
                return response('Already processed', 200);
            }

            // ✅ 更新订单
            $order->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            // ✅ 更新 payment
            if ($order->payment) {
                $order->payment->update([
                    'status' => 'success',
                    'transaction_id' => $session->payment_intent ?? null,
                ]);
            }

            // ✅ 如果有 task → 开始执行
            if ($order->task) {
                $order->task->update([
                    'status' => 'in_progress'
                ]);
            }

            Log::info("Order paid successfully: {$orderId}");
        }

        /*
        |--------------------------------------------------------------------------
        | ❌ 支付失败（可选）
        |--------------------------------------------------------------------------
        */
        if ($event->type === 'payment_intent.payment_failed') {

            $intent = $event->data->object;

            Log::warning("Payment failed: " . $intent->id);

            // 这里可以扩展：
            // - 更新 payment status
            // - 通知用户
        }

        return response('OK', Response::HTTP_OK);
    }
}