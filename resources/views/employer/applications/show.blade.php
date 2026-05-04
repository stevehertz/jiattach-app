<x-layouts.employer>
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Application Details</h1>
                    <ol class="breadcrumb text-sm">
                        <li class="breadcrumb-item">
                            <a href="{{ route('employer.dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('employer.applications.index') }}">Applications</a>
                        </li>
                        <li class="breadcrumb-item active">#{{ $applicationId }}</li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="float-right">
                        <a href="{{ route('employer.applications.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left mr-1"></i> Back to Applications
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @livewire('employer.applications.view-application', ['applicationId' => $applicationId])
        </div>
    </section>
</x-layouts.employer>