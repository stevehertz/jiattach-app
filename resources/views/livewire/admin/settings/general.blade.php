<div>
    {{-- In work, do what you enjoy. --}}
    <!-- Main Content -->
    <div class="content">
        <div class="container-fluid">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-check"></i> Success!</h5>
                    {{ session('success') }}
                </div>
            @endif

            <div class="row">
                <div class="col-md-8">
                    <form wire:submit.prevent="save">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">General System Settings</h3>
                                <div class="card-tools">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-save mr-1"></i> Save Changes
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- Site Information -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h5 class="mb-3"><i class="fas fa-info-circle mr-2"></i> Site Information</h5>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="site_name">Site Name *</label>
                                            <input type="text"
                                                class="form-control @error('settings.site_name') is-invalid @enderror"
                                                id="site_name" wire:model="settings.site_name"
                                                placeholder="Enter site name">
                                            @error('settings.site_name')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                            <small class="form-text text-muted">The name displayed throughout the
                                                site</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="site_email">Site Email</label>
                                            <input type="email"
                                                class="form-control @error('settings.site_email') is-invalid @enderror"
                                                id="site_email" wire:model="settings.site_email"
                                                placeholder="support@jiattach.co.ke">
                                            @error('settings.site_email')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="site_phone">Site Phone</label>
                                            <input type="text"
                                                class="form-control @error('settings.site_phone') is-invalid @enderror"
                                                id="site_phone" wire:model="settings.site_phone"
                                                placeholder="+254 700 123 456">
                                            @error('settings.site_phone')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="currency">Currency *</label>
                                            <select
                                                class="form-control @error('settings.currency') is-invalid @enderror"
                                                id="currency" wire:model="settings.currency">
                                                @foreach ($currencies as $code => $name)
                                                    <option value="{{ $code }}">{{ $name }}</option>
                                                @endforeach
                                            </select>
                                            @error('settings.currency')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="site_address">Site Address</label>
                                            <textarea class="form-control @error('settings.site_address') is-invalid @enderror" id="site_address"
                                                wire:model="settings.site_address" rows="2" placeholder="Physical address of your organization"></textarea>
                                            @error('settings.site_address')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="site_description">Site Description</label>
                                            <textarea class="form-control @error('settings.site_description') is-invalid @enderror" id="site_description"
                                                wire:model="settings.site_description" rows="3" placeholder="Brief description of your platform"></textarea>
                                            @error('settings.site_description')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                            <small class="form-text text-muted">This may appear in search engine
                                                results</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Date & Time Settings -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h5 class="mb-3"><i class="fas fa-clock mr-2"></i> Date & Time Settings</h5>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="timezone">Timezone *</label>
                                            <select
                                                class="form-control @error('settings.timezone') is-invalid @enderror"
                                                id="timezone" wire:model="settings.timezone">
                                                <option value="">Select Timezone</option>
                                                @foreach ($timezones as $tz)
                                                    <option value="{{ $tz }}"
                                                        {{ $tz === $settings['timezone'] ? 'selected' : '' }}>
                                                        {{ $tz }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('settings.timezone')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="date_format">Date Format *</label>
                                            <select
                                                class="form-control @error('settings.date_format') is-invalid @enderror"
                                                id="date_format" wire:model="settings.date_format">
                                                @foreach ($dateFormats as $format => $label)
                                                    <option value="{{ $format }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            @error('settings.date_format')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="time_format">Time Format *</label>
                                            <select
                                                class="form-control @error('settings.time_format') is-invalid @enderror"
                                                id="time_format" wire:model="settings.time_format">
                                                @foreach ($timeFormats as $format => $label)
                                                    <option value="{{ $format }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            @error('settings.time_format')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Language & Localization -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h5 class="mb-3"><i class="fas fa-globe mr-2"></i> Language & Localization
                                        </h5>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="language">Default Language *</label>
                                            <select
                                                class="form-control @error('settings.language') is-invalid @enderror"
                                                id="language" wire:model="settings.language">
                                                @foreach ($languages as $code => $name)
                                                    <option value="{{ $code }}">{{ $name }}</option>
                                                @endforeach
                                            </select>
                                            @error('settings.language')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="copyright_text">Copyright Text</label>
                                            <input type="text"
                                                class="form-control @error('settings.copyright_text') is-invalid @enderror"
                                                id="copyright_text" wire:model="settings.copyright_text"
                                                placeholder="Copyright © {{ date('Y') }} Jiattach. All rights reserved.">
                                            @error('settings.copyright_text')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Media Files -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h5 class="mb-3"><i class="fas fa-images mr-2"></i> Media Files</h5>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="site_logo">Site Logo URL</label>
                                            <div class="input-group">
                                                <input type="text"
                                                    class="form-control @error('settings.site_logo') is-invalid @enderror"
                                                    id="site_logo" wire:model="settings.site_logo"
                                                    placeholder="/images/logo.png">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-outline-secondary"
                                                        onclick="document.getElementById('logoUpload').click()">
                                                        <i class="fas fa-upload"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <input type="file" id="logoUpload" class="d-none" accept="image/*">
                                            @error('settings.site_logo')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                            <small class="form-text text-muted">URL or path to your site logo</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="site_favicon">Favicon URL</label>
                                            <div class="input-group">
                                                <input type="text"
                                                    class="form-control @error('settings.site_favicon') is-invalid @enderror"
                                                    id="site_favicon" wire:model="settings.site_favicon"
                                                    placeholder="/images/favicon.ico">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-outline-secondary"
                                                        onclick="document.getElementById('faviconUpload').click()">
                                                        <i class="fas fa-upload"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <input type="file" id="faviconUpload" class="d-none"
                                                accept="image/*,.ico">
                                            @error('settings.site_favicon')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                            <small class="form-text text-muted">URL or path to your favicon (16x16 or
                                                32x32 pixels)</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Maintenance Mode -->
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card card-warning">
                                            <div class="card-header">
                                                <h3 class="card-title"><i class="fas fa-tools mr-2"></i> Maintenance
                                                    Mode</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="maintenance_mode"
                                                            wire:model="settings.maintenance_mode"
                                                            wire:change="toggleMaintenanceMode">
                                                        <label class="custom-control-label" for="maintenance_mode">
                                                            Enable Maintenance Mode
                                                        </label>
                                                    </div>
                                                    <small class="form-text text-muted">
                                                        When enabled, only administrators can access the site. Regular
                                                        users will see a maintenance message.
                                                    </small>
                                                </div>
                                                @if ($settings['maintenance_mode'] ?? false)
                                                    <div class="alert alert-warning mt-2">
                                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                                        <strong>Warning:</strong> Maintenance mode is currently enabled.
                                                        Regular users cannot access the site.
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i> Save All Changes
                                </button>
                                <button type="button" class="btn btn-default" onclick="window.location.reload()">
                                    <i class="fas fa-times mr-1"></i> Cancel
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="col-md-4">
                    <!-- Preview Card -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-eye mr-2"></i> Site Preview</h3>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                @if (!empty($settings['site_logo']))
                                    <img src="{{ asset($settings['site_logo']) }}" alt="Site Logo" class="img-fluid"
                                        style="max-height: 80px;">
                                    <p class="mt-1 small text-muted">Logo: {{ $settings['site_logo'] }}</p>
                                @else
                                    <div class="bg-light py-4 rounded">
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                        <p class="mt-2 text-muted">No logo set</p>
                                        <small class="text-muted">Enter URL or upload logo</small>
                                    </div>
                                @endif
                            </div>

                            @if (!empty($settings['site_favicon']))
                                <div class="text-center mb-3">
                                    <div class="d-inline-block p-2 border rounded">
                                        <i class="fas fa-link"></i>
                                        <p class="mt-1 small text-muted mb-0">Favicon: {{ $settings['site_favicon'] }}
                                        </p>
                                    </div>
                                </div>
                            @endif


                            <div class="callout callout-info">
                                <h5><i class="fas fa-info-circle"></i> Current Settings</h5>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Site Name:</strong></td>
                                        <td>{{ $settings['site_name'] ?? 'Not set' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Timezone:</strong></td>
                                        <td>{{ $settings['timezone'] ?? 'Not set' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Currency:</strong></td>
                                        <td>{{ $settings['currency'] ?? 'Not set' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Language:</strong></td>
                                        <td>{{ isset($settings['language']) && $settings['language'] == 'en' ? 'English' : 'Swahili' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Date Format:</strong></td>
                                        <td>{{ isset($settings['date_format']) ? date($settings['date_format']) : date('d/m/Y') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Time Format:</strong></td>
                                        <td>{{ isset($settings['time_format']) ? date($settings['time_format']) : date('H:i') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Copyright:</strong></td>
                                        <td class="small">{{ $settings['copyright_text'] ?? 'Not set' }}</td>
                                    </tr>
                                </table>
                            </div>


                            <div class="callout callout-warning">
                                <h5><i class="fas fa-exclamation-triangle"></i> Important Notes</h5>
                                <ul class="mb-0 pl-3">
                                    <li>Changes take effect immediately after saving</li>
                                    <li>Timezone affects all date/time displays</li>
                                    <li>Maintenance mode restricts site access</li>
                                    <li>Logo and favicon updates may require cache clearing</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-bolt mr-2"></i> Quick Actions</h3>
                        </div>
                        <div class="card-body">
                            <button class="btn btn-outline-primary btn-block mb-2" wire:click="testEmail">
                                <i class="fas fa-envelope mr-2"></i> Test Email Configuration
                            </button>
                            <button class="btn btn-outline-secondary btn-block mb-2"
                                onclick="window.location.reload()">
                                <i class="fas fa-sync mr-2"></i> Refresh Page
                            </button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-dark btn-block">
                                <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                // Handle file uploads for logo and favicon
                document.getElementById('logoUpload')?.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        // In a real app, you would upload this to your server
                        // For now, we'll just show a message
                        alert(
                            'File upload functionality would be implemented here. In production, this would upload to your server and return a URL.'
                        );
                    }
                });

                document.getElementById('faviconUpload')?.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        alert('File upload functionality would be implemented here.');
                    }
                });

                // Listen for settings saved event
                Livewire.on('settings-saved', () => {
                    // Refresh the page after a short delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                });

                // Listen for toast messages
                Livewire.on('show-toast', (event) => {
                    toastr[event.type](event.message, '', {
                        closeButton: true,
                        progressBar: true,
                        positionClass: 'toast-top-right',
                        timeOut: 5000
                    });
                });
            });
        </script>
    @endpush

    @push('styles')
        <style>
            .callout {
                border-left-width: 5px;
            }

            .custom-switch {
                padding-left: 2.25rem;
            }

            .custom-control-input:checked~.custom-control-label::before {
                border-color: #007bff;
                background-color: #007bff;
            }
        </style>
    @endpush
</div>
