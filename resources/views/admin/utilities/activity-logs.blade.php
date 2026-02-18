<x-layouts.app>
    {{-- The Master doesn't talk, he acts. --}}
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Activity Logs</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Activity Logs</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <livewire:admin.activity-logs.index />
    
</x-layouts.app>