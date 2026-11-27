<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MpesaService
{
    protected $consumerKey;
    protected $consumerSecret;
    protected $businessShortCode;
    protected $passkey;
    protected $callbackUrl;
    protected $production;

    public function __construct()
    {
        $this->consumerKey = env('MPESA_CONSUMER_KEY');
        $this->consumerSecret = env('MPESA_CONSUMER_SECRET');
        $this->businessShortCode = env('MPESA_BUSINESS_SHORT_CODE');
        $this->passkey = env('MPESA_PASSKEY');
        $this->callbackUrl = env('MPESA_CALLBACK_URL');
        $this->production = filter_var(env('MPESA_PRODUCTION', false), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Get M-PESA access token.
     */
    public function getAccessToken(): string
    {
        if (empty($this->consumerKey) || empty($this->consumerSecret)) {
            Log::error('M-PESA: Missing consumer key or secret');
            return '';
        }

        $base64Credentials = base64_encode("{$this->consumerKey}:{$this->consumerSecret}");

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $base64Credentials,
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])->post('https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');

        if ($response->successful()) {
            $data = $response->json();
            return $data['access_token'] ?? '';
        }

        Log::error('M-PESA: Failed to get access token', ['response' => $response->body()]);
        return '';
    }

    /**
     * Generate the password for STK Push request.
     */
    public function generatePassword(): string
    {
        $timestamp = now()->format('YmdHis');
        return hash('sha256', $this->businessShortCode . $this->passkey . $timestamp);
    }
    /**
     * Initiate STK Push request for M-PESA.
     *
     * @param  string  $phone  M-PESA registered phone number
     * @param  float  $amount  Amount in cents (e.g., 100 = KSh 1.00)
     * @param  string  $partyName  Account reference / party name
     */
    public function initiateStkPush(string $phone, float $amount, string $partyName): array
    {
        if (!$this->production && empty($this->consumerKey)) {
            return [
                'ResponseDescription' => 'M-PESA sandbox mode - set MPESA_CONSUMER_KEY in .env',
                'Error' => 'Missing credentials',
            ];
        }

        $accessToken = $this->getAccessToken();

        if (empty($accessToken)) {
            return [
                'ResponseDescription' => 'Failed to obtain access token',
                'Error' => 'Authentication failed',
            ];
        }

        $data = [
            'BusinessShortCode' => $this->businessShortCode,
            'Password' => $this->generatePassword(),
            'Timestamp' => now()->format('YmdHis'),
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amount,
            'PartyA' => $phone,
            'PartyB' => $this->businessShortCode,
            'PhoneNumber' => $phone,
            'CallBackURL' => $this->callbackUrl,
            'AccountReference' => $partyName,
            'TransactionDesc' => $partyName,
        ];

        $client = new Client([
            'timeout' => 30,
        ]);

        try {
            $response = $client->post(
                $this->production
                    ? 'https://api.safaricom.co.ke/mpesa/stkpush/v1/process/request'
                    : 'https://api.safaricom.co.ke/mpesa/stkpush/v1/process/request',
                [
                    'headers' => [
                        'Authorization' => "Bearer {$accessToken}",
                        'Content-Type' => 'application/json',
                    ],
                    'json' => $data,
                ]
            );

            return [
                'ResponseDescription' => $response->getReasonPhrase(),
                'ResponseCode' => $response->getStatusCode(),
                'body' => $response->getBody()->getContents(),
            ];
        } catch (\Exception $e) {
            Log::error('M-PESA: STK Push failed', ['error' => $e->getMessage()]);
            return [
                'ResponseDescription' => 'Exception',
                'Error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify M-PESA payment (called by the callback URL).
     */
    public function verifyPayment(string $mPesaReceiptNumber): array
    {
        if (empty($this->businessShortCode)) {
            return ['ResponseDescription' => 'Business short code not configured'];
        }

        $accessToken = $this->getAccessToken();

        if (empty($accessToken)) {
            return ['ResponseDescription' => 'Failed to obtain access token'];
        }

        $data = http_build_query([
            'BusinessShortCode' => $this->businessShortCode,
            'Password' => $this->generatePassword(),
            'Timestamp' => now()->format('YmdHis'),
            'TransactionType' => 'QueryAttemptedTransaction',
            'PartyA' => $mPesaReceiptNumber,
            'IdentifierType' => '4',
            'ResultCodes' => '1',
        ]);

        $client = new Client([
            'timeout' => 30,
        ]);

        try {
            $response = $client->post(
                $this->production
                    ? 'https://api.safaricom.co.ke/mpesa/mpesaapi/v1/transactionstatus'
                    : 'https://api.safaricom.co.ke/mpesa/mpesaapi/v1/transactionstatus',
                [
                    'headers' => [
                        'Authorization' => "Bearer {$accessToken}",
                        'Content-Type' => 'application/x-www-form-urlencoded',
                    ],
                    'body' => $data,
                ]
            );

            return [
                'ResponseDescription' => $response->getReasonPhrase(),
                'ResponseCode' => $response->getStatusCode(),
                'body' => $response->getBody()->getContents(),
            ];
        } catch (\Exception $e) {
            Log::error('M-PESA: Payment verification failed', ['error' => $e->getMessage()]);
            return [
                'ResponseDescription' => 'Exception',
                'Error' => $e->getMessage(),
            ];
        }
    }
}

