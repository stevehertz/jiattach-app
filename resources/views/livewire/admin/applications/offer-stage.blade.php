<div>
    {{-- Because she competes with no one, no one can compete with her. --}}
    @push('styles')
        <style>
            .stats-card {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border-radius: 15px;
                transition: transform 0.3s;
            }

            .stats-card:hover {
                transform: translateY(-5px);
            }

            .payment-badge {
                padding: 4px 8px;
                border-radius: 12px;
                font-size: 11px;
                font-weight: 600;
            }

            .payment-completed {
                background: #d4edda;
                color: #155724;
            }

            .payment-pending {
                background: #fff3cd;
                color: #856404;
            }

            .payment-processing {
                background: #d1ecf1;
                color: #0c5460;
            }

            .status-badge {
                padding: 6px 12px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 600;
            }
        </style>
    @endpush

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 font-weight-bold">Offer Stage Applications</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.applications.index') }}">Applications</a>
                        </li>
                        <li class="breadcrumb-item active">Offer Stage</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-lg-2 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $stats['total'] }}</h3>
                            <p>Total Applications</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $stats['pending_payment'] }}</h3>
                            <p>Pending Payment</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-credit-card"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>{{ $stats['payment_completed'] }}</h3>
                            <p>Payment Completed</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $stats['offers_sent'] }}</h3>
                            <p>Offers Sent</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $stats['offers_accepted'] }}</h3>
                            <p>Offers Accepted</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-handshake"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ $stats['offers_rejected'] }}</h3>
                            <p>Offers Rejected</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-times-circle"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 font-weight-bold">
                            <i class="fas fa-filter mr-2 text-primary"></i> Filters
                        </h5>
                        <button class="btn btn-sm btn-outline-secondary" wire:click="resetFilters">
                            <i class="fas fa-undo-alt mr-1"></i> Reset Filters
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Status Filter</label>
                            <select wire:model="statusFilter" class="form-control">
                                <option value="all">All Statuses</option>
                                <option value="pending_payment">Pending Payment</option>
                                <option value="payment_completed">Payment Completed</option>
                                <option value="offer_sent">Offer Sent</option>
                                <option value="offer_accepted">Offer Accepted</option>
                                <option value="offer_rejected">Offer Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Organization</label>
                            <select wire:model="organizationFilter" class="form-control">
                                <option value="">All Organizations</option>
                                @foreach ($organizations as $org)
                                    <option value="{{ $org->name }}">{{ $org->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Date From</label>
                            <input type="date" wire:model="dateFrom" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>Date To</label>
                            <input type="date" wire:model="dateTo" class="form-control">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label>Search</label>
                            <input type="text" wire:model="search" class="form-control"
                                placeholder="Search by student name or opportunity...">
                        </div>
                        <div class="col-md-3">
                            <label>Sort By</label>
                            <select wire:model="sortBy" class="form-control">
                                <option value="created_at">Applied Date</option>
                                <option value="student_name">Student Name</option>
                                <option value="opportunity_title">Opportunity Title</option>
                                <option value="match_score">Match Score</option>
                                <option value="payment_status">Payment Status</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Sort Direction</label>
                            <select wire:model="sortDirection" class="form-control">
                                <option value="desc">Descending</option>
                                <option value="asc">Ascending</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bulk Actions -->
            <div class="card shadow-sm border-0 mb-4" wire:loading.class="opacity-50">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="selectAll"
                                    wire:model="selectAll">
                                <label class="custom-control-label" for="selectAll">
                                    Select All ({{ $applications->total() }})
                                </label>
                            </div>
                        </div>
                        <div class="d-flex">
                            <select wire:model="bulkAction" class="form-control form-control-sm mr-2"
                                style="width: 200px;">
                                <option value="">Bulk Actions</option>
                                <option value="send_offers">Send Offers</option>
                                <option value="export">Export to CSV</option>
                            </select>
                            <button class="btn btn-sm btn-primary" wire:click="executeBulkAction"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove>Apply</span>
                                <span wire:loading><i class="fas fa-spinner fa-spin"></i> Processing...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Applications Table -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th width="40">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="selectAllHeader"
                                                wire:model="selectAll">
                                            <label class="custom-control-label" for="selectAllHeader"></label>
                                        </div>
                                    </th>
                                    <th>Student</th>
                                    <th>Opportunity</th>
                                    <th>Match Score</th>
                                    <th>Payment Status</th>
                                    <th>Offer Status</th>
                                    <th>Applied Date</th>
                                    <th width="150">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($applications as $application)
                                    <tr wire:key="{{ $application->id }}">
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input"
                                                    id="app_{{ $application->id }}" value="{{ $application->id }}"
                                                    wire:model="selectedApplications">
                                                <label class="custom-control-label"
                                                    for="app_{{ $application->id }}"></label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle bg-primary-soft mr-2"
                                                    style="width: 35px; height: 35px; line-height: 35px;">
                                                    {{ substr($application->student->full_name, 0, 2) }}
                                                </div>
                                                <div>
                                                    <strong>{{ $application->student->full_name }}</strong><br>
                                                    <small
                                                        class="text-muted">{{ $application->student->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>{{ Str::limit($application->opportunity->title, 40) }}</strong><br>
                                            <small
                                                class="text-muted">{{ $application->opportunity->organization->name }}</small>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-grow-1 mr-2"
                                                    style="height: 6px; width: 80px;">
                                                    <div class="progress-bar bg-{{ $application->match_score >= 80 ? 'success' : ($application->match_score >= 60 ? 'warning' : 'danger') }}"
                                                        style="width: {{ $application->match_score }}%"></div>
                                                </div>
                                                <span
                                                    class="small font-weight-bold">{{ $application->match_score }}%</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($application->payment_completed_at)
                                                <span class="payment-badge payment-completed">
                                                    <i class="fas fa-check-circle mr-1"></i> Completed
                                                </span>
                                                <br>
                                                <small
                                                    class="text-muted">{{ $application->payment_completed_at->format('d M Y') }}</small>
                                            @elseif($application->paymentTransaction)
                                                <span class="payment-badge payment-processing">
                                                    <i class="fas fa-spinner fa-pulse mr-1"></i>
                                                    {{ ucfirst($application->paymentTransaction->status) }}
                                                </span>
                                            @else
                                                <span class="payment-badge payment-pending">
                                                    <i class="fas fa-clock mr-1"></i> Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $application->status->color() }} p-2">
                                                <i class="fas {{ $application->status->icon() }} mr-1"></i>
                                                {{ $application->status->label() }}
                                            </span>
                                        </td>
                                        <td>
                                            <small>{{ $application->created_at->format('d M Y') }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.applications.show', $application->id) }}"
                                                    class="btn btn-sm btn-outline-info" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if ($application->payment_completed_at && $application->status === \App\Enums\ApplicationStatus::INTERVIEW_COMPLETED)
                                                    <button class="btn btn-sm btn-outline-success"
                                                        wire:click="openOfferModal({{ $application->id }})"
                                                        title="Send Offer">
                                                        <i class="fas fa-paper-plane"></i>
                                                    </button>
                                                @endif
                                                @if ($application->paymentTransaction)
                                                    <button class="btn btn-sm btn-outline-primary"
                                                        wire:click="viewPaymentDetails({{ $application->id }})"
                                                        title="View Payment">
                                                        <i class="fas fa-credit-card"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <p class="text-muted mb-0">No applications found</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="card-footer bg-white border-0">
                        {{ $applications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Send Offer Modal -->
    @if ($showOfferModal)
        <div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-success text-white border-0">
                        <h5 class="modal-title">
                            <i class="fas fa-paper-plane mr-2"></i> Send Offer
                        </h5>
                        <button type="button" class="close text-white" wire:click="$set('showOfferModal', false)">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Stipend Amount <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">KSh</span>
                                        </div>
                                        <input type="number" wire:model="offerStipend" class="form-control"
                                            step="1000">
                                    </div>
                                    @error('offerStipend')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Start Date <span class="text-danger">*</span></label>
                                    <input type="date" wire:model="offerStartDate" class="form-control">
                                    @error('offerStartDate')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>End Date <span class="text-danger">*</span></label>
                                    <input type="date" wire:model="offerEndDate" class="form-control">
                                    @error('offerEndDate')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Additional Notes</label>
                                    <textarea wire:model="offerNotes" class="form-control" rows="2"
                                        placeholder="Any additional notes for the student..."></textarea>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Terms & Conditions</label>
                                    <textarea wire:model="offerTerms" class="form-control" rows="3"
                                        placeholder="Terms and conditions of the offer..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-outline-secondary"
                            wire:click="$set('showOfferModal', false)">
                            Cancel
                        </button>
                        <button type="button" class="btn btn-success" wire:click="sendOffer"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="fas fa-paper-plane mr-1"></i> Send Offer</span>
                            <span wire:loading><i class="fas fa-spinner fa-spin mr-1"></i> Sending...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

    <!-- Payment Details Modal -->
    @if ($showPaymentModal && $selectedPayment)
        <div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-primary text-white border-0">
                        <h5 class="modal-title">
                            <i class="fas fa-credit-card mr-2"></i> Payment Details
                        </h5>
                        <button type="button" class="close text-white" wire:click="$set('showPaymentModal', false)">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <h6 class="font-weight-bold">Student Information</h6>
                                <p><strong>{{ $selectedPayment->student->full_name }}</strong><br>
                                    <small>{{ $selectedPayment->student->email }}</small>
                                </p>
                                <hr>
                            </div>
                            <div class="col-md-12">
                                <h6 class="font-weight-bold">Payment Information</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <th>Reference:</th>
                                        <td><strong>{{ $selectedPayment->payment_reference ?? 'N/A' }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td>
                                            @if ($selectedPayment->payment_completed_at)
                                                <span class="badge badge-success">Completed</span>
                                            @elseif($selectedPayment->paymentTransaction)
                                                <span
                                                    class="badge badge-{{ $selectedPayment->paymentTransaction->status === 'pending' ? 'warning' : ($selectedPayment->paymentTransaction->status === 'processing' ? 'info' : 'danger') }}">
                                                    {{ ucfirst($selectedPayment->paymentTransaction->status) }}
                                                </span>
                                            @else
                                                <span class="badge badge-warning">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @if ($selectedPayment->payment_completed_at)
                                        <tr>
                                            <th>Completed On:</th>
                                            <td>{{ $selectedPayment->payment_completed_at->format('d M Y, h:i A') }}
                                            </td>
                                        </tr>
                                    @endif
                                    @if ($selectedPayment->paymentTransaction && $selectedPayment->paymentTransaction->mpesa_receipt)
                                        <tr>
                                            <th>M-Pesa Receipt:</th>
                                            <td><strong>{{ $selectedPayment->paymentTransaction->mpesa_receipt }}</strong>
                                            </td>
                                        </tr>
                                    @endif
                                    @if ($selectedPayment->paymentTransaction && $selectedPayment->paymentTransaction->phone_number)
                                        <tr>
                                            <th>Phone Number:</th>
                                            <td>{{ $selectedPayment->paymentTransaction->phone_number }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th>Amount:</th>
                                        <td><strong class="text-success">KSh
                                                {{ number_format($selectedPayment->paymentTransaction?->amount ?? 0, 2) }}</strong>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-secondary"
                            wire:click="$set('showPaymentModal', false)">
                            Close
                        </button>
                        @if ($selectedPayment->paymentTransaction && $selectedPayment->paymentTransaction->status === 'completed')
                            <button type="button" class="btn btn-primary"
                                onclick="window.open('{{ route('student.payment.receipt', $selectedPayment->paymentTransaction->id) }}', '_blank')">
                                <i class="fas fa-download mr-1"></i> Download Receipt
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif


</div>
