<div>
    {{-- Do your work, then step back. --}}
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-bell mr-2"></i>
                            Notification Settings
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3>{{ $stats['total_templates'] }}</h3>
                                        <p>Total Templates</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3>{{ $stats['active_templates'] }}</h3>
                                        <p>Active Templates</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3>{{ $stats['email_templates'] }}</h3>
                                        <p>Email Templates</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-at"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-danger">
                                    <div class="inner">
                                        <h3>{{ $stats['sms_templates'] }}</h3>
                                        <p>SMS Templates</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-mobile-alt"></i>
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
                        <ul class="nav nav-tabs" id="notification-settings-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link {{ $activeTab === 'general' ? 'active' : '' }}"
                                    wire:click="$set('activeTab', 'general')" href="#general" role="tab">
                                    <i class="fas fa-cog mr-2"></i>General Settings
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $activeTab === 'templates' ? 'active' : '' }}"
                                    wire:click="$set('activeTab', 'templates')" href="#templates" role="tab">
                                    <i class="fas fa-file-alt mr-2"></i>Email/SMS Templates
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $activeTab === 'user-preferences' ? 'active' : '' }}"
                                    wire:click="$set('activeTab', 'user-preferences')" href="#user-preferences"
                                    role="tab">
                                    <i class="fas fa-users-cog mr-2"></i>User Preferences
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
                <!-- General Settings Tab -->
                @if ($activeTab === 'general')
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-sliders-h mr-2"></i>
                                General Notification Settings
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-sm btn-outline-danger mr-2"
                                    wire:click="resetToDefaults"
                                    wire:confirm="Are you sure you want to reset all settings to defaults?">
                                    <i class="fas fa-undo mr-1"></i> Reset to Defaults
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <form wire:submit.prevent="saveGeneralSettings">
                                @foreach ($settings as $key => $setting)
                                    <div class="form-group row">
                                        <label for="{{ $key }}" class="col-sm-4 col-form-label">
                                            {{ $setting['name'] }}
                                            @if ($setting['description'])
                                                <i class="fas fa-info-circle text-info" data-toggle="tooltip"
                                                    title="{{ $setting['description'] }}"></i>
                                            @endif
                                        </label>
                                        <div class="col-sm-8">
                                            @switch($setting['type'])
                                                @case('boolean')
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="{{ $key }}"
                                                            wire:model="settings.{{ $key }}.value"
                                                            {{ $settings[$key]['value'] ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="{{ $key }}">
                                                            {{ $settings[$key]['value'] ? 'Enabled' : 'Disabled' }}
                                                        </label>
                                                    </div>
                                                @break

                                                @case('select')
                                                    <select
                                                        class="form-control @error('settings.' . $key . '.value') is-invalid @enderror"
                                                        id="{{ $key }}"
                                                        wire:model="settings.{{ $key }}.value">
                                                        @foreach ($setting['options'] as $optionValue => $optionLabel)
                                                            <option value="{{ $optionValue }}">{{ $optionLabel }}</option>
                                                        @endforeach
                                                    </select>
                                                @break

                                                @case('number')
                                                    <input type="number"
                                                        class="form-control @error('settings.' . $key . '.value') is-invalid @enderror"
                                                        id="{{ $key }}"
                                                        wire:model="settings.{{ $key }}.value" min="0">
                                                @break

                                                @default
                                                    <input type="text"
                                                        class="form-control @error('settings.' . $key . '.value') is-invalid @enderror"
                                                        id="{{ $key }}"
                                                        wire:model="settings.{{ $key }}.value">
                                            @endswitch

                                            @if ($setting['description'])
                                                <small
                                                    class="form-text text-muted">{{ $setting['description'] }}</small>
                                            @endif

                                            @error('settings.' . $key . '.value')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach

                                <div class="form-group row">
                                    <div class="col-sm-8 offset-sm-4">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save mr-2"></i> Save Settings
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- Templates Tab -->
                @if ($activeTab === 'templates')
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-file-alt mr-2"></i>
                                Notification Templates
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-sm btn-success" wire:click="createTemplate">
                                    <i class="fas fa-plus mr-1"></i> New Template
                                </button>
                                @if (!empty($selectedTemplates))
                                    <button type="button" class="btn btn-sm btn-danger ml-2"
                                        wire:click="bulkDeleteTemplates"
                                        wire:confirm="Are you sure you want to delete the selected templates?">
                                        <i class="fas fa-trash mr-1"></i> Delete Selected
                                        ({{ count($selectedTemplates) }})
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th width="40">
                                                <input type="checkbox" wire:model.live="selectAll">
                                            </th>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th>Channel</th>
                                            <th>Subject</th>
                                            <th>Status</th>
                                            <th width="150">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($templates as $template)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" wire:model.live="selectedTemplates"
                                                        value="{{ $template->id }}">
                                                </td>
                                                <td>
                                                    {{ $template->name }}
                                                    @if ($template->is_system)
                                                        <span class="badge badge-info ml-1">System</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge badge-{{ $template->type === 'placement'
                                                            ? 'primary'
                                                            : ($template->type === 'mentorship'
                                                                ? 'success'
                                                                : ($template->type === 'alert'
                                                                    ? 'warning'
                                                                    : 'secondary')) }}">
                                                        {{ ucfirst($template->type) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <i
                                                        class="fas fa-{{ $template->channel === 'email'
                                                            ? 'envelope'
                                                            : ($template->channel === 'sms'
                                                                ? 'mobile-alt'
                                                                : ($template->channel === 'push'
                                                                    ? 'bell'
                                                                    : 'comment')) }} mr-1"></i>
                                                    {{ ucfirst($template->channel) }}
                                                </td>
                                                <td>{{ Str::limit($template->subject, 30) }}</td>
                                                <td>
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="template-status-{{ $template->id }}"
                                                            wire:change="toggleTemplateStatus({{ $template->id }})"
                                                            {{ $template->is_active ? 'checked' : '' }}>
                                                        <label class="custom-control-label"
                                                            for="template-status-{{ $template->id }}">
                                                            {{ $template->is_active ? 'Active' : 'Inactive' }}
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-sm btn-info"
                                                            wire:click="testTemplate({{ $template->id }})"
                                                            title="Test Template">
                                                            <i class="fas fa-play"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-primary"
                                                            wire:click="editTemplate({{ $template->id }})"
                                                            title="Edit Template">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        @if (!$template->is_system)
                                                            <button type="button" class="btn btn-sm btn-danger"
                                                                wire:click="confirmTemplateDelete({{ $template->id }})"
                                                                title="Delete Template">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4">
                                                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted">No templates found. Create your first
                                                        template!</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                {{ $templates->links() }}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- User Preferences Tab -->
                @if ($activeTab === 'user-preferences')
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-users-cog mr-2"></i>
                                User Notification Preferences
                            </h3>
                            <div class="card-tools">
                                <div class="input-group input-group-sm" style="width: 250px;">
                                    <input type="text" class="form-control float-right"
                                        placeholder="Search users..." wire:model.live.debounce.300ms="search">
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
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>User</th>
                                            @foreach ($notificationTypes as $key => $type)
                                                <th class="text-center" title="{{ $type }}">
                                                    {{ Str::limit($type, 10) }}
                                                </th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($userPreferences as $preference)
                                            <tr>
                                                <td>
                                                    <strong>{{ $preference->user->name }}</strong><br>
                                                    <small class="text-muted">{{ $preference->user->email }}</small>
                                                </td>
                                                @foreach ($notificationTypes as $typeKey => $typeLabel)
                                                    <td class="text-center">
                                                        @php
                                                            $userPref = $preference
                                                                ->where('notification_type', $typeKey)
                                                                ->first();
                                                        @endphp
                                                        @if ($userPref && $userPref->is_enabled)
                                                            @foreach ($userPref->channels as $channel)
                                                                <i class="fas fa-{{ $channel === 'email'
                                                                    ? 'envelope'
                                                                    : ($channel === 'sms'
                                                                        ? 'mobile-alt'
                                                                        : ($channel === 'push'
                                                                            ? 'bell'
                                                                            : 'comment')) }} text-success mr-1"
                                                                    title="{{ ucfirst($channel) }}"></i>
                                                            @endforeach
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="{{ count($notificationTypes) + 1 }}"
                                                    class="text-center">
                                                    No user preferences found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                {{ $userPreferences->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Template Modal -->
        @if ($showTemplateModal)
            <div class="modal fade show" id="templateModal" tabindex="-1" role="dialog"
                style="display: block; background: rgba(0,0,0,0.5);" wire:ignore.self>
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-{{ $editingTemplate ? 'info' : 'success' }}">
                            <h5 class="modal-title">
                                <i class="fas fa-{{ $editingTemplate ? 'edit' : 'plus' }} mr-2"></i>
                                {{ $editingTemplate ? 'Edit' : 'Create' }} Notification Template
                            </h5>
                            <button type="button" class="close" wire:click="$set('showTemplateModal', false)">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form wire:submit.prevent="saveTemplate">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Template Name <span class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('templateName') is-invalid @enderror"
                                                wire:model="templateName"
                                                placeholder="e.g., Placement Match Notification">
                                            @error('templateName')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Template Type <span class="text-danger">*</span></label>
                                            <select class="form-control @error('templateType') is-invalid @enderror"
                                                wire:model="templateType">
                                                <option value="">Select Type</option>
                                                <option value="placement">Placement</option>
                                                <option value="mentorship">Mentorship</option>
                                                <option value="system">System</option>
                                                <option value="alert">Alert</option>
                                            </select>
                                            @error('templateType')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Channel <span class="text-danger">*</span></label>
                                            <select class="form-control @error('templateChannel') is-invalid @enderror"
                                                wire:model="templateChannel">
                                                <option value="">Select Channel</option>
                                                <option value="email">Email</option>
                                                <option value="sms">SMS</option>
                                                <option value="push">Push Notification</option>
                                                <option value="in_app">In-App Notification</option>
                                            </select>
                                            @error('templateChannel')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Status</label>
                                            <div class="custom-control custom-switch mt-2">
                                                <input type="checkbox" class="custom-control-input"
                                                    id="templateIsActive" wire:model="templateIsActive">
                                                <label class="custom-control-label" for="templateIsActive">
                                                    Active
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if ($templateChannel === 'email')
                                    <div class="form-group">
                                        <label>Subject <span class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('templateSubject') is-invalid @enderror"
                                            wire:model="templateSubject"
                                            placeholder="e.g., New Placement Match Found for {{ student_name }}">
                                        @error('templateSubject')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                        <small class="text-muted">
                                            Use {{ variable_name }} for dynamic content
                                        </small>
                                    </div>
                                @endif

                                <div class="form-group">
                                    <label>Body <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('templateBody') is-invalid @enderror" wire:model="templateBody" rows="6"
                                        placeholder="Dear {{ student_name }},..."></textarea>
                                    @error('templateBody')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="text-muted">
                                        Available variables will be automatically detected
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label>Description (Optional)</label>
                                    <textarea class="form-control @error('templateDescription') is-invalid @enderror" wire:model="templateDescription"
                                        rows="2" placeholder="Brief description of when this template is used"></textarea>
                                    @error('templateDescription')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                @if (!empty($templateVariables))
                                    <div class="alert alert-info">
                                        <strong><i class="fas fa-code mr-2"></i>Detected Variables:</strong>
                                        <div class="mt-2">
                                            @foreach ($templateVariables as $variable => $tag)
                                                <span class="badge badge-info mr-1 mb-1">{{ $tag }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    wire:click="$set('showTemplateModal', false)">
                                    Cancel
                                </button>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save mr-2"></i>
                                    {{ $editingTemplate ? 'Update' : 'Create' }} Template
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- Template Test Preview Modal -->
        <div wire:ignore.self id="testPreviewModal" class="modal fade" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-info">
                        <h5 class="modal-title">
                            <i class="fas fa-eye mr-2"></i>
                            Template Preview
                        </h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="test-preview-content">
                        <!-- Content will be injected via JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', function() {
                // Initialize tooltips
                $('[data-toggle="tooltip"]').tooltip();

                // Handle test preview
                Livewire.on('show-test-preview', (data) => {
                    let content = '';

                    if (data.subject) {
                        content += `<div class="mb-3">
                    <strong>Subject:</strong>
                    <div class="p-2 bg-light">${data.subject}</div>
                </div>`;
                    }

                    content += `<div>
                <strong>Body:</strong>
                <div class="p-3 bg-light border rounded">${data.body}</div>
            </div>`;

                    $('#test-preview-content').html(content);
                    $('#testPreviewModal').modal('show');
                });

                // Handle confirm delete
                Livewire.on('confirm-delete', (data) => {
                    Swal.fire({
                        title: 'Confirm Delete',
                        text: data.message || 'Are you sure you want to delete this template?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Livewire.dispatch('deleteTemplate', {
                                id: data.id
                            });
                        }
                    });
                });

                // Handle notifications
                Livewire.on('notify', (data) => {
                    toastr[data.type](data.message);
                });

                // Clean up modal on close
                $('#templateModal').on('hidden.bs.modal', function() {
                    Livewire.dispatch('$set', {
                        showTemplateModal: false
                    });
                });
            });
        </script>
    @endpush
</div>
