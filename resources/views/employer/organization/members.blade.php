<x-layouts.employer>
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Team Members</h1>
                    <ol class="breadcrumb text-sm">
                        <li class="breadcrumb-item">
                            <a href="{{ route('employer.dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('employer.organization.profile') }}">Organization</a>
                        </li>
                        <li class="breadcrumb-item active">Team Members</li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="float-right">
                        <a href="{{ route('employer.organization.profile') }}" class="btn btn-info">
                            <i class="fas fa-building mr-1"></i> Organization Profile
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
            @livewire('employer.organization.team-members')
        </div>
    </section>
</x-layouts.employer>