<?php
namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\PaymentSettings;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentController extends Controller
{
    public function createPaymentIntent(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|integer',
            'amount' => 'required|integer|min:50',
        ]);

        // 🔐 Fetch Stripe settings from DB
        $stripe = PaymentSettings::where('type', 'stripe')->first();


        Log::info('Stripe settings loaded:', $stripe ? $stripe->toArray() : ['null']);

        if (!$stripe || !$stripe->secret_key) {
            Log::error('Stripe secret key missing in settings table');
            return response()->json(['error' => 'Stripe configuration missing.'], 500);
        }

        Log::info('Stripe PaymentIntent request received', [
            'booking_id' => $request->booking_id,
            'amount' => $request->amount,
        ]);

        try {
            Stripe::setApiKey($stripe->secret_key);

            $paymentIntent = PaymentIntent::create([
                'amount' => $request->amount,
                'currency' => 'mad',
                'metadata' => [
                    'booking_id' => $request->booking_id,
                ],
            ]);

            Log::info('Stripe PaymentIntent created', [
                'payment_intent_id' => $paymentIntent->id,
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
            ]);
        } catch (\Exception $e) {
            Log::error('Stripe PaymentIntent failed', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => 'Failed to create payment intent: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function verifyPaypalPayment(Request $request)
    {
        $request->validate([
            'orderID' => 'required|string',
            'booking_id' => 'sometimes|integer',
        ]);

        $orderId = $request->input('orderID');
        $bookingId = $request->input('booking_id');

        // 🔐 Fetch PayPal settings from DB
        $paypal = PaymentSettings::where('type', 'paypal')->first();

        Log::info('PayPal settings loaded:', $paypal ? $paypal->toArray() : ['null']);

        if (!$paypal || !$paypal->public_key || !$paypal->secret_key || !$paypal->api_url) {
            Log::error('PayPal configuration missing in DB');
            return response()->json(['error' => 'PayPal configuration missing.'], 500);
        }

        Log::info('Starting PayPal verification', ['orderID' => $orderId]);

        // Step 1: Get access token
        $response = Http::asForm()
            ->withBasicAuth($paypal->public_key, $paypal->secret_key)
            ->timeout(30)
            ->post($paypal->api_url . '/v1/oauth2/token', [
                'grant_type' => 'client_credentials',
            ]);

        if (!$response->ok()) {
            Log::error('Failed to authenticate with PayPal', ['response' => $response->body()]);
            return response()->json(['error' => 'Failed to authenticate with PayPal'], 500);
        }

        $accessToken = $response->json()['access_token'];
        Log::info('Obtained PayPal access token');

        // Step 2: Fetch order details
        $orderResponse = Http::withToken($accessToken)
            ->timeout(30)
            ->get($paypal->api_url . "/v2/checkout/orders/{$orderId}");

        if (!$orderResponse->ok()) {
            Log::error('Failed to fetch PayPal order', ['response' => $orderResponse->body()]);
            return response()->json(['error' => 'Failed to fetch PayPal order'], 500);
        }

        $orderData = $orderResponse->json();

        if ($orderData['status'] === 'COMPLETED') {
            Log::info('PayPal payment completed', ['orderID' => $orderId]);

            // TODO: Store transaction details here

            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully.',
                'order' => $orderData,
            ]);
        }

        Log::warning('PayPal payment not completed', ['status' => $orderData['status'] ?? 'unknown']);
        return response()->json(['error' => 'Payment not completed'], 400);
    }
}
