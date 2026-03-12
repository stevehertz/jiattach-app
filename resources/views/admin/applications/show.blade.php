<x-layouts.app>

    <!-- Modern Header with Gradient Background -->
    <div class="bg-gradient-primary-to-secondary py-4 mb-4">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1 class="h2 text-white mb-0">
                        <i class="fas fa-file-alt mr-2"></i>Application #{{ $application->id }}
                    </h1>
                    <nav aria-label="breadcrumb" class="mt-2">
                        <ol class="breadcrumb bg-transparent p-0 mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"
                                    class="text-white-50">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.applications.index') }}"
                                    class="text-white-50">Applications</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">#{{ $application->id }}
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="mt-2 mt-sm-0">
                    <span class="badge badge-light p-3">
                        @if (function_exists('getApplicationStatusBadge'))
                            {!! getApplicationStatusBadge($application->status, 'large') !!}
                        @else
                            <span class="badge badge-secondary">
                                {{ $application->status }}
                            </span>
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    <livewire:admin.applications.show :application="$application" />
</x-layouts.app>
