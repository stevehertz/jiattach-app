<div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning text-white border-0">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    Reschedule Interview
                </h5>
                <button type="button" class="close text-white" wire:click="$set('showRescheduleModal', false)">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    Current schedule: <strong>{{ $interview->scheduled_at->format('M d, Y h:i A') }}</strong>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">New Date <span class="text-danger">*</span></label>
                            <input type="date" wire:model="newDate" class="form-control"
                                min="{{ now()->format('Y-m-d') }}">
                            @error('newDate')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">New Time <span class="text-danger">*</span></label>
                            <input type="time" wire:model="newTime" class="form-control">
                            @error('newTime')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold">Reason for Rescheduling</label>
                    <textarea wire:model="rescheduleReason" class="form-control" rows="3"
                        placeholder="e.g., Student requested, interviewer unavailable, technical issues..."></textarea>
                    @error('rescheduleReason')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-outline-secondary"
                    wire:click="$set('showRescheduleModal', false)">
                    Cancel
                </button>
                <button type="button" class="btn btn-warning" wire:click="rescheduleInterview">
                    <i class="fas fa-calendar-check mr-1"></i>
                    Confirm Reschedule
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal-backdrop fade show"></div>
