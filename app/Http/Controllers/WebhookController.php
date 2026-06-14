<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentFailure;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();
        Log::info('Razorpay Webhook Payload:', $payload);

        // Get the webhook secret from .env
        $webhookSecret = env('RAZORPAY_WEBHOOK_SECRET');
        $signature = $request->header('X-Razorpay-Signature');

        if (!$this->isValidSignature($payload, $signature, $webhookSecret)) {
            Log::error('Invalid Razorpay webhook signature.');
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        // Handle the event based on its type
        $event = $payload['event'] ?? null;
        if ($event === 'payment.failed') {
            $payment = $payload['payload']['payment']['entity'] ?? [];
            $this->handlePaymentFailed($payment, $payload);
        } else {
            Log::warning('Unhandled event type: ' . $event);
        }

        return response()->json(['message' => 'Webhook received']);
    }

    private function isValidSignature($payload, $signature, $secret)
    {
        $expectedSignature = hash_hmac('sha256', json_encode($payload), $secret);
        return hash_equals($expectedSignature, $signature);
    }

    private function handlePaymentFailed($payment, $payload)
    {
        try {
            PaymentFailure::create([
                'order_id'          => $payment['order_id'] ?? null,
                'payment_id'        => $payment['id'] ?? null,
                'error_code'        => $payment['error_code'] ?? null,
                'error_description' => $payment['error_description'] ?? null,
                'error_source'      => $payment['error_source'] ?? null,
                'error_step'        => $payment['error_step'] ?? null,
                'error_reason'      => $payment['error_reason'] ?? null,
                'payload'           => json_encode($payload),
            ]);
            Log::error('Payment failed recorded:', $payment);
        } catch (\Exception $e) {
            Log::error('Error saving payment failure: ' . $e->getMessage());
        }
    }
}

