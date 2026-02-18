<x-layouts.app :title="'Edit: ' . $opportunity->title">
    <!-- Page Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Opportunity</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.opportunities.index') }}">Opportunities</a></li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.opportunities.show', $opportunity) }}">{{ Str::limit($opportunity->title, 30) }}</a>
                        </li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <livewire:admin.opportunities.edit :opportunity="$opportunity" />
</x-layouts.app>