<?php

namespace App\Livewire\Admin\Applications;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class OfferStage extends Component
{
    use WithPagination;

    // Filters
    public $statusFilter = 'all'; // all, pending_payment, payment_completed, offer_sent, offer_accepted
    public $search = '';
    public $organizationFilter = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    // Selected application for offer modal
    public $selectedApplicationId;
    public $showOfferModal = false;
    public $offerStipend;
    public $offerStartDate;
    public $offerEndDate;
    public $offerNotes;
    public $offerTerms;

    // Payment details modal
    public $showPaymentModal = false;
    public $selectedPayment;

    // Bulk actions
    public $selectedApplications = [];
    public $selectAll = false;
    public $bulkAction = '';

    protected $queryString = [
        'statusFilter' => ['except' => 'all'],
        'search' => ['except' => ''],
        'organizationFilter' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    protected $listeners = [
        'refreshComponent' => '$refresh',
        'offerSent' => 'refreshOffers',
    ];

    public function mount()
    {
        $this->offerStartDate = now()->addDays(7)->format('Y-m-d');
        $this->offerEndDate = now()->addMonths(3)->format('Y-m-d');
    }

    public function refreshOffers()
    {
        $this->dispatch('refreshComponent');
    }

    /**
     * Get the base query for applications
     */
    public function getApplicationsQuery()
    {
        $query = Application::query()
            ->with([
                'student' => function ($q) {
                    $q->with('studentProfile');
                },
                'opportunity.organization',
                'paymentTransaction',
                'interviews' => function ($q) {
                    $q->latest()->take(1);
                }
            ])
            ->where(function ($q) {
                // Applications that are ready for offer or have offers
                $q->where('status', ApplicationStatus::INTERVIEW_COMPLETED)
                    ->orWhere('status', ApplicationStatus::OFFER_SENT)
                    ->orWhere('status', ApplicationStatus::OFFER_ACCEPTED)
                    ->orWhere('status', ApplicationStatus::OFFER_REJECTED);
            });

        // Apply status filter
        if ($this->statusFilter !== 'all') {
            switch ($this->statusFilter) {
                case 'pending_payment':
                    $query->where('status', ApplicationStatus::INTERVIEW_COMPLETED)
                        ->whereNull('payment_completed_at');
                    break;
                case 'payment_completed':
                    $query->where('status', ApplicationStatus::INTERVIEW_COMPLETED)
                        ->whereNotNull('payment_completed_at');
                    break;
                case 'offer_sent':
                    $query->where('status', ApplicationStatus::OFFER_SENT);
                    break;
                case 'offer_accepted':
                    $query->where('status', ApplicationStatus::OFFER_ACCEPTED);
                    break;
                case 'offer_rejected':
                    $query->where('status', ApplicationStatus::OFFER_REJECTED);
                    break;
            }
        }

        // Search by student name or opportunity title
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('student', function ($sub) {
                    $sub->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%');
                })->orWhereHas('opportunity', function ($sub) {
                    $sub->where('title', 'like', '%' . $this->search . '%');
                });
            });
        }

        // Filter by organization
        if ($this->organizationFilter) {
            $query->whereHas('opportunity.organization', function ($q) {
                $q->where('name', 'like', '%' . $this->organizationFilter . '%');
            });
        }

        // Date range filter
        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        // Sorting
        if ($this->sortBy === 'student_name') {
            $query->join('users', 'applications.student_id', '=', 'users.id')
                ->orderBy('users.first_name', $this->sortDirection)
                ->orderBy('users.last_name', $this->sortDirection);
        } elseif ($this->sortBy === 'opportunity_title') {
            $query->join('attachment_opportunities', 'applications.attachment_opportunity_id', '=', 'attachment_opportunities.id')
                ->orderBy('attachment_opportunities.title', $this->sortDirection);
        } elseif ($this->sortBy === 'match_score') {
            $query->orderBy('match_score', $this->sortDirection);
        } elseif ($this->sortBy === 'payment_status') {
            $query->orderBy('payment_completed_at', $this->sortDirection);
        } else {
            $query->orderBy($this->sortBy, $this->sortDirection);
        }

        return $query;
    }

    public function getApplicationsProperty()
    {
        return $this->getApplicationsQuery()->paginate(15);
    }

    /**
     * Get statistics for the dashboard
     */
    public function getStatsProperty()
    {
        $baseQuery = $this->getApplicationsQuery();

        // Calculate total offer value safely
        $totalOfferValue = Application::whereIn('status', [
            ApplicationStatus::OFFER_SENT->value,
            ApplicationStatus::OFFER_ACCEPTED->value
        ])
            ->get()
            ->sum(function ($application) {
                // Safely get stipend from offer_details
                if ($application->offer_details && isset($application->offer_details['stipend'])) {
                    return (float) $application->offer_details['stipend'];
                }
                return 0;
            });

        return [
            'total' => $baseQuery->count(),
            'pending_payment' => Application::where('status', ApplicationStatus::INTERVIEW_COMPLETED->value)
                ->whereNull('payment_completed_at')
                ->count(),
            'payment_completed' => Application::where('status', ApplicationStatus::INTERVIEW_COMPLETED->value)
                ->whereNotNull('payment_completed_at')
                ->count(),
            'offers_sent' => Application::where('status', ApplicationStatus::OFFER_SENT->value)->count(),
            'offers_accepted' => Application::where('status', ApplicationStatus::OFFER_ACCEPTED->value)->count(),
            'offers_rejected' => Application::where('status', ApplicationStatus::OFFER_REJECTED->value)->count(),
            'total_offer_value' => $totalOfferValue,
        ];
    }

    /**
     * Get organizations for filter dropdown
     */
    public function getOrganizationsProperty()
    {
        return \App\Models\Organization::orderBy('name')->get(['id', 'name']);
    }

    /**
     * Open offer modal for a specific application
     */
    public function openOfferModal($applicationId)
    {
        $application = Application::with(['opportunity', 'student', 'paymentTransaction'])
            ->findOrFail($applicationId);

        // Validate that payment is completed
        if (!$application->payment_completed_at) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Cannot send offer: Payment not completed for this application.'
            ]);
            return;
        }

        $this->selectedApplicationId = $applicationId;
        $this->offerStipend = $application->opportunity->stipend ?? 10000;
        $this->offerStartDate = $application->opportunity->start_date?->format('Y-m-d') ?? now()->addDays(7)->format('Y-m-d');
        $this->offerEndDate = $application->opportunity->end_date?->format('Y-m-d') ?? now()->addMonths(3)->format('Y-m-d');
        $this->offerNotes = '';
        $this->offerTerms = '';

        $this->showOfferModal = true;
    }

    /**
     * Send offer to student
     */
    public function sendOffer()
    {
        $this->validate([
            'offerStipend' => 'required|numeric|min:0',
            'offerStartDate' => 'required|date|after_or_equal:today',
            'offerEndDate' => 'required|date|after:offerStartDate',
            'offerNotes' => 'nullable|string|max:1000',
            'offerTerms' => 'nullable|string|max:2000',
        ]);

        $application = Application::findOrFail($this->selectedApplicationId);

        // Double-check payment status
        if (!$application->payment_completed_at) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Cannot send offer: Payment not completed.'
            ]);
            $this->showOfferModal = false;
            return;
        }

        DB::transaction(function () use ($application) {
            $oldStatus = $application->status;

            // Update application with offer details
            $application->status = ApplicationStatus::OFFER_SENT;
            $application->offer_sent_at = now();
            $application->offer_details = [
                'stipend' => $this->offerStipend,
                'start_date' => $this->offerStartDate,
                'end_date' => $this->offerEndDate,
                'notes' => $this->offerNotes,
                'terms' => $this->offerTerms,
                'sent_by' => auth()->user()->full_name,
                'sent_at' => now()->toDateTimeString(),
            ];
            $application->save();

            // Add application history
            $application->addHistory(
                'offer_sent',
                $application->student_id,
                $application->organization_id,
                $oldStatus->value,
                ApplicationStatus::OFFER_SENT->value,
                $this->offerNotes ?: 'Offer sent to student',
                [
                    'stipend' => $this->offerStipend,
                    'start_date' => $this->offerStartDate,
                    'end_date' => $this->offerEndDate,
                    'payment_reference' => $application->payment_reference,
                ]
            );

            // Log activity
            activity_log(
                "Offer sent for application #{$application->id} - KSh {$this->offerStipend}",
                'offer_sent',
                [
                    'application_id' => $application->id,
                    'student_name' => $application->student->full_name,
                    'opportunity' => $application->opportunity->title,
                    'stipend' => $this->offerStipend,
                ],
                'application'
            );

            // TODO: Send email notification
        });

        $this->showOfferModal = false;
        $this->selectedApplicationId = null;

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Offer sent successfully!'
        ]);

        $this->dispatch('refreshComponent');
    }

    /**
     * View payment details
     */
    public function viewPaymentDetails($applicationId)
    {
        $application = Application::with(['paymentTransaction', 'student'])
            ->findOrFail($applicationId);

        $this->selectedPayment = $application;
        $this->showPaymentModal = true;
    }

    /**
     * Mark offer as accepted (admin override)
     */
    public function markOfferAccepted($applicationId)
    {
        $application = Application::findOrFail($applicationId);

        DB::transaction(function () use ($application) {
            $oldStatus = $application->status;
            $application->status = ApplicationStatus::OFFER_ACCEPTED;
            $application->offer_response_at = now();
            $application->save();

            $application->addHistory(
                'offer_accepted',
                $application->student_id,
                $application->organization_id,
                $oldStatus->value,
                ApplicationStatus::OFFER_ACCEPTED->value,
                'Offer accepted (admin override)',
                []
            );
        });

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Offer marked as accepted!'
        ]);

        $this->dispatch('refreshComponent');
    }

    /**
     * Bulk actions
     */
    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedApplications = $this->applications->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedApplications = [];
        }
    }

    public function executeBulkAction()
    {
        if (empty($this->selectedApplications)) {
            $this->dispatch('show-toast', [
                'type' => 'warning',
                'message' => 'No applications selected.'
            ]);
            return;
        }

        if ($this->bulkAction === 'send_offers') {
            $this->bulkSendOffers();
        } elseif ($this->bulkAction === 'export') {
            $this->bulkExport();
        }

        $this->bulkAction = '';
        $this->selectedApplications = [];
        $this->selectAll = false;
    }

    protected function bulkSendOffers()
    {
        $count = 0;
        $skipped = 0;

        foreach ($this->selectedApplications as $id) {
            $application = Application::find($id);

            if ($application->payment_completed_at && $application->status === ApplicationStatus::INTERVIEW_COMPLETED) {
                // Auto-send offer with default values
                DB::transaction(function () use ($application) {
                    $oldStatus = $application->status;
                    $application->status = ApplicationStatus::OFFER_SENT;
                    $application->offer_sent_at = now();
                    $application->offer_details = [
                        'stipend' => $application->opportunity->stipend ?? 10000,
                        'start_date' => $application->opportunity->start_date?->format('Y-m-d') ?? now()->addDays(7)->format('Y-m-d'),
                        'end_date' => $application->opportunity->end_date?->format('Y-m-d') ?? now()->addMonths(3)->format('Y-m-d'),
                        'notes' => 'Bulk offer sent',
                        'sent_by' => auth()->user()->full_name,
                        'sent_at' => now()->toDateTimeString(),
                    ];
                    $application->save();

                    $application->addHistory(
                        'offer_sent',
                        $application->student_id,
                        $application->organization_id,
                        $oldStatus->value,
                        ApplicationStatus::OFFER_SENT->value,
                        'Bulk offer sent',
                        []
                    );
                });
                $count++;
            } else {
                $skipped++;
            }
        }

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => "{$count} offers sent successfully. {$skipped} applications skipped (payment not completed)."
        ]);

        $this->dispatch('refreshComponent');
    }

    protected function bulkExport()
    {
        $applications = Application::whereIn('id', $this->selectedApplications)
            ->with(['student', 'opportunity.organization', 'paymentTransaction'])
            ->get();

        // Generate CSV export
        $fileName = 'offers_export_' . now()->format('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function () use ($applications) {
            $file = fopen('php://output', 'w');

            // Add headers
            fputcsv($file, [
                'Application ID',
                'Student Name',
                'Student Email',
                'Opportunity',
                'Organization',
                'Match Score',
                'Status',
                'Payment Status',
                'Offer Amount',
                'Applied Date',
                'Payment Date'
            ]);

            // Add data
            foreach ($applications as $app) {
                fputcsv($file, [
                    $app->id,
                    $app->student->full_name,
                    $app->student->email,
                    $app->opportunity->title,
                    $app->opportunity->organization->name,
                    $app->match_score . '%',
                    $app->status->label(),
                    $app->payment_completed_at ? 'Completed' : 'Pending',
                    $app->offer_details['stipend'] ?? 'N/A',
                    $app->created_at->format('Y-m-d'),
                    $app->payment_completed_at?->format('Y-m-d') ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Reset filters
     */
    public function resetFilters()
    {
        $this->reset(['statusFilter', 'search', 'organizationFilter', 'dateFrom', 'dateTo', 'sortBy', 'sortDirection']);
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
    }

    public function render()
    {
        return view('livewire.admin.applications.offer-stage', [
            'applications' => $this->applications,
            'stats' => $this->stats,
            'organizations' => $this->organizations,
        ]);
    }
}
