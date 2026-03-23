<div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title">
                    <i class="fas fa-times-circle mr-2"></i>
                    Cancel Interview
                </h5>
                <button type="button" class="close text-white" wire:click="$set('showCancelModal', false)">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Are you sure you want to cancel this interview? This action cannot be undone.
                </div>

                <div class="form-group">
                    <label class="font-weight-bold">Reason for Cancellation <span class="text-danger">*</span></label>
                    <textarea wire:model="cancelReason" class="form-control" rows="4"
                              placeholder="Please provide a reason for cancellation..."></textarea>
                    @error('cancelReason') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-outline-secondary" 
                        wire:click="$set('showCancelModal', false)">
                    Go Back
                </button>
                <button type="button" class="btn btn-danger" wire:click="cancelInterview">
                    <i class="fas fa-times-circle mr-1"></i>
                    Yes, Cancel Interview
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal-backdrop fade show"></div>