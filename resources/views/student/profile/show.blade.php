<x-layouts.student>
    <div class="content-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between">
                <h1 class="m-0 font-weight-bold text-dark">My Profile</h1>
                <a href="{{ route('student.profile.edit') }}" class="btn btn-success shadow">
                    <i class="fas fa-user-edit mr-1"></i> Edit Profile
                </a>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <!-- User Summary Card -->
                    <div class="card card-success card-outline shadow-sm">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <img class="profile-user-img img-fluid img-circle shadow-sm"
                                    src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}"
                                    alt="{{ Auth::user()->full_name }}"
                                    style="width:100px; height:100px; object-fit:cover;">
                            </div>
                            <h3 class="profile-username text-center font-weight-bold">
                                {{ Auth::user()->full_name }}
                            </h3>
                            <p class="text-muted text-center">
                                {{ $profile->academic_stage }}
                            </p>

                            <div class="mb-3 mt-4">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="small font-weight-bold">Profile Completeness</span>
                                    <span class="small font-weight-bold">{{ $profile->profile_completeness }}%</span>
                                </div>
                                <div class="progress progress-sm rounded shadow-sm">
                                    <div class="progress-bar bg-success"
                                        style="width: {{ $profile->profile_completeness }}%"></div>
                                </div>
                            </div>

                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <i class="fas fa-university mr-2 text-success"></i> <b>Institution</b> <span
                                        class="float-right small">{{ Str::limit($profile->institution_name, 20) }}</span>
                                </li>
                                <li class="list-group-item">
                                    <i class="fas fa-graduation-cap mr-2 text-success"></i> <b>Year</b> <span
                                        class="float-right">{{ $profile->academic_year }}</span>
                                </li>
                                <li class="list-group-item">
                                    <i class="fas fa-envelope mr-2 text-success"></i> <b>Email</b> <span
                                        class="float-right small">{{ Auth::user()->email }}</span>
                                </li>
                                <!-- Added Disability Status Here -->
                                <li class="list-group-item">
                                    <i class="fas fa-universal-access mr-2 text-success"></i> <b>Disability</b>
                                    <span
                                        class="float-right small badge {{ Auth::user()->disability_status === 'none' ? 'badge-light' : 'badge-warning' }}">
                                        {{ ucfirst(str_replace('_', ' ', Auth::user()->disability_status ?? 'None')) }}
                                    </span>
                                </li>
                            </ul>

                            <!-- Show Details if Disability exists -->
                            @if (Auth::user()->disability_status && !in_array(Auth::user()->disability_status, ['none', 'prefer_not_to_say']))
                                <div class="alert alert-light border small">
                                    <strong><i class="fas fa-info-circle text-info"></i> Accommodations:</strong><br>
                                    {{ Auth::user()->disability_details }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Missing Fields Callout -->
                    @if (count($missingFields) > 0)
                        <div class="card bg-light shadow-sm">
                            <div class="card-body p-3">
                                <h6 class="font-weight-bold text-warning"><i
                                        class="fas fa-exclamation-triangle mr-2"></i> Action Required</h6>
                                <p class="small mb-2">Complete these to get better placements:</p>
                                <ul class="list-unstyled mb-0">
                                    @foreach ($missingFields as $missing)
                                        <li class="small mb-1"><i class="fas fa-chevron-right text-xs mr-1"></i>
                                            {{ $missing['label'] }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-md-8">
                    <!-- Detailed Breakdown (No changes needed here based on request, kept context) -->
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white">
                            <h3 class="card-title font-weight-bold">Academic Details</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6 mb-3">
                                    <label class="text-muted small d-block">Course Name</label>
                                    <span class="font-weight-bold">{{ $profile->full_course_name }}</span>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <label class="text-muted small d-block">Reg Number</label>
                                    <span class="font-weight-bold">{{ $profile->student_reg_number }}</span>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <label class="text-muted small d-block">Academic Progress</label>
                                    <div class="progress progress-xs mt-1">
                                        <div class="progress-bar bg-info"
                                            style="width: {{ $profile->academic_progress }}%"></div>
                                    </div>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <label class="text-muted small d-block">Graduation Year</label>
                                    <span class="font-weight-bold">{{ $profile->expected_graduation_year }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-white">
                                    <h3 class="card-title font-weight-bold">Skills</h3>
                                </div>
                                <div class="card-body">
                                    @forelse($profile->skills ?? [] as $skill)
                                        <span class="badge badge-info p-2 mb-1">{{ $skill }}</span>
                                    @empty
                                        <span class="text-muted small">No skills listed</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-white">
                                    <h3 class="card-title font-weight-bold">Interests</h3>
                                </div>
                                <div class="card-body">
                                    @forelse($profile->interests ?? [] as $interest)
                                        <span class="badge badge-secondary p-2 mb-1">{{ $interest }}</span>
                                    @empty
                                        <span class="text-muted small">No interests listed</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white">
                            <h3 class="card-title font-weight-bold">Uploaded Documents</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-4">
                                    @if ($profile->cv_url)
                                        <a href="{{ $profile->cv_url }}" target="_blank"
                                            class="btn btn-outline-danger btn-block h-100 d-flex align-items-center justify-content-center">
                                            <div>
                                                <i class="fas fa-file-pdf fa-lg mb-1 d-block"></i>
                                                View CV
                                            </div>
                                        </a>
                                    @else
                                        <div
                                            class="alert alert-light border text-center text-muted small h-100 d-flex align-items-center justify-content-center m-0">
                                            <div><i class="fas fa-times-circle mr-1"></i> No CV</div>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-sm-4">
                                    @if ($profile->transcript_url)
                                        <a href="{{ $profile->transcript_url }}" target="_blank"
                                            class="btn btn-outline-primary btn-block h-100 d-flex align-items-center justify-content-center">
                                            <div>
                                                <i class="fas fa-file-invoice fa-lg mb-1 d-block"></i>
                                                View Transcript
                                            </div>
                                        </a>
                                    @else
                                        <div
                                            class="alert alert-light border text-center text-muted small h-100 d-flex align-items-center justify-content-center m-0">
                                            <div><i class="fas fa-times-circle mr-1"></i> No Transcript</div>
                                        </div>
                                    @endif
                                </div>
                                <!-- School Letter -->
                                <div class="col-sm-4 mb-2">
                                    @if ($profile->school_letter_url)
                                        <a href="{{ $profile->school_letter_url }}" target="_blank"
                                            class="btn btn-outline-info btn-block h-100 d-flex align-items-center justify-content-center">
                                            <div>
                                                <i class="fas fa-envelope-open-text fa-lg mb-1 d-block"></i>
                                                View Letter
                                            </div>
                                        </a>
                                    @else
                                        <div
                                            class="alert alert-light border text-center text-muted small h-100 d-flex align-items-center justify-content-center m-0">
                                            <div><i class="fas fa-times-circle mr-1"></i> No Letter</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.student>
