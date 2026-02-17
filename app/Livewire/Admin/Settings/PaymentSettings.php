<?php

namespace App\Livewire\Admin\Settings;

use App\Models\PaymentSetting;
use App\Services\MpesaService;
use App\Services\PesapalService;
use Livewire\Component;
use Livewire\WithFileUploads;

class PaymentSettings extends Component
{
    use WithFileUploads;

    // Active tab
    public $activeTab = 'mpesa';
    
    // M-Pesa settings
    public $mpesa_is_active = false;
    public $mpesa_environment = 'sandbox';
    public $mpesa_consumer_key;
    public $mpesa_consumer_secret;
    public $mpesa_passkey;
    public $mpesa_shortcode;
    public $mpesa_till_number;
    public $mpesa_paybill;
    public $mpesa_transaction_type = 'paybill';
    public $mpesa_callback_url;
    public $mpesa_timeout_url;
    
    // Pesapal settings
    public $pesapal_is_active = false;
    public $pesapal_environment = 'sandbox';
    public $pesapal_consumer_key;
    public $pesapal_consumer_secret;
    public $pesapal_ipn_id;
    public $pesapal_currency = 'KES';
    public $pesapal_callback_url;
    public $pesapal_cancellation_url;
    public $pesapal_notification_url;
    
    // Test payment
    public $test_phone;
    public $test_amount = 10;
    public $test_email;
    public $testResponse;
    public $testLoading = false;
    
    // Connection test results
    public $mpesaTestResult = null;
    public $pesapalTestResult = null;
    public $testingMpesa = false;
    public $testingPesapal = false;

    protected function rules()
    {
        return [
            // M-Pesa rules
            'mpesa_consumer_key' => 'required_if:mpesa_is_active,true',
            'mpesa_consumer_secret' => 'required_if:mpesa_is_active,true',
            'mpesa_passkey' => 'required_if:mpesa_is_active,true',
            'mpesa_shortcode' => 'required_if:mpesa_is_active,true',
            
            // Pesapal rules
            'pesapal_consumer_key' => 'required_if:pesapal_is_active,true',
            'pesapal_consumer_secret' => 'required_if:pesapal_is_active,true',
            'pesapal_ipn_id' => 'required_if:pesapal_is_active,true',
        ];
    }

    protected function messages()
    {
        return [
            'mpesa_consumer_key.required_if' => 'Consumer Key is required when M-Pesa is active',
            'mpesa_consumer_secret.required_if' => 'Consumer Secret is required when M-Pesa is active',
            'mpesa_passkey.required_if' => 'Passkey is required when M-Pesa is active',
            'mpesa_shortcode.required_if' => 'Shortcode is required when M-Pesa is active',
            'pesapal_consumer_key.required_if' => 'Consumer Key is required when Pesapal is active',
            'pesapal_consumer_secret.required_if' => 'Consumer Secret is required when Pesapal is active',
            'pesapal_ipn_id.required_if' => 'IPN ID is required when Pesapal is active',
        ];
    }

     public function mount()
    {
        $this->loadSettings();
    }

    protected function loadSettings()
    {
        // Load M-Pesa settings
        $mpesa = PaymentSetting::where('gateway', 'mpesa')->first();
        if ($mpesa) {
            $this->mpesa_is_active = $mpesa->is_active;
            $this->mpesa_environment = $mpesa->environment;
            $this->mpesa_consumer_key = $mpesa->mpesa_consumer_key;
            $this->mpesa_consumer_secret = $mpesa->mpesa_consumer_secret;
            $this->mpesa_passkey = $mpesa->mpesa_passkey;
            $this->mpesa_shortcode = $mpesa->mpesa_shortcode;
            $this->mpesa_till_number = $mpesa->mpesa_till_number;
            $this->mpesa_paybill = $mpesa->mpesa_paybill;
            $this->mpesa_transaction_type = $mpesa->mpesa_transaction_type ?? 'paybill';
            $this->mpesa_callback_url = $mpesa->callback_url ?? route('payments.mpesa.callback');
            $this->mpesa_timeout_url = $mpesa->timeout_url ?? route('payments.mpesa.timeout');
        }

        // Load Pesapal settings
        $pesapal = PaymentSetting::where('gateway', 'pesapal')->first();
        if ($pesapal) {
            $this->pesapal_is_active = $pesapal->is_active;
            $this->pesapal_environment = $pesapal->environment;
            $this->pesapal_consumer_key = $pesapal->pesapal_consumer_key;
            $this->pesapal_consumer_secret = $pesapal->pesapal_consumer_secret;
            $this->pesapal_ipn_id = $pesapal->pesapal_ipn_id;
            $this->pesapal_currency = $pesapal->pesapal_currency ?? 'KES';
            $this->pesapal_callback_url = $pesapal->callback_url ?? route('payments.pesapal.callback');
            $this->pesapal_cancellation_url = $pesapal->cancellation_url ?? route('payments.pesapal.cancel');
            $this->pesapal_notification_url = $pesapal->notification_url ?? route('payments.pesapal.notification');
        }
    }

