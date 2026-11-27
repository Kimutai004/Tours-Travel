<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Destinations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Display the payment method selection page.
     */
    public function showPaymentMethod(Request $request)
    {
        $destination = Destinations::find($request->destination_id);

        if (!$destination) {
            return redirect()->route('packages')->with('error', 'Destination not found.');
        }

        $numericPrice = (int) preg_replace('/[^\d]/', '', $destination->pricing);
        $formattedPrice = 'KSh ' . number_format($numericPrice);

        return view('payment-method', [
            'destination' => $destination,
            'formattedPrice' => $formattedPrice,
            'priceNumeric' => $numericPrice,
            'expiresAt' => now()->addMinutes(10),
        ]);
    }

    /**
     * Process M-PESA payment.
     */
    public function mpesaPayment(Request $request)
    {
        $request->validate([
            'destination_id' => 'required|exists:destinations,id',
            'phone' => 'required|string|min:10',
            'party_name' => 'required|string|max:255',
        ]);

        $destination = Destinations::findOrFail($request->destination_id);
        $numericPrice = (int) preg_replace('/[^\d]/', '', $destination->pricing);
        $amountInCents = $numericPrice * 100;

        $mpesa = new \App\Services\MpesaService();
        $response = $mpesa->initiateStkPush($request->phone, $amountInCents, $request->party_name);

        $transactionId = Str::uuid()->toString();

        DB::transaction(function () use ($request, $destination, $numericPrice, $amountInCents, $response, $transactionId) {
            Payment::create([
                'user_id' => Auth::id(),
                'destination_id' => $destination->id,
                'payment_method' => 'mpesa',
                'payment_id' => $transactionId,
                'user_email' => $request->input('email', Auth::user()->email ?? null),
                'phone' => $request->phone,
                'amount' => $amountInCents,
                'currency' => 'KES',
                'status' => 'pending',
                'transaction_type' => 'CustomerPayBillOnline',
                'mpesa_callback_url' => env('MPESA_CALLBACK_URL') . '/mpesa/callback',
                'ip_address' => $request->ip(),
            ]);

            session()->flash('payment_transaction_id', $transactionId);
            session()->flash('mpesa_response', $response);
        });

        if (isset($response['ResponseCode']) && $response['ResponseCode'] == 200) {
            return redirect()->route('mpesa.stkpush')->with([
                'transaction_id' => $transactionId,
                'phone' => $request->phone,
                'amount' => $amountInCents / 100,
                'party_name' => $request->party_name,
                'title' => $destination->title,
            ]);
        }

        session()->flash('error', 'Failed to initiate M-PESA payment. Please try again.');
        return redirect()->back();
    }

    /**
     * Process card payment via Stripe.
     */
    public function cardPayment(Request $request)
    {
        $request->validate([
            'destination_id' => 'required|exists:destinations,id',
            'stripeToken' => 'required|string',
            'email' => 'required|email',
        ]);

        $destination = Destinations::findOrFail($request->destination_id);
        $numericPrice = (int) preg_replace('/[^\d]/', '', $destination->pricing);
        $amountInCents = $numericPrice * 100;

        try {
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            $charge = \Stripe\Charge::create([
                'amount' => $amountInCents,
                'currency' => 'usd',
                'source' => $request->stripeToken,
                'description' => "Payment for {$destination->title} by {$request->email}",
            ]);

            DB::transaction(function () use ($request, $destination, $numericPrice, $amountInCents, $charge) {
                Payment::create([
                    'user_id' => Auth::id(),
                    'destination_id' => $destination->id,
                    'payment_method' => 'card',
                    'payment_id' => $charge->id,
                    'user_email' => $request->email,
                    'phone' => null,
                    'amount' => $amountInCents,
                    'currency' => 'USD',
                    'status' => 'completed',
                    'transaction_type' => 'card',
                    'stripe_token' => $request->stripeToken,
                    'stripe_charge_id' => $charge->id,
                    'ip_address' => $request->ip(),
                ]);
            });

            session()->flash('success', 'Your card payment was successful!');

            return redirect()->route('payments.success')
                ->with('payment_id', $charge->id)
                ->with('payment_method', 'card')
                ->with('amount', $amountInCents / 100)
                ->with('destination_title', $destination->title);
        } catch (\Exception $e) {
            session()->flash('error', 'Card payment failed: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Handle M-PESA STK Push callback.
     */
    public function mpesaCallback(Request $request)
    {
        $body = $request->all();

        \Log::info('M-PESA callback received', $body);

        return response()->json(['status' => 'success']);
    }

    /**
     * Success page after payment.
     */
    public function success(Request $request)
    {
        $paymentId = $request->input('payment_id') ?? session('payment_id');
        $method = $request->input('payment_method') ?? session('payment_method');
        $amount = $request->input('amount') ?? session('amount');
        $title = $request->input('destination_title') ?? session('destination_title');

        $payment = null;
        if ($paymentId) {
            $payment = Payment::where('payment_id', $paymentId)
                ->orWhere('stripe_charge_id', $paymentId)
                ->first();
        }

        if (!$payment && !$method) {
            return redirect()->route('packages')->with('error', 'Payment details not found.');
        }

        return view('payment-success', [
            'payment' => $payment,
            'method' => $method,
            'amount' => $amount,
            'title' => $title,
        ]);
    }

    /**
     * Display payment history for the user.
     */
    public function history(Request $request)
    {
        $payments = Payment::where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return view('payment-history', [
            'payments' => $payments,
        ]);
    }
}

