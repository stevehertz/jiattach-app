<div>
    {{-- Nothing in the world is as soft and yielding as water. --}}
     <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">New Mentorship Program</h3>
                        </div>
                        <form wire:submit.prevent="save">
                            <div class="card-body">
                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif

                                @error('form')
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @enderror

                                <!-- Basic Information -->
                                <div class="card card-info">
                                    <div class="card-header">
                                        <h3 class="card-title">Basic Information</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="title">Mentorship Title *</label>
                                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                                           id="title" wire:model="title" 
                                                           placeholder="e.g., Software Development Mentorship Program">
                                                    @error('title') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                                    <small class="text-muted">Give this mentorship a descriptive title</small>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="description">Description *</label>
                                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                                              id="description" wire:model="description" rows="4"
                                                              placeholder="Describe the purpose, objectives, and what participants can expect from this mentorship..."></textarea>
                                                    @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Select Participants -->
                                <div class="card card-info mt-3">
                                    <div class="card-header">
                                        <h3 class="card-title">Select Participants</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <!-- Mentor Selection -->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="mentorSearch">Select Mentor *</label>
                                                    <input type="text" class="form-control" id="mentorSearch" 
                                                           wire:model.live.debounce.300ms="mentorSearch"
                                                           placeholder="Search for a mentor by name, email, or expertise...">
                                                    
                                                    @if($mentor_id)
                                                        <!-- Selected Mentor Card -->
                                                        <div class="card mt-2 border-success">
                                                            <div class="card-body">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="mr-3">
                                                                        {!! getUserAvatar($selectedMentor->user, 50) !!}
                                                                    </div>
                                                                    <div>
                                                                        <h5 class="mb-0">{{ $selectedMentor->user->full_name }}</h5>
                                                                        <p class="mb-1">
                                                                            <small class="text-muted">
                                                                                {{ $selectedMentor->job_title }} at {{ $selectedMentor->company }}
                                                                            </small>
                                                                        </p>
                                                                        <div>
                                                                            <span class="badge badge-success">
                                                                                {{ $selectedMentor->years_of_experience }} years experience
                                                                            </span>
                                                                            <span class="badge badge-info">
                                                                                {{ $selectedMentor->current_mentees }}/{{ $selectedMentor->max_mentees }} mentees
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="ml-auto">
                                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                                                wire:click="$set('mentor_id', '')">
                                                                            <i class="fas fa-times"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <!-- Mentor Search Results -->
                                                        <div class="mt-2">
                                                            @forelse($availableMentors as $mentor)
                                                                <div class="card mb-2 cursor-pointer" 
                                                                     wire:click="$set('mentor_id', {{ $mentor->id }})"
                                                                     style="cursor: pointer;">
                                                                    <div class="card-body">
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="mr-3">
                                                                                {!! getUserAvatar($mentor->user, 40) !!}
                                                                            </div>
                                                                            <div>
                                                                                <h6 class="mb-0">{{ $mentor->user->full_name }}</h6>
                                                                                <small class="text-muted">
                                                                                    {{ $mentor->job_title }} at {{ $mentor->company }}
                                                                                </small>
                                                                                <div class="mt-1">
                                                                                    @if($mentor->areas_of_expertise)
                                                                                        @foreach(array_slice($mentor->areas_of_expertise, 0, 2) as $expertise)
                                                                                            <span class="badge badge-light mr-1">{{ $expertise }}</span>
                                                                                        @endforeach
                                                                                    @endif
                                                                                    <span class="badge badge-warning">
                                                                                        {{ $mentor->years_of_experience }} yrs
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @empty
                                                                <div class="alert alert-warning">
                                                                    <i class="fas fa-exclamation-triangle"></i> No mentors found. Try a different search term.
                                                                </div>
                                                            @endforelse
                                                        </div>
                                                    @endif
                                                    @error('mentor_id') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </div>

                                            <!-- Mentee Selection -->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="menteeSearch">Select Mentee *</label>
                                                    <input type="text" class="form-control" id="menteeSearch" 
                                                           wire:model.live.debounce.300ms="menteeSearch"
                                                           placeholder="Search for a mentee by name, email, or institution...">
                                                    
                                                    @if($mentee_id)
                                                        <!-- Selected Mentee Card -->
                                                        <div class="card mt-2 border-success">
                                                            <div class="card-body">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="mr-3">
                                                                        {!! getUserAvatar($selectedMentee, 50) !!}
                                                                    </div>
                                                                    <div>
                                                                        <h5 class="mb-0">{{ $selectedMentee->full_name }}</h5>
                                                                        @if($selectedMentee->studentProfile)
                                                                            <p class="mb-1">
                                                                                <small class="text-muted">
                                                                                    {{ $selectedMentee->studentProfile->course_name }} at {{ $selectedMentee->studentProfile->institution_name }}
                                                                                </small>
                                                                            </p>
                                                                            <div>
                                                                                <span class="badge badge-info">
                                                                                    Year {{ $selectedMentee->studentProfile->year_of_study }}
                                                                                </span>
                                                                                @if($selectedMentee->studentProfile->cgpa)
                                                                                    <span class="badge badge-success">
                                                                                        CGPA: {{ $selectedMentee->studentProfile->cgpa }}
                                                                                    </span>
                                                                                @endif
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                    <div class="ml-auto">
                                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                                                wire:click="$set('mentee_id', '')">
                                                                            <i class="fas fa-times"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <!-- Mentee Search Results -->
                                                        <div class="mt-2">
                                                            @forelse($availableMentees as $mentee)
                                                                <div class="card mb-2 cursor-pointer" 
                                                                     wire:click="$set('mentee_id', {{ $mentee->id }})"
                                                                     style="cursor: pointer;">
                                                                    <div class="card-body">
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="mr-3">
                                                                                {!! getUserAvatar($mentee, 40) !!}
                                                                            </div>
                                                                            <div>
                                                                                <h6 class="mb-0">{{ $mentee->full_name }}</h6>
                                                                                @if($mentee->studentProfile)
                                                                                    <small class="text-muted">
                                                                                        {{ $mentee->studentProfile->course_name }} • {{ $mentee->studentProfile->institution_name }}
                                                                                    </small>
                                                                                    <div class="mt-1">
                                                                                        <span class="badge badge-light">
                                                                                            Year {{ $mentee->studentProfile->year_of_study }}
                                                                                        </span>
                                                                                        <span class="badge badge-info">
                                                                                            {{ $mentee->studentProfile->attachment_status_label }}
                                                                                        </span>
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @empty
                                                                <div class="alert alert-warning">
                                                                    <i class="fas fa-exclamation-triangle"></i> No mentees found. Try a different search term.
                                                                </div>
                                                            @endforelse
                                                        </div>
                                                    @endif
                                                    @error('mentee_id') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Program Details -->
                                <div class="card card-info mt-3">
                                    <div class="card-header">
                                        <h3 class="card-title">Program Details</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="duration_weeks">Program Duration *</label>
                                                    <select class="form-control @error('duration_weeks') is-invalid @enderror" 
                                                            id="duration_weeks" wire:model="duration_weeks">
                                                        @foreach($durationOptions as $value => $label)
                                                            <option value="{{ $value }}">{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('duration_weeks') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="meetings_per_month">Meeting Frequency *</label>
                                                    <select class="form-control @error('meetings_per_month') is-invalid @enderror" 
                                                            id="meetings_per_month" wire:model="meetings_per_month">
                                                        @foreach($meetingsPerMonthOptions as $value => $label)
                                                            <option value="{{ $value }}">{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('meetings_per_month') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="meeting_duration_minutes">Session Duration *</label>
                                                    <select class="form-control @error('meeting_duration_minutes') is-invalid @enderror" 
                                                            id="meeting_duration_minutes" wire:model="meeting_duration_minutes">
                                                        @foreach($durationMinutesOptions as $value => $label)
                                                            <option value="{{ $value }}">{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('meeting_duration_minutes') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="start_date">Start Date *</label>
                                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                                           id="start_date" wire:model="start_date">
                                                    @error('start_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="end_date">End Date *</label>
                                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                                           id="end_date" wire:model="end_date" readonly>
                                                    @error('end_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="meeting_preference">Meeting Preference *</label>
                                                    <select class="form-control @error('meeting_preference') is-invalid @enderror" 
                                                            id="meeting_preference" wire:model="meeting_preference">
                                                        @foreach($meetingPreferences as $value => $label)
                                                            <option value="{{ $value }}">{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('meeting_preference') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="experience_level">Experience Level *</label>
                                                    <select class="form-control @error('experience_level') is-invalid @enderror" 
                                                            id="experience_level" wire:model="experience_level">
                                                        @foreach($experienceLevels as $value => $label)
                                                            <option value="{{ $value }}">{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('experience_level') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Goals & Topics -->
                                <div class="card card-info mt-3">
                                    <div class="card-header">
                                        <h3 class="card-title">Goals & Topics</h3>
                                    </div>
                                    <div class="card-body">
                                        <!-- Goals -->
                                        <div class="form-group">
                                            <label>Program Goals</label>
                                            @foreach($goals as $index => $goal)
                                                <div class="input-group mb-2">
                                                    <input type="text" class="form-control" 
                                                           wire:model="goals.{{ $index }}"
                                                           placeholder="Enter a specific, measurable goal...">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-danger" type="button"
                                                                wire:click="removeGoal({{ $index }})">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                            <button type="button" class="btn btn-sm btn-outline-primary" wire:click="addGoal">
                                                <i class="fas fa-plus"></i> Add Goal
                                            </button>
                                            @error('goals') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        <!-- Topics -->
                                        <div class="form-group">
                                            <label>Topics to Cover</label>
                                            <div class="row">
                                                @foreach($availableTopics as $topic)
                                                    <div class="col-md-4 mb-2">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" 
                                                                   id="topic_{{ Str::slug($topic) }}"
                                                                   wire:model="topics" value="{{ $topic }}">
                                                            <label class="custom-control-label" for="topic_{{ Str::slug($topic) }}">
                                                                {{ $topic }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            @error('topics') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Financial Settings -->
                                <div class="card card-info mt-3">
                                    <div class="card-header">
                                        <h3 class="card-title">Financial Settings</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" 
                                                               id="is_paid" wire:model="is_paid">
                                                        <label class="custom-control-label" for="is_paid">
                                                            Paid Mentorship Program
                                                        </label>
                                                    </div>
                                                    <small class="text-muted">If enabled, the mentee will be charged for sessions</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                @if($is_paid)
                                                    <div class="form-group">
                                                        <label for="hourly_rate">Hourly Rate (KSh)</label>
                                                        <input type="number" class="form-control @error('hourly_rate') is-invalid @enderror" 
                                                               id="hourly_rate" wire:model="hourly_rate" 
                                                               min="0" step="100" placeholder="e.g., 2000">
                                                        @error('hourly_rate') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                                    </div>
                                                @endif
                                            </div>
                                            @if($is_paid && $hourly_rate > 0)
                                                <div class="col-md-12">
                                                    <div class="alert alert-info">
                                                        <h5><i class="fas fa-calculator"></i> Cost Estimate</h5>
                                                        <p>
                                                            Total estimated cost for the program: 
                                                            <strong>KSh {{ number_format($estimatedCost, 2) }}</strong>
                                                            <br>
                                                            <small class="text-muted">
                                                                Based on {{ $totalMeetings }} sessions × {{ $meeting_duration_minutes }} minutes × KSh {{ number_format($hourly_rate, 2) }}/hour
                                                            </small>
                                                        </p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional Information -->
                                <div class="card card-info mt-3">
                                    <div class="card-header">
                                        <h3 class="card-title">Additional Information</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="mentor_expectations">Mentor Expectations</label>
                                                    <textarea class="form-control @error('mentor_expectations') is-invalid @enderror" 
                                                              id="mentor_expectations" wire:model="mentor_expectations" rows="3"
                                                              placeholder="What is expected from the mentor..."></textarea>
                                                    @error('mentor_expectations') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="mentee_expectations">Mentee Expectations</label>
                                                    <textarea class="form-control @error('mentee_expectations') is-invalid @enderror" 
                                                              id="mentee_expectations" wire:model="mentee_expectations" rows="3"
                                                              placeholder="What is expected from the mentee..."></textarea>
                                                    @error('mentee_expectations') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="expectations">General Expectations & Guidelines</label>
                                                    <textarea class="form-control @error('expectations') is-invalid @enderror" 
                                                              id="expectations" wire:model="expectations" rows="3"
                                                              placeholder="General expectations, guidelines, and ground rules..."></textarea>
                                                    @error('expectations') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="notes">Additional Notes (Internal)</label>
                                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                                              id="notes" wire:model="notes" rows="3"
                                                              placeholder="Any additional internal notes or comments..."></textarea>
                                                    @error('notes') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" 
                                                           id="is_confidential" wire:model="is_confidential">
                                                    <label class="custom-control-label" for="is_confidential">
                                                        Confidential Mentorship
                                                    </label>
                                                    <small class="text-muted d-block">
                                                        If enabled, details of this mentorship will not be publicly visible
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Program Summary -->
                                <div class="card card-success mt-3">
                                    <div class="card-header">
                                        <h3 class="card-title">Program Summary</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <ul class="list-group">
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        Duration
                                                        <span class="badge badge-primary badge-pill">
                                                            {{ $duration_weeks }} weeks
                                                        </span>
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        Total Sessions
                                                        <span class="badge badge-info badge-pill">
                                                            {{ $totalMeetings }} sessions
                                                        </span>
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        Session Duration
                                                        <span class="badge badge-info badge-pill">
                                                            {{ $meeting_duration_minutes }} minutes
                                                        </span>
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        Meeting Type
                                                        <span class="badge badge-warning badge-pill">
                                                            {{ $meetingPreferences[$meeting_preference] }}
                                                        </span>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <ul class="list-group">
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        Program Type
                                                        <span class="badge badge-{{ $is_paid ? 'warning' : 'success' }} badge-pill">
                                                            {{ $is_paid ? 'Paid' : 'Free' }}
                                                        </span>
                                                    </li>
                                                    @if($is_paid && $hourly_rate > 0)
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            Hourly Rate
                                                            <span class="badge badge-warning badge-pill">
                                                                KSh {{ number_format($hourly_rate, 2) }}/hour
                                                            </span>
                                                        </li>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            Estimated Total
                                                            <span class="badge badge-warning badge-pill">
                                                                KSh {{ number_format($estimatedCost, 2) }}
                                                            </span>
                                                        </li>
                                                    @endif
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        Experience Level
                                                        <span class="badge badge-info badge-pill">
                                                            {{ $experienceLevels[$experience_level] }}
                                                        </span>
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        Confidential
                                                        <span class="badge badge-{{ $is_confidential ? 'danger' : 'success' }} badge-pill">
                                                            {{ $is_confidential ? 'Yes' : 'No' }}
                                                        </span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.mentorships.index') }}" class="btn btn-default">
                                        <i class="fas fa-arrow-left"></i> Back to List
                                    </a>
                                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                                        <span wire:loading.remove wire:target="save">
                                            <i class="fas fa-check"></i> Create Mentorship
                                        </span>
                                        <span wire:loading wire:target="save">
                                            <i class="fas fa-spinner fa-spin"></i> Creating...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
