<div>
    {{-- If you look to others for fulfillment, you will never truly be fulfilled. --}}
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-envelope mr-2"></i>
                                Email Configuration
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <form wire:submit.prevent="saveEmailSettings">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mail_mailer">Mail Driver</label>
                                            <select wire:model="mail_mailer" id="mail_mailer" class="form-control">
                                                <option value="smtp">SMTP</option>
                                                <option value="mailgun">Mailgun</option>
                                                <option value="ses">Amazon SES</option>
                                                <option value="postmark">Postmark</option>
                                                <option value="sendmail">Sendmail</option>
                                                <option value="log">Log (Testing)</option>
                                            </select>
                                            @error('mail_mailer')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="mail_from_address">From Email Address</label>
                                            <input type="email" wire:model="mail_from_address" id="mail_from_address"
                                                class="form-control" placeholder="noreply@jiattach.co.ke">
                                            @error('mail_from_address')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="mail_from_name">From Name</label>
                                            <input type="text" wire:model="mail_from_name" id="mail_from_name"
                                                class="form-control" placeholder="Jiattach Platform">
                                            @error('mail_from_name')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="smtp_host">SMTP Host</label>
                                            <input type="text" wire:model="smtp_host" id="smtp_host"
                                                class="form-control" placeholder="smtp.mailtrap.io">
                                            @error('smtp_host')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="smtp_port">SMTP Port</label>
                                            <input type="number" wire:model="smtp_port" id="smtp_port"
                                                class="form-control" placeholder="2525">
                                            @error('smtp_port')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="smtp_encryption">Encryption</label>
                                            <select wire:model="smtp_encryption" id="smtp_encryption"
                                                class="form-control">
                                                <option value="tls">TLS</option>
                                                <option value="ssl">SSL</option>
                                                <option value="none">None</option>
                                            </select>
                                            @error('smtp_encryption')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="smtp_username">SMTP Username</label>
                                            <input type="text" wire:model="smtp_username" id="smtp_username"
                                                class="form-control" placeholder="username">
                                            @error('smtp_username')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <!-- Replace the password field section with this -->
                                        <div class="form-group">
                                            <label for="smtp_password">SMTP Password</label>
                                            <div class="input-group">
                                                <input type="{{ $show_password ? 'text' : 'password' }}"
                                                    wire:model="smtp_password" id="smtp_password" class="form-control"
                                                    placeholder="{{ $password_changed ? 'New password entered' : '••••••••' }}">
                                                <div class="input-group-append">
                                                    <button type="button" wire:click="togglePasswordVisibility"
                                                        class="btn btn-outline-secondary"
                                                        title="{{ $show_password ? 'Hide password' : 'Show password' }}">
                                                        <i
                                                            class="fas fa-{{ $show_password ? 'eye-slash' : 'eye' }}"></i>
                                                    </button>
                                                    <button type="button" wire:click="resetPassword"
                                                        class="btn btn-outline-danger" title="Clear password">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <small class="text-muted">
                                                @if ($password_changed)
                                                    <span class="text-warning">
                                                        <i class="fas fa-exclamation-circle"></i> Password changed -
                                                        remember to save!
                                                    </span>
                                                @else
                                                    Leave empty to keep current password
                                                @endif
                                            </small>
                                            @error('smtp_password')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save mr-2"></i>
                                            Save SMTP Settings
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card card-success card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-bell mr-2"></i>
                                Email Notifications
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" wire:model="enable_email_notifications"
                                                class="custom-control-input" id="enable_email_notifications">
                                            <label class="custom-control-label" for="enable_email_notifications">
                                                Enable Email Notifications
                                            </label>
                                        </div>
                                        <small class="text-muted">Toggle all email notifications on/off</small>
                                    </div>

                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" wire:model="send_welcome_email"
                                                class="custom-control-input" id="send_welcome_email">
                                            <label class="custom-control-label" for="send_welcome_email">
                                                Send Welcome Emails
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" wire:model="send_application_updates"
                                                class="custom-control-input" id="send_application_updates">
                                            <label class="custom-control-label" for="send_application_updates">
                                                Application Updates
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" wire:model="send_interview_notifications"
                                                class="custom-control-input" id="send_interview_notifications">
                                            <label class="custom-control-label" for="send_interview_notifications">
                                                Interview Notifications
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" wire:model="send_offer_notifications"
                                                class="custom-control-input" id="send_offer_notifications">
                                            <label class="custom-control-label" for="send_offer_notifications">
                                                Offer Notifications
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" wire:model="send_mentorship_notifications"
                                                class="custom-control-input" id="send_mentorship_notifications">
                                            <label class="custom-control-label" for="send_mentorship_notifications">
                                                Mentorship Notifications
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" wire:model="send_exchange_program_notifications"
                                                class="custom-control-input" id="send_exchange_program_notifications">
                                            <label class="custom-control-label"
                                                for="send_exchange_program_notifications">
                                                Exchange Program Notifications
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" wire:model="send_system_notifications"
                                                class="custom-control-input" id="send_system_notifications">
                                            <label class="custom-control-label" for="send_system_notifications">
                                                System Notifications
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12">
                                    <button type="button" wire:click="saveEmailSettings" class="btn btn-success">
                                        <i class="fas fa-save mr-2"></i>
                                        Save Notification Settings
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-file-alt mr-2"></i>
                                Email Templates (Subjects)
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="welcome_email_subject">Welcome Email Subject</label>
                                        <input type="text" wire:model="welcome_email_subject"
                                            id="welcome_email_subject" class="form-control"
                                            placeholder="Welcome to Jiattach - Your Career Platform">
                                        @error('welcome_email_subject')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="application_submitted_subject">Application Submitted
                                            Subject</label>
                                        <input type="text" wire:model="application_submitted_subject"
                                            id="application_submitted_subject" class="form-control"
                                            placeholder="Application Submitted Successfully">
                                        @error('application_submitted_subject')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="interview_scheduled_subject">Interview Scheduled
                                            Subject</label>
                                        <input type="text" wire:model="interview_scheduled_subject"
                                            id="interview_scheduled_subject" class="form-control"
                                            placeholder="Interview Scheduled - Jiattach">
                                        @error('interview_scheduled_subject')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="offer_sent_subject">Offer Sent Subject</label>
                                        <input type="text" wire:model="offer_sent_subject" id="offer_sent_subject"
                                            class="form-control" placeholder="Offer Letter - Jiattach">
                                        @error('offer_sent_subject')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12">
                                    <button type="button" wire:click="saveEmailSettings" class="btn btn-info">
                                        <i class="fas fa-save mr-2"></i>
                                        Save Email Templates
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card card-warning card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Test Email Configuration
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="test_email">Test Email Address</label>
                                        <input type="email" wire:model="test_email" id="test_email"
                                            class="form-control" placeholder="test@example.com">
                                        @error('test_email')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="button" wire:click="sendTestEmail" class="btn btn-warning w-100">
                                        <i class="fas fa-paper-plane mr-2"></i>
                                        Send Test Email
                                    </button>
                                </div>
                            </div>

                            @if ($test_email_sent)
                                <div class="alert alert-success mt-3">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    Test email sent successfully to {{ $test_email }}!
                                </div>
                            @endif

                            @if ($test_email_error)
                                <div class="alert alert-danger mt-3">
                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                    Failed to send test email: {{ $test_email_error }}
                                </div>
                            @endif

                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Note:</strong> Make sure to save your SMTP settings before testing.
                                The test email will use your current configuration.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

     @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                // Auto-save notification on toggle change
                const notificationSwitches = document.querySelectorAll(
                    '[wire\\:model^="send_"], [wire\\:model="enable_email_notifications"]');

                notificationSwitches.forEach(switchElement => {
                    switchElement.addEventListener('change', function() {
                        // Small delay to ensure Livewire model is updated
                        setTimeout(() => {
                            Livewire.dispatch('save-email-settings');
                        }, 300);
                    });
                });

                // Auto-save email templates on blur
                const templateInputs = document.querySelectorAll('[wire\\:model$="_subject"]');

                templateInputs.forEach(input => {
                    input.addEventListener('blur', function() {
                        Livewire.dispatch('save-email-settings');
                    });
                });
            });

            // Alert handler
            Livewire.on('alert', (event) => {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });

                Toast.fire({
                    icon: event.type,
                    title: event.message
                });
            });
        </script>
    @endpush

</div>
