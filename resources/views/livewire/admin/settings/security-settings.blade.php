<div>
    {{-- Because she competes with no one, no one can compete with her. --}}
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card card-success">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-2 col-6">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3>{{ $stats['failed_attempts_today'] }}</h3>
                                        <p>Failed Attempts Today</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-ban"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-6">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3>{{ $stats['successful_logins_today'] }}</h3>
                                        <p>Successful Logins</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-sign-in-alt"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-6">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3>{{ $stats['unique_ips_today'] }}</h3>
                                        <p>Unique IPs Today</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-network-wired"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-primary">
                                    <div class="inner">
                                        <h3>{{ $stats['users_with_2fa'] }}/{{ $stats['total_users'] }}</h3>
                                        <p>Users with 2FA Enabled</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-mobile-alt"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-danger">
                                    <div class="inner">
                                        <h3>{{ count($ipWhitelist) }}</h3>
                                        <p>Whitelisted IPs</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="row">
            <div class="col-12">
                <div class="card card-success card-outline">
                    <div class="card-header p-0 pt-1">
                        <ul class="nav nav-tabs" id="security-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link {{ $activeTab === 'password-policy' ? 'active' : '' }}"
                                    wire:click="$set('activeTab', 'password-policy')" href="#password-policy"
                                    role="tab">
                                    <i class="fas fa-key mr-2"></i>Password Policy
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $activeTab === 'session' ? 'active' : '' }}"
                                    wire:click="$set('activeTab', 'session')" href="#session" role="tab">
                                    <i class="fas fa-clock mr-2"></i>Session Management
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $activeTab === '2fa' ? 'active' : '' }}"
                                    wire:click="$set('activeTab', '2fa')" href="#2fa" role="tab">
                                    <i class="fas fa-mobile-alt mr-2"></i>Two-Factor Auth
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $activeTab === 'ip-whitelist' ? 'active' : '' }}"
                                    wire:click="$set('activeTab', 'ip-whitelist')" href="#ip-whitelist" role="tab">
                                    <i class="fas fa-network-wired mr-2"></i>IP Whitelist
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $activeTab === 'audit' ? 'active' : '' }}"
                                    wire:click="$set('activeTab', 'audit')" href="#audit" role="tab">
                                    <i class="fas fa-history mr-2"></i>Audit & Logs
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $activeTab === 'users' ? 'active' : '' }}"
                                    wire:click="$set('activeTab', 'users')" href="#users" role="tab">
                                    <i class="fas fa-users-cog mr-2"></i>User 2FA Management
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="row">
            <div class="col-12">
                <!-- Password Policy Tab -->
                @if ($activeTab === 'password-policy')
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-key mr-2"></i>
                                Password Policy Settings
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                    wire:click="resetToDefaults" wire:loading.attr="disabled">
                                    <i class="fas fa-undo mr-1"></i> Reset to Defaults
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <form wire:submit.prevent="savePasswordPolicy">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Minimum Password Length</label>
                                            <input type="number"
                                                class="form-control @error('passwordMinLength') is-invalid @enderror"
                                                wire:model="passwordMinLength" min="6" max="128">
                                            @error('passwordMinLength')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                            <small class="text-muted">Recommended: 8-12 characters</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Password Expiry (Days)</label>
                                            <input type="number"
                                                class="form-control @error('passwordExpiryDays') is-invalid @enderror"
                                                wire:model="passwordExpiryDays" min="0" max="365">
                                            @error('passwordExpiryDays')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                            <small class="text-muted">0 = never expires</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Password History Count</label>
                                            <input type="number"
                                                class="form-control @error('passwordHistoryCount') is-invalid @enderror"
                                                wire:model="passwordHistoryCount" min="0" max="24">
                                            @error('passwordHistoryCount')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                            <small class="text-muted">Number of previous passwords to remember</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Max Login Attempts</label>
                                            <input type="number"
                                                class="form-control @error('maxLoginAttempts') is-invalid @enderror"
                                                wire:model="maxLoginAttempts" min="1" max="50">
                                            @error('maxLoginAttempts')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Lockout Time (Minutes)</label>
                                            <input type="number"
                                                class="form-control @error('lockoutTime') is-invalid @enderror"
                                                wire:model="lockoutTime" min="1" max="1440">
                                            @error('lockoutTime')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <h5 class="mt-3">Password Complexity Requirements</h5>
                                        <hr>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="requireMixedCase"
                                                wire:model="passwordRequireMixedCase">
                                            <label class="custom-control-label" for="requireMixedCase">
                                                Require Mixed Case (A-Z, a-z)
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="requireNumbers"
                                                wire:model="passwordRequireNumbers">
                                            <label class="custom-control-label" for="requireNumbers">
                                                Require Numbers (0-9)
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="requireSymbols"
                                                wire:model="passwordRequireSymbols">
                                            <label class="custom-control-label" for="requireSymbols">
                                                Require Symbols (!@#$%)
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row mt-4">
                                    <div class="col-sm-12">
                                        <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                                            <i class="fas fa-save mr-2"></i> Save Password Policy
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- Session Management Tab -->
                @if ($activeTab === 'session')
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-clock mr-2"></i>
                                Session Management Settings
                            </h3>
                        </div>
                        <div class="card-body">
                            <form wire:submit.prevent="saveSessionSettings">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Session Lifetime (Minutes)</label>
                                            <input type="number"
                                                class="form-control @error('sessionLifetime') is-invalid @enderror"
                                                wire:model="sessionLifetime" min="5" max="43200">
                                            @error('sessionLifetime')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                            <small class="text-muted">How long before session expires (43200 = 30
                                                days)</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Session Timeout (Minutes)</label>
                                            <input type="number"
                                                class="form-control @error('sessionTimeout') is-invalid @enderror"
                                                wire:model="sessionTimeout" min="0" max="1440">
                                            @error('sessionTimeout')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                            <small class="text-muted">Inactivity timeout (0 = disabled)</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Remember Me Lifetime (Minutes)</label>
                                            <input type="number"
                                                class="form-control @error('rememberMeLifetime') is-invalid @enderror"
                                                wire:model="rememberMeLifetime" min="0" max="43200">
                                            @error('rememberMeLifetime')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input"
                                                id="singleDeviceSession" wire:model="singleDeviceSession">
                                            <label class="custom-control-label" for="singleDeviceSession">
                                                Single Device Session (Force logout from other devices)
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row mt-4">
                                    <div class="col-sm-12">
                                        <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                                            <i class="fas fa-save mr-2"></i> Save Session Settings
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- 2FA Settings Tab -->
                @if ($activeTab === '2fa')
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-mobile-alt mr-2"></i>
                                Two-Factor Authentication Settings
                            </h3>
                        </div>
                        <div class="card-body">
                            <form wire:submit.prevent="saveTwoFactorSettings">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="custom-control custom-switch mb-3">
                                            <input type="checkbox" class="custom-control-input" id="requireTwoFactor"
                                                wire:model="requireTwoFactor">
                                            <label class="custom-control-label" for="requireTwoFactor">
                                                <strong>Require Two-Factor Authentication</strong>
                                            </label>
                                            <p class="text-muted mt-1">
                                                If enabled, users will be required to set up 2FA before accessing the
                                                system
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <h5>Available 2FA Methods</h5>
                                        <hr>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="methodApp"
                                                wire:model="twoFactorMethods.app">
                                            <label class="custom-control-label" for="methodApp">
                                                Authenticator App (Google/Microsoft)
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="methodEmail"
                                                wire:model="twoFactorMethods.email">
                                            <label class="custom-control-label" for="methodEmail">
                                                Email One-Time Code
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="methodSms"
                                                wire:model="twoFactorMethods.sms">
                                            <label class="custom-control-label" for="methodSms">
                                                SMS One-Time Code
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <h5>Require 2FA for Specific Roles</h5>
                                        <hr>
                                    </div>
                                </div>

                                <div class="row">
                                    @foreach (['super-admin', 'admin', 'mentor', 'student'] as $role)
                                        <div class="col-md-3">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input"
                                                    id="role{{ $role }}" value="{{ $role }}"
                                                    wire:model="twoFactorForRoles">
                                                <label class="custom-control-label" for="role{{ $role }}">
                                                    {{ ucfirst($role) }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="form-group row mt-4">
                                    <div class="col-sm-12">
                                        <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                                            <i class="fas fa-save mr-2"></i> Save 2FA Settings
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- IP Whitelist Tab -->
                @if ($activeTab === 'ip-whitelist')
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-network-wired mr-2"></i>
                                IP Address Whitelist
                            </h3>
                        </div>
                        <div class="card-body">
                            <form wire:submit.prevent="addIpToWhitelist">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>IP Address</label>
                                            <input type="text"
                                                class="form-control @error('newIpAddress') is-invalid @enderror"
                                                wire:model="newIpAddress" placeholder="192.168.1.1">
                                            @error('newIpAddress')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>Description</label>
                                            <input type="text"
                                                class="form-control @error('ipDescription') is-invalid @enderror"
                                                wire:model="ipDescription" placeholder="Office Network">
                                            @error('ipDescription')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <button type="submit" class="btn btn-success btn-block">
                                                <i class="fas fa-plus"></i> Add
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive mt-4">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>IP Address</th>
                                            <th>Description</th>
                                            <th>Added By</th>
                                            <th>Added At</th>
                                            <th width="100">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($ipWhitelist as $index => $item)
                                            <tr>
                                                <td><code>{{ $item['ip'] }}</code></td>
                                                <td>{{ $item['description'] ?? '-' }}</td>
                                                <td>{{ $item['added_by'] ?? 'System' }}</td>
                                                <td>{{ \Carbon\Carbon::parse($item['added_at'])->format('Y-m-d H:i') }}
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                        wire:click="removeIpFromWhitelist({{ $index }})"
                                                        wire:confirm="Are you sure you want to remove this IP?">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4">
                                                    <i class="fas fa-shield-alt fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted">No IP addresses in whitelist</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Audit & Logs Tab -->
                @if ($activeTab === 'audit')
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-history mr-2"></i>
                                Audit & Logging Settings
                            </h3>
                        </div>
                        <div class="card-body">
                            <form wire:submit.prevent="saveAuditSettings">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Log Retention (Days)</label>
                                            <input type="number"
                                                class="form-control @error('retentionDays') is-invalid @enderror"
                                                wire:model="retentionDays" min="30" max="730">
                                            @error('retentionDays')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-4">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="logAllEvents"
                                                wire:model="logAllEvents">
                                            <label class="custom-control-label" for="logAllEvents">
                                                Log All Events
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="logAuthEvents"
                                                wire:model="logAuthEvents">
                                            <label class="custom-control-label" for="logAuthEvents">
                                                Log Authentication Events
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="logModelEvents"
                                                wire:model="logModelEvents">
                                            <label class="custom-control-label" for="logModelEvents">
                                                Log Model Changes
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row mt-4">
                                    <div class="col-sm-12">
                                        <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                                            <i class="fas fa-save mr-2"></i> Save Audit Settings
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5>Recent Login Attempts</h5>
                                    <hr>

                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <input type="text" class="form-control"
                                                placeholder="Search email or IP..."
                                                wire:model.live.debounce.300ms="logSearch">
                                        </div>
                                        <div class="col-md-2">
                                            <select class="form-control" wire:model.live="logEvent">
                                                <option value="">All Events</option>
                                                <option value="success">Successful</option>
                                                <option value="failed">Failed</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="date" class="form-control" wire:model.live="logDateFrom">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="date" class="form-control" wire:model.live="logDateTo">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger btn-block"
                                                wire:click="confirmClearLogs">
                                                <i class="fas fa-trash mr-2"></i> Clear Logs
                                            </button>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Time</th>
                                                    <th>Email</th>
                                                    <th>IP Address</th>
                                                    <th>User Agent</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($loginAttempts as $attempt)
                                                    <tr>
                                                        <td>{{ $attempt->attempted_at->format('Y-m-d H:i:s') }}</td>
                                                        <td>{{ $attempt->email }}</td>
                                                        <td><code>{{ $attempt->ip_address }}</code></td>
                                                        <td>{{ \Illuminate\Support\Str::limit($attempt->user_agent, 50) }}
                                                        </td>
                                                        <td>
                                                            @if ($attempt->success)
                                                                <span class="badge badge-success">Success</span>
                                                            @else
                                                                <span class="badge badge-danger">Failed</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center py-3">
                                                            No login attempts found
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="mt-3">
                                        {{ $loginAttempts->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- User 2FA Management Tab -->
                @if ($activeTab === 'users')
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-users-cog mr-2"></i>
                                Users Without 2FA Enabled
                            </h3>
                            <div class="card-tools">
                                <div class="input-group input-group-sm" style="width: 250px;">
                                    <input type="text" class="form-control" placeholder="Search users..."
                                        wire:model.live.debounce.300ms="search">
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="fas fa-search"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Last Login</th>
                                            <th width="120">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($usersWithout2FA as $user)
                                            <tr>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>
                                                    @foreach ($user->roles as $role)
                                                        <span class="badge badge-info">{{ $role->name }}</span>
                                                    @endforeach
                                                </td>
                                                <td>{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-primary"
                                                        wire:click="viewUser2FA({{ $user->id }})">
                                                        <i class="fas fa-shield-alt"></i> Manage 2FA
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4">
                                                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                                    <p class="text-muted">All users have 2FA enabled!</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                {{ $usersWithout2FA->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- User 2FA Management Modal -->
        @if ($showUser2FAModal && $selectedUser)
            <div class="modal fade show" id="user2FAModal" tabindex="-1"
                style="display: block; background: rgba(0,0,0,0.5);" wire:ignore.self>
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h5 class="modal-title">
                                <i class="fas fa-shield-alt mr-2"></i>
                                Manage 2FA: {{ $selectedUser->name }}
                            </h5>
                            <button type="button" class="close" wire:click="$set('showUser2FAModal', false)">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center mb-3">
                                @if ($userTwoFactorStatus)
                                    <span class="badge badge-success p-2">
                                        <i class="fas fa-check-circle"></i> 2FA Enabled
                                    </span>
                                @else
                                    <span class="badge badge-danger p-2">
                                        <i class="fas fa-times-circle"></i> 2FA Disabled
                                    </span>
                                @endif
                            </div>

                            @if ($userTwoFactorStatus)
                                <div class="form-group">
                                    <label>Method</label>
                                    <input type="text" class="form-control"
                                        value="{{ ucfirst($userTwoFactorMethod) }}" readonly>
                                </div>

                                <div class="form-group">
                                    <label>Recovery Codes</label>
                                    <div class="bg-light p-3 rounded">
                                        @if (!empty($userRecoveryCodes))
                                            @foreach ($userRecoveryCodes as $code)
                                                <code class="d-block mb-1">{{ $code }}</code>
                                            @endforeach
                                        @else
                                            <p class="text-muted mb-0">No recovery codes generated</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <button type="button" class="btn btn-warning btn-block"
                                            wire:click="generateRecoveryCodes">
                                            <i class="fas fa-sync-alt mr-2"></i> Generate New Codes
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <button type="button" class="btn btn-danger btn-block"
                                            wire:click="disableUser2FA"
                                            wire:confirm="Are you sure you want to disable 2FA for this user?">
                                            <i class="fas fa-ban mr-2"></i> Disable 2FA
                                        </button>
                                    </div>
                                </div>
                            @else
                                <p class="text-center">
                                    2FA is not enabled for this user. They can enable it from their profile settings.
                                </p>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                wire:click="$set('showUser2FAModal', false)">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Confirmation Modal (for actions) -->
        <div wire:ignore.self id="confirmActionModal" class="modal fade" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Confirm Action
                        </h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="confirm-action-message">
                        <!-- Message will be injected via JavaScript -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-2"></i> Cancel
                        </button>
                        <button type="button" class="btn btn-warning" id="confirm-action-button">
                            <i class="fas fa-check mr-2"></i> Confirm
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', function() {
                // Handle confirm action
                Livewire.on('confirm-action', (data) => {
                    $('#confirm-action-message').html(data.message);
                    $('#confirm-action-button').attr('data-event', data.event);
                    $('#confirmActionModal').modal('show');
                });

                $('#confirm-action-button').on('click', function() {
                    let event = $(this).attr('data-event');
                    Livewire.dispatch(event);
                    $('#confirmActionModal').modal('hide');
                });

                // Handle notifications
                Livewire.on('notify', (data) => {
                    toastr[data.type](data.message);
                });

                // Clean up modal on close
                $('#user2FAModal').on('hidden.bs.modal', function() {
                    Livewire.dispatch('$set', {
                        showUser2FAModal: false
                    });
                });
            });
        </script>
    @endpush
</div>
