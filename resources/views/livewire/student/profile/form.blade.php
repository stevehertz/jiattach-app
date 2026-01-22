<div>
    {{-- Be like water. --}}

     @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

     <!-- Progress Card -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-chart-line mr-2"></i>
                Profile Progress: {{ $profileCompleteness }}%
            </h5>
        </div>
        <div class="card-body">
            @if($profile)
                <div class="row">
                    @foreach($progressBreakdown as $category)
                        <div class="col-md-6 mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <strong>{{ $category['label'] }}</strong>
                                <span class="badge badge-{{ $category['percentage'] == 100 ? 'success' : ($category['percentage'] >= 50 ? 'warning' : 'danger') }}">
                                    {{ $category['percentage'] }}%
                                </span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-{{ $category['percentage'] == 100 ? 'success' : ($category['percentage'] >= 50 ? 'warning' : 'danger') }}"
                                     role="progressbar"
                                     style="width: {{ $category['percentage'] }}%"
                                     aria-valuenow="{{ $category['percentage'] }}"
                                     aria-valuemin="0"
                                     aria-valuemax="100">
                                </div>
                            </div>
                            <small class="text-muted">
                                {{ $category['completed'] }}/{{ $category['total'] }} fields complete
                            </small>
                        </div>
                    @endforeach
                </div>
                
                @if($isPlacementReady)
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle mr-2"></i>
                        <strong>Your profile is placement ready!</strong> Our team will start searching for opportunities.
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Complete your profile to get placement.</strong> Focus on the required fields below.
                    </div>
                @endif
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Welcome!</strong> Start by creating your student profile below.
                </div>
            @endif
        </div>
    </div>

      <!-- Profile Form -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-user-graduate mr-2"></i>
                {{ $profile ? 'Edit' : 'Create' }} Student Profile
            </h5>
        </div>
        <div class="card-body">
            <form wire:submit.prevent="save">
                <!-- Academic Information -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-graduation-cap mr-2"></i>
                            Academic Information
                        </h6>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="student_reg_number" class="form-label">
                                Student Registration Number *
                                @if(in_array('student_reg_number', array_column($missingFields, 'field')))
                                    <span class="text-danger">(Required)</span>
                                @endif
                            </label>
                            <input type="text" 
                                   id="student_reg_number"
                                   class="form-control @error('student_reg_number') is-invalid @enderror"
                                   wire:model.lazy="student_reg_number"
                                   placeholder="e.g., SCII/00001/2020">
                            @error('student_reg_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="institution_name" class="form-label">
                                Institution Name *
                                @if(in_array('institution_name', array_column($missingFields, 'field')))
                                    <span class="text-danger">(Required)</span>
                                @endif
                            </label>
                            <input type="text" 
                                   id="institution_name"
                                   class="form-control @error('institution_name') is-invalid @enderror"
                                   wire:model.lazy="institution_name"
                                   placeholder="e.g., University of Nairobi">
                            @error('institution_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="institution_type" class="form-label">Institution Type *</label>
                            <select id="institution_type"
                                    class="form-control @error('institution_type') is-invalid @enderror"
                                    wire:model.lazy="institution_type">
                                <option value="">Select Type</option>
                                @foreach($institutionTypes as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('institution_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="course_name" class="form-label">
                                Course Name *
                                @if(in_array('course_name', array_column($missingFields, 'field')))
                                    <span class="text-danger">(Required)</span>
                                @endif
                            </label>
                            <input type="text" 
                                   id="course_name"
                                   class="form-control @error('course_name') is-invalid @enderror"
                                   wire:model.lazy="course_name"
                                   placeholder="e.g., Computer Science">
                            @error('course_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="course_level" class="form-label">Course Level *</label>
                            <select id="course_level"
                                    class="form-control @error('course_level') is-invalid @enderror"
                                    wire:model.lazy="course_level">
                                <option value="">Select Level</option>
                                @foreach($courseLevels as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('course_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="year_of_study" class="form-label">
                                Year of Study *
                                @if(in_array('year_of_study', array_column($missingFields, 'field')))
                                    <span class="text-danger">(Required)</span>
                                @endif
                            </label>
                            <select id="year_of_study"
                                    class="form-control @error('year_of_study') is-invalid @enderror"
                                    wire:model.lazy="year_of_study">
                                <option value="">Select Year</option>
                                @for($i = 1; $i <= 6; $i++)
                                    <option value="{{ $i }}">Year {{ $i }}</option>
                                @endfor
                            </select>
                            @error('year_of_study')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="expected_graduation_year" class="form-label">Expected Graduation Year *</label>
                            <input type="number" 
                                   id="expected_graduation_year"
                                   class="form-control @error('expected_graduation_year') is-invalid @enderror"
                                   wire:model.lazy="expected_graduation_year"
                                   min="{{ date('Y') }}"
                                   max="{{ date('Y') + 10 }}">
                            @error('expected_graduation_year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cgpa" class="form-label">CGPA (on 4.0 scale)</label>
                            <input type="number" 
                                   id="cgpa"
                                   class="form-control @error('cgpa') is-invalid @enderror"
                                   wire:model.lazy="cgpa"
                                   step="0.01"
                                   min="0"
                                   max="4.0"
                                   placeholder="e.g., 3.5">
                            @error('cgpa')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Leave empty if not applicable</small>
                        </div>
                    </div>
                </div>

                <!-- Skills & Interests -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-tools mr-2"></i>
                            Skills & Interests
                        </h6>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="skills" class="form-label">
                                Skills *
                                @if(in_array('skills', array_column($missingFields, 'field')))
                                    <span class="text-danger">(Required - Add at least one)</span>
                                @endif
                            </label>
                            <div class="input-group mb-2">
                                <input type="text" 
                                       id="skillInput"
                                       class="form-control"
                                       wire:model="skillInput"
                                       placeholder="e.g., Python, Laravel, Communication"
                                       wire:keydown.enter.prevent="addSkill">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-primary" type="button" wire:click="addSkill">
                                        <i class="fas fa-plus"></i> Add
                                    </button>
                                </div>
                            </div>
                            <div class="skills-container mt-2">
                                @foreach($skills as $index => $skill)
                                    <span class="badge badge-primary mr-2 mb-2 p-2">
                                        {{ $skill }}
                                        <button type="button" 
                                                class="btn btn-sm btn-link text-white p-0 ml-2"
                                                wire:click="removeSkill({{ $index }})">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </span>
                                @endforeach
                                @if(count($skills) === 0)
                                    <div class="text-muted">No skills added yet</div>
                                @endif
                            </div>
                            @error('skills')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="interests" class="form-label">Interests (Optional)</label>
                            <div class="input-group mb-2">
                                <input type="text" 
                                       class="form-control"
                                       wire:model="interestInput"
                                       placeholder="e.g., Web Development, Data Science"
                                       wire:keydown.enter.prevent="addInterest">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" wire:click="addInterest">
                                        <i class="fas fa-plus"></i> Add
                                    </button>
                                </div>
                            </div>
                            <div class="interests-container mt-2">
                                @foreach($interests as $index => $interest)
                                    <span class="badge badge-secondary mr-2 mb-2 p-2">
                                        {{ $interest }}
                                        <button type="button" 
                                                class="btn btn-sm btn-link text-white p-0 ml-2"
                                                wire:click="removeInterest({{ $index }})">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Placement Preferences -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            Placement Preferences
                        </h6>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="preferred_location" class="form-label">Preferred Location *</label>
                            <input type="text" 
                                   id="preferred_location"
                                   class="form-control @error('preferred_location') is-invalid @enderror"
                                   wire:model.lazy="preferred_location"
                                   placeholder="e.g., Nairobi, Mombasa, Remote">
                            @error('preferred_location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="preferred_attachment_duration" class="form-label">Preferred Duration (Months) *</label>
                            <select id="preferred_attachment_duration"
                                    class="form-control @error('preferred_attachment_duration') is-invalid @enderror"
                                    wire:model.lazy="preferred_attachment_duration">
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}">{{ $i }} {{ str_plural('month', $i) }}</option>
                                @endfor
                            </select>
                            @error('preferred_attachment_duration')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Document Uploads -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-file-upload mr-2"></i>
                            Documents
                        </h6>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cv_file" class="form-label">
                                CV/Resume *
                                @if(in_array('cv_url', array_column($missingFields, 'field')))
                                    <span class="text-danger">(Required)</span>
                                @endif
                            </label>
                            @if($cv_url)
                                <div class="mb-2">
                                    <div class="alert alert-success p-2">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        CV uploaded
                                        <a href="{{ $cv_url }}" target="_blank" class="btn btn-sm btn-outline-primary ml-2">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger"
                                                wire:click="deleteDocument('cv')"
                                                wire:confirm="Are you sure you want to delete your CV?">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            @endif
                            <div class="custom-file">
                                <input type="file" 
                                       id="cv_file"
                                       class="custom-file-input @error('cv_file') is-invalid @enderror"
                                       wire:model="cv_file"
                                       accept=".pdf,.doc,.docx">
                                <label class="custom-file-label" for="cv_file">
                                    {{ $cv_file ? $cv_file->getClientOriginalName() : 'Choose file (PDF, DOC, DOCX)' }}
                                </label>
                            </div>
                            @error('cv_file')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Maximum file size: 2MB. Accepts PDF, DOC, DOCX</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="transcript_file" class="form-label">Academic Transcript (Optional)</label>
                            @if($transcript_url)
                                <div class="mb-2">
                                    <div class="alert alert-success p-2">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        Transcript uploaded
                                        <a href="{{ $transcript_url }}" target="_blank" class="btn btn-sm btn-outline-primary ml-2">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger"
                                                wire:click="deleteDocument('transcript')"
                                                wire:confirm="Are you sure you want to delete your transcript?">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            @endif
                            <div class="custom-file">
                                <input type="file" 
                                       id="transcript_file"
                                       class="custom-file-input @error('transcript_file') is-invalid @enderror"
                                       wire:model="transcript_file"
                                       accept=".pdf,.doc,.docx">
                                <label class="custom-file-label" for="transcript_file">
                                    {{ $transcript_file ? $transcript_file->getClientOriginalName() : 'Choose file (PDF, DOC, DOCX)' }}
                                </label>
                            </div>
                            @error('transcript_file')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Maximum file size: 2MB. Accepts PDF, DOC, DOCX</small>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-lg" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="save">
                                    <i class="fas fa-save mr-2"></i>
                                    {{ $profile ? 'Update Profile' : 'Create Profile' }}
                                </span>
                                <span wire:loading wire:target="save">
                                    <i class="fas fa-spinner fa-spin mr-2"></i>
                                    Saving...
                                </span>
                            </button>
                            
                            @if($profile)
                                <a href="{{ route('student.dashboard') }}" class="btn btn-secondary btn-lg ml-2">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    Back to Dashboard
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
