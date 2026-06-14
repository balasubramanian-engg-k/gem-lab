<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Log;
use App\Models\Payment;
use Carbon\Carbon;
use App\Models\Registration;
use App\Models\RegistrationType;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegistrationMail;
use App\Mail\RenewalMail;
class RazorpayController extends Controller
{
    // Create an order and return order details
    public function createOrder(Request $request)
    {
        try {
            $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
            $registrationType = RegistrationType::find($request->player_type);
            if (!$registrationType) {
                return response()->json(['success' => false, 'message' => 'Registration type not found'], 404);
            }
            $order = $api->order->create([
                'receipt' => 'order_'.time(),
                'amount' => $registrationType->amount * 100,
                'currency' => 'INR',
                'payment_capture' => 1
            ]);
           $orderAttributes = $order->toArray();
           $renewal = $request->boolean('renewal');
           $payment = Payment::create([
                'user_id' => $request->id,
                'renewal' => $renewal,
                'order_id' => $orderAttributes['id'],
                'amount' => $registrationType->amount,
                'status' => 'pending',
                'response_data' => json_encode((array)$order)
            ]);
            Log::info('Razorpay Order:', (array)$order);
            return response()->json([
                'success' => true,
                'key' => env('RAZORPAY_KEY'),
                'order' => $orderAttributes
            ]);
        } catch (\Exception $e) {
            Log::error('Razorpay Error:', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Verify the payment
    public function verifyPayment(Request $request)
    {
        try {
            $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

            $attributes = [
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature
            ];
            $responseObj = $api->utility->verifyPaymentSignature($attributes);
            Log::info('Razorpay Order:', $attributes);
            $registration = Registration::where('id', $request->id)->first();
            if ($registration) {
                $registration->status = 1;
                $registration->user_status = 1;
                $registration->save();
            }
            $payment = Payment::where('order_id', $request->razorpay_order_id)
              ->where('user_id', $request->id)
              ->first();
            if ($payment && $registration) {
                $payment->payment_id = $request->razorpay_payment_id;
                $payment->paid_at = Carbon::now();
                $payment->status = 'paid';
                $payment->save();
                $details = [
                    'name' => $registration->first_name.' '.$registration->last_name,
                    'mdccid' => $registration->mdcc_id,
                    'playerType' => $registration->player_type,
                ];
                if ($payment->renewal) {
                    Mail::to($registration->email)->send(new RenewalMail($details));
                } else {
                    Mail::to($registration->email)->send(new RegistrationMail($details));
                }
            }
            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully!'
            ]);
        } catch (\Exception $e) {
            Log::info('Razorpay Order:', 'Payment verification failed!');
            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed!'
            ], 500);
        }
    }
}
