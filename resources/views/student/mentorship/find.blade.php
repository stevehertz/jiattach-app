<x-layouts.student>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1 class="m-0 text-dark font-weight-bold">Find a Mentor</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Find Mentor</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Search & Filter -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form action="{{ route('student.mentorship.find') }}" method="GET">
                        <div class="row">
                            <div class="col-md-5">
                                <input type="text" name="search" class="form-control" placeholder="Search by name, company, or job title..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-4">
                                <select name="expertise" class="form-control">
                                    <option value="">All Areas of Expertise</option>
                                    @foreach($allExpertise as $exp)
                                        <option value="{{ $exp }}" {{ request('expertise') == $exp ? 'selected' : '' }}>{{ $exp }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search mr-1"></i> Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Mentor Grid -->
            <div class="row">
                @forelse($mentors as $mentor)
                    <div class="col-md-4 d-flex align-items-stretch">
                        <div class="card card-outline card-primary w-100 shadow-sm hover-shadow transition-all">
                            <div class="card-body box-profile">
                                <div class="text-center">
                                    <img class="profile-user-img img-fluid img-circle"
                                         src="{{ $mentor->user->profile_photo_url }}"
                                         alt="{{ $mentor->user->full_name }}">
                                </div>

                                <h3 class="profile-username text-center font-weight-bold mb-0">{{ $mentor->user->full_name }}</h3>
                                <p class="text-muted text-center mb-1">{{ $mentor->job_title }}</p>
                                <p class="text-muted text-center small"><i class="fas fa-building mr-1"></i> {{ $mentor->company }}</p>

                                <div class="text-center mb-3">
                                    @foreach(array_slice($mentor->areas_of_expertise ?? [], 0, 3) as $skill)
                                        <span class="badge badge-light border mr-1">{{ $skill }}</span>
                                    @endforeach
                                </div>

                                <ul class="list-group list-group-unbordered mb-3">
                                    <li class="list-group-item">
                                        <b>Experience</b> <span class="float-right">{{ $mentor->years_of_experience }} Years</span>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Rating</b> <span class="float-right text-warning">
                                            {{ $mentor->average_rating }} <i class="fas fa-star"></i>
                                        </span>
                                    </li>
                                </ul>

                                <form action="{{ route('student.mentorship.request', $mentor->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-paper-plane mr-1"></i> Request Mentorship
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No mentors found matching your criteria.</h5>
                    </div>
                @endforelse
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $mentors->links() }}
            </div>
        </div>
    </section>
</x-layouts.student>
