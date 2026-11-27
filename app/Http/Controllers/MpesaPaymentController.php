<?php

namespace App\Http\Controllers;

use App\Services\MpesaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MpesaPaymentController extends Controller
{
    protected $mpesa;

    public function __construct(MpesaService $mpesa)
    {
        $this->mpesa = $mpesa;
    }

    /**
     * Display the M-PESA STK Push page.
     */
    public function index(Request $request)
    {
        $transactionId = $request->input('transaction_id');
        $phone = $request->input('phone');
        $amount = $request->input('amount');
        $partyName = $request->input('party_name');
        $title = $request->input('title');

        if (!$transactionId || !$phone || !$amount) {
            return redirect()->route('payment-method')->with('error', 'Missing payment details.');
        }

        return view('mpesa-stkpush', [
            'transaction_id' => $transactionId,
            'phone' => $phone,
            'amount' => $amount,
            'party_name' => $partyName,
            'title' => $title,
        ]);
    }

    /**
     * Submit the STK Push form.
     */
    public function submit(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|min:10',
            'party_name' => 'required|string|max:255',
            'transaction_id' => 'required|string',
        ]);

        $phone = preg_replace('/[^0-9+]/', '', $request->phone);

        $response = $this->mpesa->initiateStkPush($phone, $request->amount * 100, $request->party_name);

        if (isset($response['ResponseCode']) && $response['ResponseCode'] == 200) {
            Log::info('M-PESA STK Push initiated', [
                'transaction_id' => $request->transaction_id,
                'phone' => $phone,
                'amount' => $request->amount * 100,
                'response' => $response,
            ]);

            return redirect()->route('mpesa.stkpush.confirm', [
                'transaction_id' => $request->transaction_id,
                'phone' => $phone,
                'amount' => $request->amount,
                'party_name' => $request->party_name,
            ])->with('mpesa_success', 'You will receive a prompt message from M-PESA. Confirm the payment on your phone.');
        }

        $error = $response['ResponseDescription'] ?? 'Unknown error';
        Log::error('M-PESA STK Push failed', ['transaction_id' => $request->transaction_id, 'error' => $error]);

        return redirect()->route('mpesa.stkpush')->with('error', 'M-PESA payment initiation failed: ' . $error);
    }

    /**
     * Confirm page after STK Push initiation.
     */
    public function confirm(Request $request)
    {
        return view('mpesa-stkpush-confirm', [
            'transaction_id' => $request->transaction_id,
            'phone' => $request->phone,
            'amount' => $request->amount,
            'party_name' => $request->party_name,
        ]);
    }

    /**
     * Verify a payment after callback.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'm_pesa_receipt_number' => 'required|string|min:10',
        ]);

        $result = $this->mpesa->verifyPayment($request->m_pesa_receipt_number);

        Log::info('M-PESA verification result', [
            'receipt_number' => $request->m_pesa_receipt_number,
            'result' => $result,
        ]);

        return view('mpesa-verify', [
            'result' => $result,
            'receipt_number' => $request->m_pesa_receipt_number,
        ]);
    }
}