    public function saveMpesaSettings()
    {
        $this->validateOnly('mpesa_consumer_key');
        $this->validateOnly('mpesa_consumer_secret');
        $this->validateOnly('mpesa_passkey');
        $this->validateOnly('mpesa_shortcode');

        try {
            PaymentSetting::updateOrCreate(
                ['gateway' => 'mpesa'],
                [
                    'is_active' => $this->mpesa_is_active,
                    'environment' => $this->mpesa_environment,
                    'mpesa_consumer_key' => $this->mpesa_consumer_key,
                    'mpesa_consumer_secret' => $this->mpesa_consumer_secret,
                    'mpesa_passkey' => $this->mpesa_passkey,
                    'mpesa_shortcode' => $this->mpesa_shortcode,
                    'mpesa_till_number' => $this->mpesa_till_number,
                    'mpesa_paybill' => $this->mpesa_paybill,
                    'mpesa_transaction_type' => $this->mpesa_transaction_type,
                    'callback_url' => $this->mpesa_callback_url,
                    'timeout_url' => $this->mpesa_timeout_url,
                ]
            );

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'M-Pesa settings saved successfully!'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to save settings: ' . $e->getMessage()
            ]);
        }
    }

    public function savePesapalSettings()
    {
        $this->validateOnly('pesapal_consumer_key');
        $this->validateOnly('pesapal_consumer_secret');
        $this->validateOnly('pesapal_ipn_id');

        try {
            PaymentSetting::updateOrCreate(
                ['gateway' => 'pesapal'],
                [
                    'is_active' => $this->pesapal_is_active,
                    'environment' => $this->pesapal_environment,
                    'pesapal_consumer_key' => $this->pesapal_consumer_key,
                    'pesapal_consumer_secret' => $this->pesapal_consumer_secret,
                    'pesapal_ipn_id' => $this->pesapal_ipn_id,
                    'pesapal_currency' => $this->pesapal_currency,
                    'callback_url' => $this->pesapal_callback_url,
                    'cancellation_url' => $this->pesapal_cancellation_url,
                    'notification_url' => $this->pesapal_notification_url,
                ]
            );

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Pesapal settings saved successfully!'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to save settings: ' . $e->getMessage()
            ]);
        }
    }

    public function testMpesaConnection()
    {
        $this->testingMpesa = true;
        $this->mpesaTestResult = null;

        try {
            $service = new MpesaService();
            $token = app()->make(\App\Models\PaymentSetting::class)
                ->getActiveGateway('mpesa')
                ?->generateToken();

            $this->mpesaTestResult = [
                'success' => !empty($token),
                'message' => $token ? '✓ Connection successful!' : '✗ Failed to generate token',
                'token' => $token ? substr($token, 0, 20) . '...' : null,
            ];

        } catch (\Exception $e) {
            $this->mpesaTestResult = [
                'success' => false,
                'message' => '✗ Connection failed: ' . $e->getMessage(),
            ];
        }

        $this->testingMpesa = false;
    }

    public function testPesapalConnection()
    {
        $this->testingPesapal = true;
        $this->pesapalTestResult = null;

        try {
            $service = new PesapalService();
            $token = app()->make(\App\Models\PaymentSetting::class)
                ->getActiveGateway('pesapal')
                ?->generateToken();

            $this->pesapalTestResult = [
                'success' => !empty($token),
                'message' => $token ? '✓ Connection successful!' : '✗ Failed to generate token',
                'token' => $token ? substr($token, 0, 20) . '...' : null,
            ];

        } catch (\Exception $e) {
            $this->pesapalTestResult = [
                'success' => false,
                'message' => '✗ Connection failed: ' . $e->getMessage(),
            ];
        }

        $this->testingPesapal = false;
    }

    public function testMpesaPayment()
    {
        $this->validate([
            'test_phone' => 'required|regex:/^[0-9]{10,12}$/',
            'test_amount' => 'required|numeric|min:1|max:1000',
        ]);

        $this->testLoading = true;
        $this->testResponse = null;

        try {
            $service = new MpesaService();
            $result = $service->stkPush(
                $this->test_phone,
                $this->test_amount,
                'TEST' . now()->timestamp,
                'Test Payment'
            );

            $this->testResponse = [
                'success' => true,
                'message' => '✓ STK Push sent! Check your phone to complete payment.',
                'data' => $result,
            ];

        } catch (\Exception $e) {
            $this->testResponse = [
                'success' => false,
                'message' => '✗ Test failed: ' . $e->getMessage(),
            ];
        }

        $this->testLoading = false;
    }

    public function registerPesapalIpn()
    {
        try {
            $service = new PesapalService();
            $result = $service->registerIPN($this->pesapal_notification_url);

            if (!empty($result['ipn_id'])) {
                $this->pesapal_ipn_id = $result['ipn_id'];
                $this->savePesapalSettings();
                
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'IPN URL registered successfully! IPN ID: ' . $result['ipn_id']
                ]);
            }

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'IPN registration failed: ' . $e->getMessage()
            ]);
        }
    }
    
    public function render()
    {
        return view('livewire.admin.settings.payment-settings');
    }
}
