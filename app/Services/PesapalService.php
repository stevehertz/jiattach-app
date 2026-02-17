<?php

namespace App\Services;

use App\Models\PaymentSetting;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Log;

class PesapalService
{
    protected PaymentSetting $config;
    protected ?string $token = null;
    protected $client;

    public function __construct()
    {
        $this->config = PaymentSetting::getActiveGateway('pesapal');
        $this->client = new \GuzzleHttp\Client();
    }

    /**
     * Submit order for payment
     */
    public function submitOrder(
        float $amount,
        string $description,
        array $billingAddress,
        ?string $callbackUrl = null
    ): array {
        if (!$this->config) {
            throw new \Exception('Pesapal not configured');
        }

        $token = $this->config->generateToken();
        if (!$token) {
            throw new \Exception('Failed to generate Pesapal token');
        }

        $merchantReference = uniqid('PSP', true);

        $payload = [
            'id' => $merchantReference,
            'currency' => $this->config->pesapal_currency,
            'amount' => $amount,
            'description' => $description,
            'callback_url' => $callbackUrl ?? $this->config->callback_url,
            'notification_id' => $this->config->pesapal_ipn_id,
            'billing_address' => [
                'email_address' => $billingAddress['email'] ?? '',
                'phone_number' => $billingAddress['phone'] ?? '',
                'country_code' => $billingAddress['country'] ?? 'KE',
                'first_name' => $billingAddress['first_name'] ?? '',
                'middle_name' => $billingAddress['middle_name'] ?? '',
                'last_name' => $billingAddress['last_name'] ?? '',
                'line_1' => $billingAddress['address'] ?? '',
                'city' => $billingAddress['city'] ?? '',
                'state' => $billingAddress['state'] ?? '',
                'postal_code' => $billingAddress['postal_code'] ?? '',
                'zip_code' => $billingAddress['zip_code'] ?? '',
            ],
        ];

        // Create transaction record
        $transaction = PaymentTransaction::create([
            'gateway' => 'pesapal',
            'merchant_reference' => $merchantReference,
            'amount' => $amount,
            'email' => $billingAddress['email'] ?? null,
            'phone_number' => $billingAddress['phone'] ?? null,
            'account_reference' => $merchantReference,
            'transaction_desc' => $description,
            'status' => 'pending',
            'request_payload' => $payload,
        ]);

        try {
            $response = $this->client->post(
                $this->config->getBaseUrl() . '/Transactions/SubmitOrderRequest',
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ],
                    'json' => $payload,
                ]
            );

            $result = json_decode($response->getBody(), true);
            
            // Update transaction with response
            $transaction->update([
                'transaction_id' => $result['order_tracking_id'] ?? null,
                'pesapal_tracking_id' => $result['order_tracking_id'] ?? null,
                'response_payload' => $result,
                'status' => 'processing',
            ]);

            return [
                'success' => true,
                'transaction' => $transaction,
                'redirect_url' => $result['redirect_url'] ?? null,
                'order_tracking_id' => $result['order_tracking_id'] ?? null,
            ];

        } catch (\Exception $e) {
            Log::error('Pesapal order submission failed: ' . $e->getMessage());
            
            $transaction->update([
                'status' => 'failed',
                'response_payload' => ['error' => $e->getMessage()],
            ]);

            throw $e;
        }
    }

    /**
     * Get transaction status
     */
    public function getTransactionStatus(string $orderTrackingId): array
    {
        $token = $this->config->generateToken();

        try {
            $response = $this->client->get(
                $this->config->getBaseUrl() . '/Transactions/GetTransactionStatus',
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ],
                    'query' => [
                        'orderTrackingId' => $orderTrackingId,
                    ],
                ]
            );

            return json_decode($response->getBody(), true);

        } catch (\Exception $e) {
            Log::error('Pesapal status check failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle IPN (Instant Payment Notification)
     */
    public function handleIPN(array $data): PaymentTransaction
    {
        $orderTrackingId = $data['order_tracking_id'] ?? null;
        
        if (!$orderTrackingId) {
            throw new \Exception('Invalid IPN data');
        }

        $transaction = PaymentTransaction::where('pesapal_tracking_id', $orderTrackingId)
            ->orWhere('transaction_id', $orderTrackingId)
            ->firstOrFail();

        $transaction->callback_payload = $data;

        // Get detailed status
        $status = $this->getTransactionStatus($orderTrackingId);
        
        $transaction->status = match($status['payment_status_description'] ?? '') {
            'COMPLETED' => 'completed',
            'FAILED' => 'failed',
            'PENDING' => 'processing',
            'REFUNDED' => 'refunded',
            default => $transaction->status,
        };

        if ($transaction->status === 'completed') {
            $transaction->paid_at = now();
        }

        $transaction->save();
        
        return $transaction;
    }

    /**
     * Register IPN URL
     */
    public function registerIPN(string $ipnUrl, string $ipnType = 'GET'): array
    {
        $token = $this->config->generateToken();

        try {
            $response = $this->client->post(
                $this->config->getBaseUrl() . '/URLSetup/RegisterIPN',
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ],
                    'json' => [
                        'url' => $ipnUrl,
                        'ipn_type' => $ipnType,
                    ],
                ]
            );

            return json_decode($response->getBody(), true);

        } catch (\Exception $e) {
            Log::error('Pesapal IPN registration failed: ' . $e->getMessage());
            throw $e;
        }
    }
}