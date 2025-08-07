<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Create Stripe Payment Intent
     * Expects:
     *  - booking_id: integer
     *  - amount: integer (in cents)
     */
public function createPaymentIntent(Request $request)
{
    $request->validate([
        'booking_id' => 'required|integer',
        'amount' => 'required|integer|min:50', // minimum 0.50 MAD in cents
    ]);


    Log::info('Stripe Secret Key', [
    'STRIPE_SECRET' => config('services.stripe.secret'),
]);


    // Log the incoming request for debug purposes
    Log::info('Stripe PaymentIntent request received', [
        'booking_id' => $request->booking_id,
        'amount' => $request->amount,
    ]);

    try {
Stripe::setApiKey(config('services.stripe.secret'));

        $paymentIntent = PaymentIntent::create([
            'amount' => $request->amount,
            'currency' => 'mad', // Moroccan Dirham
            'metadata' => [
                'booking_id' => $request->booking_id,
            ],
        ]);

        Log::info('Stripe PaymentIntent successfully created', [
            'payment_intent_id' => $paymentIntent->id,
            'booking_id' => $request->booking_id,
            'amount' => $request->amount,
        ]);

        return response()->json([
            'clientSecret' => $paymentIntent->client_secret,
        ]);
    } catch (\Exception $e) {
        Log::error('Stripe payment intent creation failed', [
            'error' => $e->getMessage(),
            'booking_id' => $request->booking_id,
            'amount' => $request->amount,
        ]);
        return response()->json([
            'error' => 'Failed to create payment intent: ' . $e->getMessage(),
        ], 500);
    }
}


    /**
     * Verify PayPal payment by order ID and booking_id
     * Expects:
     *  - orderID: string
     *  - booking_id: integer (optional, for your reference or validation)
     */
    public function verifyPaypalPayment(Request $request)
    {
        $request->validate([
            'orderID' => 'required|string',
            'booking_id' => 'sometimes|integer',
        ]);

        $orderId = $request->input('orderID');
        $bookingId = $request->input('booking_id');

        Log::info('Starting PayPal verification', ['orderID' => $orderId, 'booking_id' => $bookingId]);

       // Log PayPal API URL
Log::info('PayPal API URL from config', ['url' => config('services.paypal.PAYPAL_API_URL')]);

// Step 1: Get PayPal OAuth Access Token with extended timeout
$response = Http::asForm()
    ->withBasicAuth(config('services.paypal.public'), config('services.paypal.secret'))
    ->timeout(30)
    ->post(config('services.paypal.PAYPAL_API_URL') . '/v1/oauth2/token', [
        'grant_type' => 'client_credentials',
    ]);

if (!$response->ok()) {
    Log::error('Failed to authenticate with PayPal', ['response' => $response->body()]);
    return response()->json(['error' => 'Failed to authenticate with PayPal'], 500);
}


        $accessToken = $response->json()['access_token'];
        Log::info('Obtained PayPal access token');

        // Step 2: Fetch order details from PayPal
        $orderResponse = Http::withToken($accessToken)
            ->timeout(30)
            ->get(config('services.paypal.PAYPAL_API_URL') . "/v2/checkout/orders/{$orderId}");

        if (!$orderResponse->ok()) {
            Log::error('Failed to fetch PayPal order', ['response' => $orderResponse->body()]);
            return response()->json(['error' => 'Failed to fetch PayPal order'], 500);
        }

        $orderData = $orderResponse->json();
        Log::info('Fetched PayPal order data', ['orderData' => $orderData]);

        if (isset($orderData['status']) && $orderData['status'] === 'COMPLETED') {
            Log::info('PayPal payment completed successfully', ['orderID' => $orderId, 'booking_id' => $bookingId]);

            // TODO: Implement transaction save or booking status update here

            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully.',
                'order' => $orderData,
            ]);
        }

        Log::warning('PayPal payment not completed', [
            'orderID' => $orderId,
            'booking_id' => $bookingId,
            'status' => $orderData['status'] ?? 'unknown',
        ]);

        return response()->json(['error' => 'Payment not completed'], 400);
    }
}
