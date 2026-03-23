<div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-info text-white border-0">
                <h5 class="modal-title">
                    <i class="fas fa-comment mr-2"></i>
                    Add Feedback
                </h5>
                <button type="button" class="close text-white" wire:click="$set('showFeedbackModal', false)">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="font-weight-bold">Feedback Type</label>
                    <select wire:model="feedbackType" class="form-control">
                        <option value="general">General Feedback</option>
                        <option value="technical">Technical Skills</option>
                        <option value="communication">Communication Skills</option>
                        <option value="preparation">Preparation Level</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold">Feedback <span class="text-danger">*</span></label>
                    <textarea wire:model="feedbackMessage" class="form-control" rows="5"
                              placeholder="Enter your feedback about the interview, student performance, or any observations..."></textarea>
                    @error('feedbackMessage') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-outline-secondary" 
                        wire:click="$set('showFeedbackModal', false)">
                    Cancel
                </button>
                <button type="button" class="btn btn-info" wire:click="submitFeedback">
                    <i class="fas fa-paper-plane mr-1"></i>
                    Submit Feedback
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal-backdrop fade show"></div>