<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe;

class StripePaymentController extends Controller
{
    /**
     * Display the Stripe payment page.
     */
    public function stripe(Request $request)
    {
        $destination = null;
        if ($request->has('destination_id')) {
            $destination = \App\Models\Destinations::find($request->destination_id);
        }

        return view('stripe', [
            'destinations' => $destination ?? \App\Models\Destinations::first(),
        ]);
    }

    /**
     * Process card payment via Stripe.
     */
    public function stripePost(Request $request)
    {
        $request->validate([
            'destination_id' => 'required|exists:destinations,id',
            'stripeToken' => 'required|string',
            'email' => 'required|email',
        ]);

        $destination = \App\Models\Destinations::findOrFail($request->destination_id);
        $numericPrice = (int) preg_replace('/[^\d]/', '', $destination->pricing);
        $amountInCents = $numericPrice * 100;

        try {
            Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            $charge = Stripe\Charge::create([
                'amount' => $amountInCents,
                'currency' => 'usd',
                'source' => $request->stripeToken,
                'description' => "Payment for {$destination->title} by {$request->email}",
            ]);

            \Illuminate\Support\Facades\DB::transaction(function () use ($request, $destination, $numericPrice, $amountInCents, $charge) {
                \App\Models\Payment::create([
                    'user_id' => Auth::id(),
                    'destination_id' => $destination->id,
                    'payment_method' => 'card',
                    'payment_id' => $charge->id,
                    'user_email' => $request->email,
                    'amount' => $amountInCents,
                    'currency' => 'USD',
                    'status' => 'completed',
                    'transaction_type' => 'card',
                    'stripe_token' => $request->stripeToken,
                    'stripe_charge_id' => $charge->id,
                    'ip_address' => $request->ip(),
                ]);
            });

            $request->session()->flash('success', 'Your card payment was successful!');

            return redirect()->route('payments.success')
                ->with('payment_id', $charge->id)
                ->with('payment_method', 'card')
                ->with('amount', $amountInCents / 100)
                ->with('destination_title', $destination->title);
        } catch (\Exception $e) {
            $request->session()->flash('error', 'Card payment failed: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
