<x-layouts.student>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1 class="m-0 text-dark font-weight-bold">My Mentors</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Mentors</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="row">
                @forelse($mentorships as $ms)
                    <div class="col-md-6">
                        <div class="card card-outline {{ $ms->status == 'active' ? 'card-success' : 'card-secondary' }} shadow-sm">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <span class="badge {{ $ms->status == 'active' ? 'badge-success' : 'badge-secondary' }}">
                                        {{ ucfirst($ms->status) }}
                                    </span>
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <img src="{{ $ms->mentor->user->profile_photo_url }}" class="img-circle elevation-1 mr-3" style="width: 60px; height: 60px;">
                                    <div>
                                        <h5 class="mb-0 font-weight-bold">{{ $ms->mentor->user->full_name }}</h5>
                                        <small class="text-muted">{{ $ms->mentor->full_title }}</small>
                                    </div>
                                </div>

                                <p class="text-muted small">
                                    Started: {{ $ms->created_at->format('M d, Y') }}<br>
                                    Goals: {{ implode(', ', $ms->goals ?? []) }}
                                </p>

                                <div class="d-flex gap-2">
                                    @if($ms->status == 'active')
                                        <button class="btn btn-sm btn-primary flex-fill"><i class="fas fa-comment mr-1"></i> Message</button>
                                        <button class="btn btn-sm btn-info flex-fill"><i class="fas fa-calendar-plus mr-1"></i> Schedule</button>
                                    @else
                                        <button class="btn btn-sm btn-default disabled flex-fill">Pending Approval</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="callout callout-info">
                            <h5>You don't have any mentors yet.</h5>
                            <p>Browse our directory of industry professionals to find guidance.</p>
                            <a href="{{ route('student.mentorship.find') }}" class="btn btn-primary mt-2">Find a Mentor</a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</x-layouts.student>
