<div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white border-0">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle mr-2"></i>Complete Interview
                </h5>
                <button type="button" class="close text-white" wire:click="$set('showCompleteInterviewModal', false)">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                <!-- Interview Outcome -->
                <div class="form-group">
                    <label class="font-weight-bold">Interview Outcome <span class="text-danger">*</span></label>
                    <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                        <label class="btn btn-outline-success {{ $interviewOutcome === 'successful' ? 'active' : '' }}">
                            <input type="radio" wire:model="interviewOutcome" value="successful" autocomplete="off">
                            <i class="fas fa-check-circle mr-1"></i> Successful
                        </label>
                        <label
                            class="btn btn-outline-danger {{ $interviewOutcome === 'unsuccessful' ? 'active' : '' }}">
                            <input type="radio" wire:model="interviewOutcome" value="unsuccessful" autocomplete="off">
                            <i class="fas fa-times-circle mr-1"></i> Unsuccessful
                        </label>
                    </div>
                </div>

                <!-- Rating -->
                <div class="form-group">
                    <label class="font-weight-bold">Overall Rating <span class="text-danger">*</span></label>
                    <div class="rating-input">
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star fa-2x {{ $i <= $interviewRating ? 'text-warning' : 'text-muted' }}"
                                style="cursor: pointer;" wire:click="$set('interviewRating', {{ $i }})"></i>
                        @endfor
                        <span class="ml-2 text-muted">({{ $interviewRating }}/5)</span>
                    </div>
                </div>

                <!-- Skills Assessment -->
                <div class="form-group">
                    <label class="font-weight-bold">Skills Assessment</label>
                    <div class="border rounded p-3 bg-light">
                        @foreach ($commonSkills as $skill)
                            <div class="row mb-2 align-items-center">
                                <div class="col-md-5">
                                    <span>{{ $skill }}</span>
                                </div>
                                <div class="col-md-7">
                                    <select wire:model="skillsAssessment.{{ $skill }}"
                                        class="form-control form-control-sm">
                                        <option value="">Not Assessed</option>
                                        <option value="1">1 - Poor</option>
                                        <option value="2">2 - Below Average</option>
                                        <option value="3">3 - Average</option>
                                        <option value="4">4 - Good</option>
                                        <option value="5">5 - Excellent</option>
                                    </select>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Strengths -->
                <div class="form-group">
                    <label class="font-weight-bold">Strengths</label>
                    <div class="input-group mb-2">
                        <input type="text" wire:model="newStrengthInput" class="form-control"
                            placeholder="Add a strength..." wire:keydown.enter="addStrengthFromInput">
                        <div class="input-group-append">
                            <button class="btn btn-outline-success" type="button" wire:click="addStrengthFromInput">
                                <i class="fas fa-plus"></i> Add
                            </button>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach ($studentStrengths as $index => $strength)
                            <span class="badge badge-success p-2 mr-2 mb-2">
                                {{ $strength }}
                                <i class="fas fa-times ml-2" style="cursor: pointer;"
                                    wire:click="removeStrength({{ $index }})"></i>
                            </span>
                        @endforeach
                    </div>
                </div>

                <!-- Areas for Improvement -->
                <div class="form-group">
                    <label class="font-weight-bold">Areas for Improvement</label>
                    <div class="input-group mb-2">
                        <input type="text" wire:model="newWeaknessInput" class="form-control"
                            placeholder="Add area for improvement..." wire:keydown.enter="addWeaknessFromInput">
                        <div class="input-group-append">
                            <button class="btn btn-outline-warning" type="button" wire:click="addWeaknessFromInput">
                                <i class="fas fa-plus"></i> Add
                            </button>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach ($studentWeaknesses as $index => $weakness)
                            <span class="badge badge-warning p-2 mr-2 mb-2">
                                {{ $weakness }}
                                <i class="fas fa-times ml-2" style="cursor: pointer;"
                                    wire:click="removeWeakness({{ $index }})"></i>
                            </span>
                        @endforeach
                    </div>
                </div>

                <!-- Feedback -->
                <div class="form-group">
                    <label class="font-weight-bold">Detailed Feedback</label>
                    <textarea wire:model="interviewFeedback" class="form-control" rows="4"
                        placeholder="Provide detailed feedback about the interview..."></textarea>
                </div>

                <!-- Interview Notes -->
                <div class="form-group">
                    <label class="font-weight-bold">Additional Notes</label>
                    <textarea wire:model="interviewNotes" class="form-control" rows="2"
                        placeholder="Any additional notes about the interview..."></textarea>
                </div>

                <!-- Next Steps -->
                <div class="form-group">
                    <label class="font-weight-bold">Next Steps</label>
                    <textarea wire:model="nextSteps" class="form-control" rows="2"
                        placeholder="What are the next steps after this interview?"></textarea>
                </div>

                <!-- Follow-up -->
                {{-- <div class="form-group">
                    <div class="custom-control custom-switch mb-2">
                        <input type="checkbox" class="custom-control-input" id="followUpRequired"
                            wire:model="followUpRequired">
                        <label class="custom-control-label font-weight-bold" for="followUpRequired">
                            Schedule Follow-up
                        </label>
                    </div>

                    @if ($followUpRequired)
                        <div class="mt-2">
                            <label>Follow-up Date</label>
                            <input type="date" wire:model="followUpDate"
                                class="form-control @error('followUpDate') is-invalid @enderror"
                                min="{{ now()->format('Y-m-d') }}">
                            @error('followUpDate')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="text-muted">Select a date for follow-up action</small>
                        </div>
                    @endif
                </div> --}}
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-outline-secondary"
                    wire:click="$set('showCompleteInterviewModal', false)">
                    Cancel
                </button>
                <button type="button" class="btn btn-success" wire:click="completeInterview"
                    wire:loading.attr="disabled" wire:target="completeInterview">
                    <span wire:loading.remove wire:target="completeInterview">
                        <i class="fas fa-check-circle mr-1"></i> Complete Interview
                    </span>
                    <span wire:loading wire:target="completeInterview">
                        <i class="fas fa-spinner fa-spin mr-1"></i> Processing...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal-backdrop fade show"></div>
