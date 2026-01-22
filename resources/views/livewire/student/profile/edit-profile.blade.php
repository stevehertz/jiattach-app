<div>
    {{-- Care about people's approval and you will be their prisoner. --}}
    <div class="container-fluid">
        <form wire:submit.prevent="save">
            <div class="row">
                <!-- Sidebar: Photo & Personal -->
                <div class="col-md-4">
                    <div class="card card-outline card-success">
                        <div class="card-body box-profile">
                            <div class="text-center mb-3">
                                @if ($profile_photo)
                                    <img class="profile-user-img img-fluid img-circle shadow"
                                        src="{{ $profile_photo->temporaryUrl() }}"
                                        style="width:120px; height:120px; object-fit:cover;">
                                @else
                                    <img class="profile-user-img img-fluid img-circle shadow"
                                        src="{{ Auth::user()->profile_photo_url }}"
                                        style="width:120px; height:120px; object-fit:cover;">
                                @endif
                                <div class="mt-2">
                                    <label class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-camera mr-1"></i> Change Photo
                                        <input type="file" wire:model="profile_photo" hidden>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="text" wire:model="phone" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>County</label>
                                <input type="text" wire:model="county" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Bio</label>
                                <textarea wire:model="bio" class="form-control" rows="3" placeholder="Tell us about yourself..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header bg-light">
                            <h3 class="card-title font-weight-bold">Documents</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>CV / Resume (PDF)</label>
                                <input type="file" wire:model="cv" class="form-control-file">
                            </div>
                            <div class="form-group">
                                <label>Academic Transcript (PDF)</label>
                                <input type="file" wire:model="transcript" class="form-control-file">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Panel: Academic & Professional -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#academic"
                                        data-toggle="tab">Academic Info</a></li>
                                <li class="nav-item"><a class="nav-link" href="#professional" data-toggle="tab">Skills &
                                        Preferences</a></li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <!-- Academic Tab -->
                                <div class="active tab-pane" id="academic">
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label>Student Reg Number</label>
                                            <input type="text" wire:model="student_reg_number" class="form-control">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Institution Name</label>
                                            <input type="text" wire:model="institution_name" class="form-control">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Institution Type</label>
                                            <select wire:model="institution_type" class="form-control">
                                                <option value="">Select Type</option>
                                                <option value="university">University</option>
                                                <option value="college">College</option>
                                                <option value="polytechnic">Polytechnic</option>
                                                <option value="technical">Technical Institute</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Course Name</label>
                                            <input type="text" wire:model="course_name" class="form-control">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Course Level</label>
                                            <select wire:model="course_level" class="form-control">
                                                <option value="certificate">Certificate</option>
                                                <option value="diploma">Diploma</option>
                                                <option value="bachelor">Bachelor</option>
                                                <option value="masters">Masters</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Current Year of Study</label>
                                            <input type="number" wire:model="year_of_study" class="form-control">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Current CGPA</label>
                                            <input type="text" wire:model="cgpa" class="form-control"
                                                placeholder="e.g. 3.50">
                                        </div>
                                    </div>
                                </div>

                                <!-- Professional Tab -->
                                <div class="tab-pane" id="professional">
                                    <div class="form-group">
                                        <label>Professional Skills</label>
                                        <div class="input-group">
                                            <input type="text" wire:model="newSkill"
                                                wire:keydown.enter.prevent="addSkill" class="form-control"
                                                placeholder="Type a skill and press Add">
                                            <div class="input-group-append">
                                                <button type="button" wire:click="addSkill"
                                                    class="btn btn-success">Add</button>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            @foreach ($skills as $skill)
                                                <span class="badge badge-info p-2 mr-1 mb-1">{{ $skill }} <i
                                                        class="fas fa-times ml-1" style="cursor:pointer"
                                                        wire:click="removeSkill('{{ $skill }}')"></i></span>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Interests</label>
                                        <div class="input-group">
                                            <input type="text" wire:model="newInterest"
                                                wire:keydown.enter.prevent="addInterest" class="form-control"
                                                placeholder="Add interests...">
                                            <div class="input-group-append">
                                                <button type="button" wire:click="addInterest"
                                                    class="btn btn-info">Add</button>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            @foreach ($interests as $interest)
                                                <span class="badge badge-secondary p-2 mr-1 mb-1">{{ $interest }}
                                                    <i class="fas fa-times ml-1" style="cursor:pointer"
                                                        wire:click="removeInterest('{{ $interest }}')"></i></span>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="form-group col-md-6">
                                            <label>Preferred Placement Location</label>
                                            <input type="text" wire:model="preferred_location"
                                                class="form-control" placeholder="e.g. Nairobi, Mombasa">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Preferred Duration (Months)</label>
                                            <input type="number" wire:model="preferred_attachment_duration"
                                                class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 border-top pt-3">
                                <button type="submit" class="btn btn-success px-5 shadow">
                                    <i class="fas fa-save mr-1"></i> Save Changes
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
