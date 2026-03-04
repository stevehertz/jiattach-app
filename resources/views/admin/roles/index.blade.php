<x-layouts.app>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Roles Management</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.administrators.index') }}">
                                User Management
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Roles & Permissions</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <livewire:admin.roles.index />
</x-layouts.app>