<div>
    {{-- In work, do what you enjoy. --}}
     <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Profile Card -->
                <div class="col-md-3">
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <h3 class="profile-username text-center">{{ $organization->name }}</h3>
                            <p class="text-muted text-center">{{ $organization->industry }}</p>

                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Jobs Posted</b> <a class="float-right">{{ $organization->opportunities->count() }}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Placements</b> <a class="float-right">{{ $organization->placements->count() }}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Status</b> 
                                    <a class="float-right">
                                        {{ $organization->is_verified ? 'Verified' : 'Pending' }}
                                    </a>
                                </li>
                            </ul>
                            
                            <a href="{{ route('admin.organizations.edit', $organization->id) }}" class="btn btn-primary btn-block"><b>Edit Profile</b></a>
                        </div>
                    </div>
                </div>

                <!-- Details Tabs -->
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item"><a class="nav-link active" href="#details" data-toggle="tab">Details</a></li>
                                <li class="nav-item"><a class="nav-link" href="#jobs" data-toggle="tab">Opportunities</a></li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="active tab-pane" id="details">
                                    <strong><i class="fas fa-map-marker-alt mr-1"></i> Location</strong>
                                    <p class="text-muted">{{ $organization->county }}, {{ $organization->address }}</p>
                                    <hr>
                                    <strong><i class="fas fa-book mr-1"></i> Description</strong>
                                    <p class="text-muted">{{ $organization->description ?? 'No description provided.' }}</p>
                                    <hr>
                                    <strong><i class="fas fa-user mr-1"></i> Contact Person</strong>
                                    <p class="text-muted">
                                        {{ $organization->contact_person_name }}<br>
                                        {{ $organization->contact_person_email }}
                                    </p>
                                </div>
                                
                                <div class="tab-pane" id="jobs">
                                    <!-- List Opportunities here -->
                                    @forelse($organization->opportunities as $job)
                                        <div class="post">
                                            <div class="user-block">
                                                <span class="username"><a href="#">{{ $job->title }}</a></span>
                                                <span class="description">Posted - {{ $job->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p>{{ Str::limit($job->description, 100) }}</p>
                                        </div>
                                    @empty
                                        <p>No opportunities posted yet.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
