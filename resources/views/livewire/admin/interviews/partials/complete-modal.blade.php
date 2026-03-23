<div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white border-0">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle mr-2"></i>
                    Mark Interview as Completed
                </h5>
                <button type="button" class="close text-white" wire:click="$set('showCompleteModal', false)">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="fas fa-check-circle fa-4x text-success"></i>
                    <h5 class="mt-3">Complete Interview</h5>
                    <p class="text-muted">Record the outcome of the interview</p>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold">Rating</label>
                    <div class="rating-stars mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= $interviewRating ? 'text-warning' : 'text-muted' }}"
                               style="font-size: 24px; cursor: pointer; margin-right: 5px;"
                               wire:click="$set('interviewRating', {{ $i }})"></i>
                        @endfor
                    </div>
                    <small class="text-muted d-block mb-3">
                        @switch($interviewRating)
                            @case(1) Poor @break
                            @case(2) Fair @break
                            @case(3) Good @break
                            @case(4) Very Good @break
                            @case(5) Excellent @break
                        @endswitch
                    </small>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold">Feedback / Notes</label>
                    <textarea wire:model="interviewFeedback" class="form-control" rows="4"
                              placeholder="Enter any feedback, observations, or notes about the interview..."></textarea>
                    @error('interviewFeedback') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-outline-secondary" 
                        wire:click="$set('showCompleteModal', false)">
                    Cancel
                </button>
                <button type="button" class="btn btn-success" wire:click="completeInterview">
                    <i class="fas fa-check-circle mr-1"></i>
                    Mark as Completed
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal-backdrop fade show"></div>