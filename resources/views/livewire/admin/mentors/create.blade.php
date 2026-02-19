<div>
    {{-- If you look to others for fulfillment, you will never truly be fulfilled. --}}
    <div class="content">
        <div class="container-fluid">
            <form wire:submit.prevent="saveMentor">
                <div class="row">
                    <div class="col-md-6">
                        <!-- User Information Card -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Personal Information</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="first_name">First Name <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   wire:model="first_name"
                                                   id="first_name"
                                                   class="form-control @error('first_name') is-invalid @enderror"
                                                   placeholder="Enter first name">
                                            @error('first_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   wire:model="last_name"
                                                   id="last_name"
                                                   class="form-control @error('last_name') is-invalid @enderror"
                                                   placeholder="Enter last name">
                                            @error('last_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="email">Email Address <span class="text-danger">*</span></label>
                                    <input type="email"
                                           wire:model="email"
                                           id="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           placeholder="Enter email address">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="phone">Phone Number <span class="text-danger">*</span></label>
                                    <input type="text"
                                           wire:model="phone"
                                           id="phone"
                                           class="form-control @error('phone') is-invalid @enderror"
                                           placeholder="Enter phone number">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">Password <span class="text-danger">*</span></label>
                                            <input type="password"
                                                   wire:model="password"
                                                   id="password"
                                                   class="form-control @error('password') is-invalid @enderror"
                                                   placeholder="Enter password">
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
                                            <input type="password"
                                                   wire:model="password_confirmation"
                                                   id="password_confirmation"
                                                   class="form-control"
                                                   placeholder="Confirm password">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Professional Information Card -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Professional Information</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="job_title">Job Title <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   wire:model="job_title"
                                                   id="job_title"
                                                   class="form-control @error('job_title') is-invalid @enderror"
                                                   placeholder="e.g., Senior Developer, Marketing Manager">
                                            @error('job_title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="company">Company <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   wire:model="company"
                                                   id="company"
                                                   class="form-control @error('company') is-invalid @enderror"
                                                   placeholder="e.g., Google, Safaricom">
                                            @error('company')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="years_of_experience">Years of Experience <span class="text-danger">*</span></label>
                                    <input type="number"
                                           wire:model="years_of_experience"
                                           id="years_of_experience"
                                           class="form-control @error('years_of_experience') is-invalid @enderror"
                                           min="0"
                                           max="50"
                                           placeholder="e.g., 5">
                                    @error('years_of_experience')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Areas of Expertise <span class="text-danger">*</span></label>
                                    <div class="mb-2">
                                        <select class="form-control" wire:model="selectedExpertise" wire:change="addExpertise($event.target.value)">
                                            <option value="">Select expertise to add</option>
                                            @foreach($expertiseOptions as $expertise)
                                                <option value="{{ $expertise }}">{{ $expertise }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="selected-expertise">
                                        @foreach($areas_of_expertise as $expertise)
                                            <span class="badge badge-info mr-1 mb-1">
                                                {{ $expertise }}
                                                <button type="button"
                                                        class="close ml-1"
                                                        style="font-size: 0.75rem;"
                                                        wire:click="removeExpertise('{{ $expertise }}')">
                                                    ×
                                                </button>
                                            </span>
                                        @endforeach
                                    </div>
                                    @error('areas_of_expertise')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Industries <span class="text-danger">*</span></label>
                                    <div class="mb-2">
                                        <select class="form-control" wire:model="selectedIndustry" wire:change="addIndustry($event.target.value)">
                                            <option value="">Select industry to add</option>
                                            @foreach($industryOptions as $industry)
                                                <option value="{{ $industry }}">{{ $industry }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="selected-industries">
                                        @foreach($industries as $industry)
                                            <span class="badge badge-secondary mr-1 mb-1">
                                                {{ $industry }}
                                                <button type="button"
                                                        class="close ml-1"
                                                        style="font-size: 0.75rem;"
                                                        wire:click="removeIndustry('{{ $industry }}')">
                                                    ×
                                                </button>
                                            </span>
                                        @endforeach
                                    </div>
                                    @error('industries')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <!-- Mentoring Details Card -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Mentoring Details</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="max_mentees">Max Mentees <span class="text-danger">*</span></label>
                                            <input type="number"
                                                   wire:model="max_mentees"
                                                   id="max_mentees"
                                                   class="form-control @error('max_mentees') is-invalid @enderror"
                                                   min="1"
                                                   max="20"
                                                   placeholder="e.g., 5">
                                            @error('max_mentees')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="availability">Availability <span class="text-danger">*</span></label>
                                            <select wire:model="availability"
                                                    id="availability"
                                                    class="form-control @error('availability') is-invalid @enderror">
                                                @foreach($availabilityOptions as $value => $label)
                                                    <option value="{{ $value }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            @error('availability')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mentoring_focus">Mentoring Focus <span class="text-danger">*</span></label>
                                            <select wire:model="mentoring_focus"
                                                    id="mentoring_focus"
                                                    class="form-control @error('mentoring_focus') is-invalid @enderror">
                                                @foreach($focusOptions as $value => $label)
                                                    <option value="{{ $value }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            @error('mentoring_focus')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="meeting_preference">Meeting Preference <span class="text-danger">*</span></label>
                                            <select wire:model="meeting_preference"
                                                    id="meeting_preference"
                                                    class="form-control @error('meeting_preference') is-invalid @enderror">
                                                @foreach($meetingOptions as $value => $label)
                                                    <option value="{{ $value }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            @error('meeting_preference')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="session_duration_minutes">Session Duration <span class="text-danger">*</span></label>
                                    <select wire:model="session_duration_minutes"
                                            id="session_duration_minutes"
                                            class="form-control @error('session_duration_minutes') is-invalid @enderror">
                                        @foreach($durationOptions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('session_duration_minutes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="hourly_rate">Hourly Rate (KES)</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">KSh</span>
                                        </div>
                                        <input type="number"
                                               wire:model="hourly_rate"
                                               id="hourly_rate"
                                               class="form-control @error('hourly_rate') is-invalid @enderror"
                                               placeholder="e.g., 2000"
                                               step="0.01"
                                               min="0">
                                    </div>
                                    @error('hourly_rate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox"
                                               wire:model="offers_free_sessions"
                                               id="offers_free_sessions"
                                               class="custom-control-input"
                                               value="1">
                                        <label class="custom-control-label" for="offers_free_sessions">
                                            Offers free sessions
                                        </label>
                                    </div>

                                    @if($offers_free_sessions)
                                        <div class="mt-2">
                                            <label for="free_sessions_per_month">Free Sessions Per Month</label>
                                            <input type="number"
                                                   wire:model="free_sessions_per_month"
                                                   id="free_sessions_per_month"
                                                   class="form-control"
                                                   min="1"
                                                   max="10"
                                                   placeholder="e.g., 2">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Bio & Philosophy Card -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Bio & Philosophy</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="bio">Bio <span class="text-danger">*</span></label>
                                    <textarea wire:model="bio"
                                              id="bio"
                                              class="form-control @error('bio') is-invalid @enderror"
                                              rows="4"
                                              placeholder="Tell us about your background, experience, and why you want to mentor..."></textarea>
                                    <small class="form-text text-muted">Minimum 100 characters. This will be displayed on your public profile.</small>
                                    @error('bio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="mentoring_philosophy">Mentoring Philosophy</label>
                                    <textarea wire:model="mentoring_philosophy"
                                              id="mentoring_philosophy"
                                              class="form-control @error('mentoring_philosophy') is-invalid @enderror"
                                              rows="3"
                                              placeholder="Share your approach to mentoring..."></textarea>
                                    @error('mentoring_philosophy')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information Row -->
                <div class="row">
                    <div class="col-md-6">
                        <!-- Languages & Education Card -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Languages & Education</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Languages</label>
                                    <div class="mb-2">
                                        <select class="form-control" wire:model="selectedLanguage" wire:change="addLanguage($event.target.value)">
                                            <option value="">Select language to add</option>
                                            @foreach($languageOptions as $language)
                                                <option value="{{ $language }}">{{ $language }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="selected-languages">
                                        @foreach($languages as $language)
                                            <span class="badge badge-light mr-1 mb-1">
                                                {{ $language }}
                                                <button type="button"
                                                        class="close ml-1"
                                                        style="font-size: 0.75rem;"
                                                        wire:click="removeLanguage('{{ $language }}')">
                                                    ×
                                                </button>
                                            </span>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Education Background</label>
                                    <button type="button" class="btn btn-sm btn-outline-primary mb-2" wire:click="addEducation">
                                        <i class="fas fa-plus mr-1"></i> Add Education
                                    </button>

                                    @foreach($education_background as $index => $education)
                                        <div class="border rounded p-3 mb-2">
                                            <div class="d-flex justify-content-between mb-2">
                                                <h6 class="mb-0">Education #{{ $index + 1 }}</h6>
                                                <button type="button" class="btn btn-sm btn-danger" wire:click="removeEducation({{ $index }})">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <input type="text"
                                                           wire:model="education_background.{{ $index }}.degree"
                                                           class="form-control form-control-sm mb-2"
                                                           placeholder="Degree (e.g., BSc, MBA)">
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text"
                                                           wire:model="education_background.{{ $index }}.field"
                                                           class="form-control form-control-sm mb-2"
                                                           placeholder="Field of Study">
                                                </div>
                                            </div>
                                            <input type="text"
                                                   wire:model="education_background.{{ $index }}.institution"
                                                   class="form-control form-control-sm mb-2"
                                                   placeholder="Institution Name">
                                            <input type="text"
                                                   wire:model="education_background.{{ $index }}.year"
                                                   class="form-control form-control-sm"
                                                   placeholder="Year of Graduation">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <!-- Social Media & Verification Card -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Social Media & Verification</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="linkedin_profile">LinkedIn Profile</label>
                                    <input type="url"
                                           wire:model="linkedin_profile"
                                           id="linkedin_profile"
                                           class="form-control @error('linkedin_profile') is-invalid @enderror"
                                           placeholder="https://linkedin.com/in/username">
                                    @error('linkedin_profile')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="twitter_profile">Twitter Profile</label>
                                    <input type="text"
                                           wire:model="twitter_profile"
                                           id="twitter_profile"
                                           class="form-control @error('twitter_profile') is-invalid @enderror"
                                           placeholder="@username">
                                    @error('twitter_profile')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="website">Personal Website/Blog</label>
                                    <input type="url"
                                           wire:model="website"
                                           id="website"
                                           class="form-control @error('website') is-invalid @enderror"
                                           placeholder="https://example.com">
                                    @error('website')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <hr>

                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox"
                                               wire:model="is_verified"
                                               id="is_verified"
                                               class="custom-control-input"
                                               value="1">
                                        <label class="custom-control-label" for="is_verified">
                                            Verify this mentor immediately
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Verified mentors can be featured and have higher visibility.
                                    </small>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox"
                                               wire:model="is_featured"
                                               id="is_featured"
                                               class="custom-control-input"
                                               value="1">
                                        <label class="custom-control-label" for="is_featured">
                                            Feature this mentor
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Featured mentors appear prominently on the platform.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.mentors.index') }}"
                                       class="btn btn-default">
                                        <i class="fas fa-arrow-left mr-1"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-1"></i> Create Mentor
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

     @push('styles')
        <style>
            .selected-expertise .badge,
            .selected-industries .badge,
            .selected-languages .badge {
                display: inline-flex;
                align-items: center;
                padding: 0.25rem 0.5rem;
                margin: 0.125rem;
            }
            .selected-expertise .badge .close,
            .selected-industries .badge .close,
            .selected-languages .badge .close {
                margin-left: 0.25rem;
                opacity: 0.7;
            }
            .selected-expertise .badge .close:hover,
            .selected-industries .badge .close:hover,
            .selected-languages .badge .close:hover {
                opacity: 1;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                // Toast notification handler
                Livewire.on('show-toast', (event) => {
                    toastr[event.type](event.message, '', {
                        closeButton: true,
                        progressBar: true,
                        positionClass: 'toast-top-right',
                        timeOut: 5000
                    });
                });
            });
        </script>
    @endpush


</div>
