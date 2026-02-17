<x-layouts.app>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-shield-alt mr-2"></i> Security Settings
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.settings.general') }}">Settings</a></li>
                        <li class="breadcrumb-item active">Security</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <livewire:admin.settings.security-settings />
</x-layouts.app>
