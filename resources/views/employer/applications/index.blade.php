<x-layouts.employer>
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Applications</h1>
                    <ol class="breadcrumb text-sm">
                        <li class="breadcrumb-item">
                            <a href="{{ route('employer.dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Applications</li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="float-right">
                        <a href="{{ route('employer.opportunities.index') }}" class="btn btn-primary">
                            <i class="fas fa-briefcase mr-1"></i> View Opportunities
                        </a>
                        <a href="{{ route('employer.dashboard') }}" class="btn btn-default ml-2">
                            <i class="fas fa-arrow-left mr-1"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @livewire('employer.applications.applications-list')
        </div>
    </section>
</x-layouts.employer>