<?php

namespace App\Services;

use App\Models\PaymentSetting;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Log;

class MpesaService
{
    protected PaymentSetting $config;
    protected ?string $token = null;
    protected $client;

    public function __construct()
    {
        $this->config = PaymentSetting::getActiveGateway('mpesa');
        $this->client = new \GuzzleHttp\Client();
    }

    /**
     * Initialize STK Push (Lipa Na M-Pesa Online)
     */
    public function stkPush(
        string $phoneNumber,
        float $amount,
        string $accountReference,
        string $transactionDesc
    ): array {
        if (!$this->config) {
            throw new \Exception('M-Pesa not configured');
        }

        $token = $this->config->generateToken();
        if (!$token) {
            throw new \Exception('Failed to generate M-Pesa token');
        }

        $timestamp = now()->format('YmdHis');
        $password = base64_encode(
            $this->config->mpesa_shortcode . 
            $this->config->mpesa_passkey . 
            $timestamp
        );

        $payload = [
            'BusinessShortCode' => $this->config->mpesa_shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => $this->config->mpesa_transaction_type === 'paybill' 
                ? 'CustomerPayBillOnline' 
                : 'CustomerBuyGoodsOnline',
            'Amount' => round($amount),
            'PartyA' => $this->formatPhoneNumber($phoneNumber),
            'PartyB' => $this->config->mpesa_shortcode,
            'PhoneNumber' => $this->formatPhoneNumber($phoneNumber),
            'CallBackURL' => $this->config->callback_url ?? route('payments.mpesa.callback'),
            'AccountReference' => substr($accountReference, 0, 12),
            'TransactionDesc' => substr($transactionDesc, 0, 13),
        ];

        // Create transaction record
        $transaction = PaymentTransaction::create([
            'gateway' => 'mpesa',
            'merchant_reference' => uniqid('MP', true),
            'amount' => $amount,
            'phone_number' => $phoneNumber,
            'account_reference' => $accountReference,
            'transaction_desc' => $transactionDesc,
            'status' => 'pending',
            'request_payload' => $payload,
        ]);

        try {
            $response = $this->client->post(
                $this->config->getBaseUrl() . '/mpesa/stkpush/v1/processrequest',
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => $payload,
                ]
            );

            $result = json_decode($response->getBody(), true);
            
            // Update transaction with response
            $transaction->update([
                'transaction_id' => $result['CheckoutRequestID'] ?? null,
                'response_payload' => $result,
            ]);

            return [
                'success' => true,
                'transaction' => $transaction,
                'response' => $result,
            ];

        } catch (\Exception $e) {
            Log::error('M-Pesa STK Push failed: ' . $e->getMessage());
            
            $transaction->update([
                'status' => 'failed',
                'response_payload' => ['error' => $e->getMessage()],
            ]);

            throw $e;
        }
    }

    /**
     * Query STK Push status
     */
    public function queryStatus(string $checkoutRequestId): array
    {
        $token = $this->config->generateToken();
        $timestamp = now()->format('YmdHis');
        $password = base64_encode(
            $this->config->mpesa_shortcode . 
            $this->config->mpesa_passkey . 
            $timestamp
        );

        $payload = [
            'BusinessShortCode' => $this->config->mpesa_shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'CheckoutRequestID' => $checkoutRequestId,
        ];

        try {
            $response = $this->client->post(
                $this->config->getBaseUrl() . '/mpesa/stkpushquery/v1/query',
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => $payload,
                ]
            );

            return json_decode($response->getBody(), true);

        } catch (\Exception $e) {
            Log::error('M-Pesa query failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle STK Push callback
     */
    public function handleCallback(array $data): PaymentTransaction
    {
        $callbackData = $data['Body']['stkCallback'] ?? null;
        
        if (!$callbackData) {
            throw new \Exception('Invalid callback data');
        }

        $transaction = PaymentTransaction::where('transaction_id', $callbackData['CheckoutRequestID'])
            ->firstOrFail();

        $transaction->callback_payload = $data;

        // Check if successful
        if ($callbackData['ResultCode'] == 0) {
            $metadata = collect($callbackData['CallbackMetadata']['Item'] ?? [])
                ->pluck('Value', 'Name');

            $transaction->status = 'completed';
            $transaction->mpesa_receipt = $metadata['MpesaReceiptNumber'] ?? null;
            $transaction->paid_at = now();
        } else {
            $transaction->status = 'failed';
        }

        $transaction->save();
        
        return $transaction;
    }

    /**
     * Format phone number to international format
     */
    private function formatPhoneNumber(string $phone): string
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // If starts with 0, replace with 254
        if (substr($phone, 0, 1) === '0') {
            $phone = '254' . substr($phone, 1);
        }
        
        // If starts with 7, add 254
        if (substr($phone, 0, 1) === '7') {
            $phone = '254' . $phone;
        }
        
        return $phone;
    }
}