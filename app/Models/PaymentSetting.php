<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class PaymentSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'gateway',
        'is_active',
        'is_sandbox',
        'environment',
        'callback_url',
        'timeout_url',
        'webhook_secret',
        'mpesa_consumer_key',
        'mpesa_consumer_secret',
        'mpesa_passkey',
        'mpesa_shortcode',
        'mpesa_till_number',
        'mpesa_paybill',
        'mpesa_transaction_type',
        'pesapal_consumer_key',
        'pesapal_consumer_secret',
        'pesapal_ipn_id',
        'pesapal_currency',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_sandbox' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Get active gateway configuration
     */
    public static function getActiveGateway(string $gateway): ?self
    {
        return self::where('gateway', $gateway)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get base URL based on environment
     */
    public function getBaseUrl(): string
    {
        if ($this->gateway === 'mpesa') {
            return $this->environment === 'production'
                ? 'https://api.safaricom.co.ke'
                : 'https://sandbox.safaricom.co.ke';
        }
        
        // Pesapal
        return $this->environment === 'production'
            ? 'https://pay.pesapal.com/v3/api'
            : 'https://cybqa.pesapal.com/pesapalv3/api';
    }

    /**
     * Generate auth token for gateway
     */
    public function generateToken(): ?string
    {
        if ($this->gateway === 'mpesa') {
            return $this->generateMpesaToken();
        }
        
        return $this->generatePesapalToken();
    }

    private function generateMpesaToken(): ?string
    {
        $credentials = base64_encode(
            $this->mpesa_consumer_key . ':' . $this->mpesa_consumer_secret
        );

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->get($this->getBaseUrl() . '/oauth/v1/generate?grant_type=client_credentials', [
                'headers' => [
                    'Authorization' => 'Basic ' . $credentials,
                ],
            ]);

            $result = json_decode($response->getBody(), true);
            return $result['access_token'] ?? null;
        } catch (\Exception $e) {
            Log::error('M-Pesa token generation failed: ' . $e->getMessage());
            return null;
        }
    }

    private function generatePesapalToken(): ?string
    {
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post($this->getBaseUrl() . '/Auth/RequestToken', [
                'json' => [
                    'consumer_key' => $this->pesapal_consumer_key,
                    'consumer_secret' => $this->pesapal_consumer_secret,
                ],
            ]);

            $result = json_decode($response->getBody(), true);
            return $result['token'] ?? null;
        } catch (\Exception $e) {
            Log::error('Pesapal token generation failed: ' . $e->getMessage());
            return null;
        }
    }
}
