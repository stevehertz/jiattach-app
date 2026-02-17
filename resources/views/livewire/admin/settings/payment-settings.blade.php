<div>
    {{-- Stop trying to control. --}}
    <div class="container-fluid">
        <!-- Tabs -->
        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-outline">
                    <div class="card-header p-0 pt-1">
                        <ul class="nav nav-tabs" id="payment-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link {{ $activeTab === 'mpesa' ? 'active' : '' }}"
                                    wire:click="$set('activeTab', 'mpesa')" href="#" role="tab">
                                    <i class="fas fa-mobile-alt mr-2"></i>M-Pesa (Daraja API)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $activeTab === 'pesapal' ? 'active' : '' }}"
                                    wire:click="$set('activeTab', 'pesapal')" href="#" role="tab">
                                    <i class="fas fa-globe mr-2"></i>Pesapal API
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $activeTab === 'test' ? 'active' : '' }}"
                                    wire:click="$set('activeTab', 'test')" href="#" role="tab">
                                    <i class="fas fa-flask mr-2"></i>Test Payments
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <!-- M-Pesa Tab -->
                        @if ($activeTab === 'mpesa')
                            <div>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <strong>M-Pesa Daraja API</strong> - Integration with Safaricom's payment API.
                                    Get your credentials from the <a href="https://developer.safaricom.co.ke/"
                                        target="_blank">Safaricom Developer Portal</a>.
                                </div>

                                <div class="row">
                                    <div class="col-md-8">
                                        <form wire:submit.prevent="saveMpesaSettings">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5 class="mb-0">M-Pesa Configuration</h5>
                                                </div>
                                                <div class="card-body">
                                                    <!-- Status Toggle -->
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 col-form-label">Status</label>
                                                        <div class="col-sm-9">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    id="mpesaActive" wire:model="mpesa_is_active">
                                                                <label class="custom-control-label" for="mpesaActive">
                                                                    {{ $mpesa_is_active ? 'Active' : 'Inactive' }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Environment -->
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 col-form-label">Environment</label>
                                                        <div class="col-sm-9">
                                                            <select wire:model="mpesa_environment" class="form-control">
                                                                <option value="sandbox">Sandbox (Testing)</option>
                                                                <option value="production">Production (Live)</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Consumer Key -->
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 col-form-label">Consumer Key</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" wire:model="mpesa_consumer_key"
                                                                class="form-control @error('mpesa_consumer_key') is-invalid @enderror"
                                                                placeholder="Enter consumer key">
                                                            @error('mpesa_consumer_key')
                                                                <span class="invalid-feedback">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <!-- Consumer Secret -->
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 col-form-label">Consumer Secret</label>
                                                        <div class="col-sm-9">
                                                            <input type="password" wire:model="mpesa_consumer_secret"
                                                                class="form-control @error('mpesa_consumer_secret') is-invalid @enderror"
                                                                placeholder="Enter consumer secret">
                                                            @error('mpesa_consumer_secret')
                                                                <span class="invalid-feedback">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <!-- Passkey -->
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 col-form-label">Passkey</label>
                                                        <div class="col-sm-9">
                                                            <input type="password" wire:model="mpesa_passkey"
                                                                class="form-control @error('mpesa_passkey') is-invalid @enderror"
                                                                placeholder="Enter STK passkey">
                                                            @error('mpesa_passkey')
                                                                <span class="invalid-feedback">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <!-- Shortcode -->
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 col-form-label">Shortcode</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" wire:model="mpesa_shortcode"
                                                                class="form-control @error('mpesa_shortcode') is-invalid @enderror"
                                                                placeholder="e.g., 174379">
                                                            @error('mpesa_shortcode')
                                                                <span class="invalid-feedback">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <!-- Transaction Type -->
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 col-form-label">Transaction Type</label>
                                                        <div class="col-sm-9">
                                                            <select wire:model="mpesa_transaction_type"
                                                                class="form-control">
                                                                <option value="paybill">Paybill</option>
                                                                <option value="till">Till Number (Buy Goods)</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Till Number (conditional) -->
                                                    @if ($mpesa_transaction_type === 'till')
                                                        <div class="form-group row">
                                                            <label class="col-sm-3 col-form-label">Till Number</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" wire:model="mpesa_till_number"
                                                                    class="form-control"
                                                                    placeholder="Enter till number">
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <!-- Callback URLs -->
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 col-form-label">Callback URL</label>
                                                        <div class="col-sm-9">
                                                            <input type="url" wire:model="mpesa_callback_url"
                                                                class="form-control"
                                                                placeholder="https://yourdomain.com/payments/mpesa/callback">
                                                            <small class="text-muted">Endpoint for payment
                                                                notifications</small>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label class="col-sm-3 col-form-label">Timeout URL</label>
                                                        <div class="col-sm-9">
                                                            <input type="url" wire:model="mpesa_timeout_url"
                                                                class="form-control"
                                                                placeholder="https://yourdomain.com/payments/mpesa/timeout">
                                                            <small class="text-muted">Endpoint for timeout
                                                                notifications</small>
                                                        </div>
                                                    </div>

                                                    <!-- Connection Test -->
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 col-form-label">Test Connection</label>
                                                        <div class="col-sm-9">
                                                            <button type="button" class="btn btn-info"
                                                                wire:click="testMpesaConnection"
                                                                wire:loading.attr="disabled">
                                                                <i class="fas fa-plug mr-1"></i>
                                                                <span wire:loading.remove
                                                                    wire:target="testMpesaConnection">Test
                                                                    Connection</span>
                                                                <span wire:loading
                                                                    wire:target="testMpesaConnection">Testing...</span>
                                                            </button>

                                                            @if ($mpesaTestResult)
                                                                <div
                                                                    class="mt-2 alert alert-{{ $mpesaTestResult['success'] ? 'success' : 'danger' }}">
                                                                    {{ $mpesaTestResult['message'] }}
                                                                    @if (!empty($mpesaTestResult['token']))
                                                                        <br><small>Token:
                                                                            {{ $mpesaTestResult['token'] }}</small>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-footer">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-save mr-1"></i>Save M-Pesa Settings
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="card">
                                            <div class="card-header bg-info">
                                                <h5 class="mb-0 text-white">M-Pesa Setup Guide</h5>
                                            </div>
                                            <div class="card-body">
                                                <h6>Step 1: Create Developer Account</h6>
                                                <p>Register at <a href="https://developer.safaricom.co.ke/"
                                                        target="_blank">Safaricom Developer Portal</a></p>

                                                <h6>Step 2: Create an App</h6>
                                                <p>Create a new app in sandbox environment to get credentials</p>

                                                <h6>Step 3: Get Credentials</h6>
                                                <ul class="pl-3">
                                                    <li>Consumer Key</li>
                                                    <li>Consumer Secret</li>
                                                    <li>Passkey (from app settings)</li>
                                                </ul>

                                                <h6>Step 4: Set Callback URLs</h6>
                                                <p>Use ngrok for local testing or your live domain</p>

                                                <h6>Test Credentials</h6>
                                                <p>Sandbox: Use 254708374149 for testing</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Pesapal Tab -->
                        @if ($activeTab === 'pesapal')
                            <div>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <strong>Pesapal API 3.0</strong> - Integration with Pesapal payment gateway.
                                    Get your credentials from the <a href="https://developer.pesapal.com/"
                                        target="_blank">Pesapal Developer Community</a>.
                                </div>

                                <div class="row">
                                    <div class="col-md-8">
                                        <form wire:submit.prevent="savePesapalSettings">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5 class="mb-0">Pesapal Configuration</h5>
                                                </div>
                                                <div class="card-body">
                                                    <!-- Status Toggle -->
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 col-form-label">Status</label>
                                                        <div class="col-sm-9">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    id="pesapalActive" wire:model="pesapal_is_active">
                                                                <label class="custom-control-label"
                                                                    for="pesapalActive">
                                                                    {{ $pesapal_is_active ? 'Active' : 'Inactive' }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Environment -->
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 col-form-label">Environment</label>
                                                        <div class="col-sm-9">
                                                            <select wire:model="pesapal_environment"
                                                                class="form-control">
                                                                <option value="sandbox">Sandbox (Testing)</option>
                                                                <option value="production">Production (Live)</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Consumer Key -->
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 col-form-label">Consumer Key</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" wire:model="pesapal_consumer_key"
                                                                class="form-control @error('pesapal_consumer_key') is-invalid @enderror"
                                                                placeholder="Enter consumer key">
                                                            @error('pesapal_consumer_key')
                                                                <span class="invalid-feedback">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <!-- Consumer Secret -->
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 col-form-label">Consumer Secret</label>
                                                        <div class="col-sm-9">
                                                            <input type="password"
                                                                wire:model="pesapal_consumer_secret"
                                                                class="form-control @error('pesapal_consumer_secret') is-invalid @enderror"
                                                                placeholder="Enter consumer secret">
                                                            @error('pesapal_consumer_secret')
                                                                <span class="invalid-feedback">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <!-- IPN ID -->
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 col-form-label">IPN ID</label>
                                                        <div class="col-sm-9">
                                                            <div class="input-group">
                                                                <input type="text" wire:model="pesapal_ipn_id"
                                                                    class="form-control @error('pesapal_ipn_id') is-invalid @enderror"
                                                                    placeholder="Enter IPN ID from Pesapal">
                                                                <div class="input-group-append">
                                                                    <button class="btn btn-outline-secondary"
                                                                        type="button" wire:click="registerPesapalIpn"
                                                                        title="Register IPN URL">
                                                                        <i class="fas fa-sync-alt"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            @error('pesapal_ipn_id')
                                                                <span class="invalid-feedback">{{ $message }}</span>
                                                            @enderror
                                                            <small class="text-muted">Instant Payment Notification
                                                                ID</small>
                                                        </div>
                                                    </div>

                                                    <!-- Currency -->
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 col-form-label">Currency</label>
                                                        <div class="col-sm-9">
                                                            <select wire:model="pesapal_currency"
                                                                class="form-control">
                                                                <option value="KES">KES - Kenyan Shilling</option>
                                                                <option value="UGX">UGX - Ugandan Shilling</option>
                                                                <option value="TZS">TZS - Tanzanian Shilling
                                                                </option>
                                                                <option value="USD">USD - US Dollar</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- URLs -->
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 col-form-label">Callback URL</label>
                                                        <div class="col-sm-9">
                                                            <input type="url" wire:model="pesapal_callback_url"
                                                                class="form-control"
                                                                placeholder="https://yourdomain.com/payments/pesapal/callback">
                                                            <small class="text-muted">Where users return after
                                                                payment</small>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label class="col-sm-3 col-form-label">Cancellation URL</label>
                                                        <div class="col-sm-9">
                                                            <input type="url"
                                                                wire:model="pesapal_cancellation_url"
                                                                class="form-control"
                                                                placeholder="https://yourdomain.com/payments/pesapal/cancel">
                                                            <small class="text-muted">Where users go if they
                                                                cancel</small>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label class="col-sm-3 col-form-label">Notification URL</label>
                                                        <div class="col-sm-9">
                                                            <input type="url"
                                                                wire:model="pesapal_notification_url"
                                                                class="form-control"
                                                                placeholder="https://yourdomain.com/payments/pesapal/ipn">
                                                            <small class="text-muted">IPN endpoint for payment
                                                                notifications</small>
                                                        </div>
                                                    </div>

                                                    <!-- Connection Test -->
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 col-form-label">Test Connection</label>
                                                        <div class="col-sm-9">
                                                            <button type="button" class="btn btn-info"
                                                                wire:click="testPesapalConnection"
                                                                wire:loading.attr="disabled">
                                                                <i class="fas fa-plug mr-1"></i>
                                                                <span wire:loading.remove
                                                                    wire:target="testPesapalConnection">Test
                                                                    Connection</span>
                                                                <span wire:loading
                                                                    wire:target="testPesapalConnection">Testing...</span>
                                                            </button>

                                                            @if ($pesapalTestResult)
                                                                <div
                                                                    class="mt-2 alert alert-{{ $pesapalTestResult['success'] ? 'success' : 'danger' }}">
                                                                    {{ $pesapalTestResult['message'] }}
                                                                    @if (!empty($pesapalTestResult['token']))
                                                                        <br><small>Token:
                                                                            {{ $pesapalTestResult['token'] }}</small>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-footer">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-save mr-1"></i>Save Pesapal Settings
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="card">
                                            <div class="card-header bg-info">
                                                <h5 class="mb-0 text-white">Pesapal Setup Guide</h5>
                                            </div>
                                            <div class="card-body">
                                                <h6>Step 1: Create Account</h6>
                                                <p>Register at <a href="https://developer.pesapal.com/"
                                                        target="_blank">Pesapal Developer Community</a></p>

                                                <h6>Step 2: Get API Credentials</h6>
                                                <p>Navigate to your dashboard to get Consumer Key and Secret</p>

                                                <h6>Step 3: Set Up IPN</h6>
                                                <p>Register your IPN URL to receive payment notifications</p>

                                                <h6>Step 4: Test in Sandbox</h6>
                                                <p>Use sandbox environment with test credentials</p>

                                                <h6>API 3.0 Endpoints</h6>
                                                <ul class="pl-3">
                                                    <li>Sandbox: cybqa.pesapal.com/pesapalv3/api</li>
                                                    <li>Live: pay.pesapal.com/v3/api</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Test Payments Tab -->
                        @if ($activeTab === 'test')
                            <div>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    <strong>Test Mode</strong> - These tests will initiate real API calls.
                                    Use sandbox/test credentials only!
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="mb-0">Test M-Pesa Payment</h5>
                                            </div>
                                            <div class="card-body">
                                                <form wire:submit.prevent="testMpesaPayment">
                                                    <div class="form-group">
                                                        <label>Phone Number</label>
                                                        <input type="text" wire:model="test_phone"
                                                            class="form-control @error('test_phone') is-invalid @enderror"
                                                            placeholder="e.g., 254708374149">
                                                        @error('test_phone')
                                                            <span class="invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                        <small class="text-muted">Format: 254XXXXXXXXX</small>
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Amount (KES)</label>
                                                        <input type="number" wire:model="test_amount"
                                                            class="form-control @error('test_amount') is-invalid @enderror"
                                                            min="1" max="1000">
                                                        @error('test_amount')
                                                            <span class="invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>

                                                    <button type="submit" class="btn btn-warning"
                                                        wire:loading.attr="disabled"
                                                        {{ !$mpesa_is_active ? 'disabled' : '' }}>
                                                        <i class="fas fa-paper-plane mr-1"></i>
                                                        <span wire:loading.remove wire:target="testMpesaPayment">Send
                                                            STK Push</span>
                                                        <span wire:loading
                                                            wire:target="testMpesaPayment">Sending...</span>
                                                    </button>

                                                    @if (!$mpesa_is_active)
                                                        <p class="text-muted mt-2">
                                                            <i class="fas fa-info-circle"></i>
                                                            Enable M-Pesa first to test
                                                        </p>
                                                    @endif
                                                </form>

                                                @if ($testResponse)
                                                    <div
                                                        class="mt-3 alert alert-{{ $testResponse['success'] ? 'success' : 'danger' }}">
                                                        {{ $testResponse['message'] }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="mb-0">Recent Test Transactions</h5>
                                            </div>
                                            <div class="card-body p-0">
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-hover mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th>Gateway</th>
                                                                <th>Amount</th>
                                                                <th>Status</th>
                                                                <th>Time</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach (\App\Models\PaymentTransaction::latest()->limit(5)->get() as $txn)
                                                                <tr>
                                                                    <td>
                                                                        <span
                                                                            class="badge badge-{{ $txn->gateway === 'mpesa' ? 'success' : 'info' }}">
                                                                            {{ strtoupper($txn->gateway) }}
                                                                        </span>
                                                                    </td>
                                                                    <td>{{ number_format($txn->amount) }} KES</td>
                                                                    <td>
                                                                        <span
                                                                            class="badge badge-{{ $txn->status === 'completed' ? 'success' : ($txn->status === 'failed' ? 'danger' : 'warning') }}">
                                                                            {{ $txn->status }}
                                                                        </span>
                                                                    </td>
                                                                    <td>{{ $txn->created_at->diffForHumans() }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
