<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Transaction;
use App\Models\Customer;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Checkout\Session;

class PaymentController extends Controller
{
    public function handleWebhook(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        // ✅ Retrieve the Stripe payload
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('services.stripe.webhook_secret');

        try {
            // ✅ Verify webhook signature (Optional but recommended)
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe Webhook Signature Verification Failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        // ✅ Handle the 'checkout.session.completed' event
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            // ✅ Retrieve metadata
            $customerId = $session->metadata->customer_id ?? null;
            $invoiceNumber = $session->metadata->invoice_number ?? null;
            $transactionId = $session->id; // Stripe transaction ID

            if ($customerId && $invoiceNumber) {
                // ✅ Find the corresponding transaction
                $transaction = Transaction::where('transaction_refrence_number', $transactionId)->first();

                if ($transaction) {
                    // ✅ Update transaction status to 'paid'
                    $transaction->update(['status' => 'paid']);

                    // ✅ Update customer status if needed
                    Customer::where('id', $customerId)->update(['status' => 1]);

                    Log::info('Stripe Payment Confirmed', [
                        'customer_id' => $customerId,
                        'invoice_number' => $invoiceNumber,
                        'transaction_id' => $transactionId,
                        'status' => 'paid'
                    ]);

                    return response()->json(['message' => 'Payment processed successfully'], 200);
                } else {
                    Log::error('Transaction Not Found for Webhook', ['transaction_id' => $transactionId]);
                }
            }
        }

        return response()->json(['message' => 'Webhook handled'], 200);
    }

    public function paymentSuccess(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $sessionId = $request->query('session_id');

        // ✅ Debug the request parameters
        Log::info('🔍 Stripe Payment Success - Request Params', $request->all());

        if (!$sessionId) {
            Log::error('❌ Missing session_id in paymentSuccess URL.');
            return redirect()->route('customers.index')->with('error', 'Invalid payment session.');
        }

        try {
            // ✅ Retrieve the session from Stripe
            Log::info('🔍 Retrieving session from Stripe:', ['session_id' => $sessionId]);
            $session = Session::retrieve($sessionId);

            $customerId = $session->metadata->customer_id ?? null;
            $invoiceNumber = $session->metadata->invoice_number ?? null;

            if ($customerId && $invoiceNumber) {
                // ✅ Find the transaction by Stripe session ID
                $transaction = Transaction::where('transaction_refrence_number', $sessionId)->first();

                if ($transaction) {
                    $transaction->update(['status' => 'paid']);
                    Customer::where('id', $customerId)->update(['transaction_refrence_number' => $sessionId]);

                    Log::info('✅ Payment Successful', [
                        'customer_id' => $customerId,
                        'invoice_number' => $invoiceNumber,
                        'transaction_id' => $sessionId,
                        'status' => 'paid'
                    ]);
                } else {
                    Log::error('❌ Transaction not found in database for session ID:', ['session_id' => $sessionId]);
                }
            }

            return redirect()->away('https://www.stripe.com');

        } catch (\Exception $e) {
            Log::error('❌ Stripe Payment Success Handling Failed', [
                'error' => $e->getMessage(),
                'session_id' => $sessionId,
            ]);

            return redirect()->away('https://www.stripe.com');
        }
    }


    /**
     * ❌ Handle canceled payment
     */
    public function paymentCancel()
    {
        Log::warning('User canceled the payment');
        return redirect()->away('https://www.stripe.com');
    }

}
