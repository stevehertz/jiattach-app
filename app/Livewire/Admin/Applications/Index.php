<?php

namespace App\Livewire\Admin\Applications;

use App\Models\Application;
use App\Models\AttachmentOpportunity;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $opportunityFilter = '';
    public $studentFilter = '';
    public $employerFilter = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 20;
    public $selectedApplications = [];
    public $selectAll = false;
    public $showFilters = false;
    public $showBulkActions = false;
    public $viewType = 'all'; // 'all', 'pending', 'interviewing', 'offers', 'hired', 'rejected'
    // Add these properties
    public $bulkAction = '';
    public $showBulkActionModal = false;
    // Add these properties
    public $cgpaMin = '';
    public $cgpaMax = '';
    public $institutionFilter = '';
    public $courseFilter = '';
    public $yearOfStudyFilter = '';
    public $skillFilter = '';

    protected $listeners = ['refreshApplications' => '$refresh'];

    public function mount($viewType = 'all')
    {
        $this->viewType = $viewType;
        $this->applyViewFilters();
    }

    protected function applyViewFilters()
    {
        switch ($this->viewType) {
            case 'pending':
                $this->statusFilter = 'submitted,under_review';
                break;
            case 'interviewing':
                $this->statusFilter = 'interview_scheduled,interview_completed';
                break;
            case 'offers':
                $this->statusFilter = 'offer_sent,offer_accepted,offer_rejected';
                break;
            case 'hired':
                $this->statusFilter = 'hired';
                break;
            case 'rejected':
                $this->statusFilter = 'rejected';
                break;
        }
    }


    // Add this method
    public function applyBulkAction()
    {
        if (!$this->bulkAction || empty($this->selectedApplications)) {
            return;
        }

        $applications = Application::whereIn('id', $this->selectedApplications)->get();

        switch ($this->bulkAction) {
            case 'shortlist':
                foreach ($applications as $app) {
                    if (in_array($app->status, ['submitted', 'under_review'])) {
                        $app->status = 'shortlisted';
                        $app->save();
                    }
                }
                $message = 'Applications shortlisted successfully!';
                break;

            case 'reject':
                foreach ($applications as $app) {
                    if (in_array($app->status, ['submitted', 'under_review', 'shortlisted'])) {
                        $app->reject('Bulk rejection by admin');
                    }
                }
                $message = 'Applications rejected successfully!';
                break;

            case 'schedule_interview':
                // You would redirect to bulk interview scheduling
                session(['bulk_applications' => $this->selectedApplications]);
                return redirect()->route('admin.applications.bulk-interview');
                break;

            case 'send_offer':
                // You would redirect to bulk offer sending
                session(['bulk_applications' => $this->selectedApplications]);
                return redirect()->route('admin.applications.bulk-offer');
                break;

            case 'export':
                return $this->exportApplications();
                break;

            case 'archive':
                foreach ($applications as $app) {
                    $app->archive();
                }
                $message = 'Applications archived successfully!';
                break;
        }
        $this->selectedApplications = [];
        $this->showBulkActions = false;
        $this->showBulkActionModal = false;
        $this->bulkAction = '';

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => $message
        ]);

        $this->dispatch('refreshApplications');
    }

    private function exportApplications()
    {
        $applications = Application::whereIn('id', $this->selectedApplications)
            ->with(['student', 'opportunity', 'opportunity.employer'])
            ->get();

        $csvData = [];

        // Add headers
        $csvData[] = [
            'Application ID',
            'Student Name',
            'Student Email',
            'Opportunity Title',
            'Company',
            'Status',
            'Applied Date',
            'Cover Letter Excerpt',
            'Feedback',
        ];

        // Add data
        foreach ($applications as $app) {
            $csvData[] = [
                $app->id,
                $app->student->full_name,
                $app->student->email,
                $app->opportunity->title,
                $app->opportunity->employer->company_name,
                $app->status_label,
                $app->submitted_at ? $app->submitted_at->format('Y-m-d H:i') : '',
                substr($app->cover_letter ?? '', 0, 100),
                substr($app->feedback ?? '', 0, 100),
            ];
        }

        $filename = 'applications_export_' . date('Y-m-d_H-i') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedApplications = $this->getApplicationsQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedApplications = [];
        }
    }

    public function updatedSelectedApplications()
    {
        $this->selectAll = false;
        $this->showBulkActions = count($this->selectedApplications) > 0;
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    public function getApplicationsQuery()
    {
        $query = Application::with(['student', 'student.studentProfile', 'opportunity'])
            ->when($this->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('cover_letter', 'like', '%' . $search . '%')
                        ->orWhere('feedback', 'like', '%' . $search . '%')
                        ->orWhereHas('student', function ($q) use ($search) {
                            $q->where('first_name', 'like', '%' . $search . '%')
                                ->orWhere('last_name', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('opportunity', function ($q) use ($search) {
                            $q->where('title', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($this->statusFilter, function ($query, $status) {
                $statuses = explode(',', $status);
                $query->whereIn('status', $statuses);
            })
            ->when($this->cgpaMin, function ($query, $cgpa) {
                $query->whereHas('student.studentProfile', function ($q) use ($cgpa) {
                    $q->where('cgpa', '>=', $cgpa);
                });
            })
            ->when($this->cgpaMax, function ($query, $cgpa) {
                $query->whereHas('student.studentProfile', function ($q) use ($cgpa) {
                    $q->where('cgpa', '<=', $cgpa);
                });
            })
            ->when($this->institutionFilter, function ($query, $institution) {
                $query->whereHas('student.studentProfile', function ($q) use ($institution) {
                    $q->where('institution_name', 'like', '%' . $institution . '%');
                });
            })
            ->when($this->courseFilter, function ($query, $course) {
                $query->whereHas('student.studentProfile', function ($q) use ($course) {
                    $q->where('course_name', 'like', '%' . $course . '%');
                });
            })
            ->when($this->yearOfStudyFilter, function ($query, $year) {
                $query->whereHas('student.studentProfile', function ($q) use ($year) {
                    $q->where('year_of_study', $year);
                });
            })
            ->when($this->skillFilter, function ($query, $skill) {
                $query->whereHas('student.studentProfile', function ($q) use ($skill) {
                    $q->whereJsonContains('skills', $skill);
                });
            })
            ->orderBy($this->sortField, $this->sortDirection);

        return $query;
    }

    // Add these properties to get data for filters
    public function getInstitutionsProperty()
    {
        return \App\Models\StudentProfile::distinct('institution_name')
            ->whereNotNull('institution_name')
            ->orderBy('institution_name')
            ->pluck('institution_name');
    }

    public function getCoursesProperty()
    {
        return \App\Models\StudentProfile::distinct('course_name')
            ->whereNotNull('course_name')
            ->orderBy('course_name')
            ->pluck('course_name');
    }

    public function getSkillsProperty()
    {
        $allSkills = \App\Models\StudentProfile::whereNotNull('skills')
            ->pluck('skills')
            ->flatten()
            ->unique()
            ->sort()
            ->values();

        return $allSkills;
    }
    public function getApplicationsProperty()
    {
        return $this->getApplicationsQuery()->paginate($this->perPage);
    }

    public function getStatsProperty()
    {
        return [
            'total' => Application::count(),
            'pending' => Application::whereIn('status', ['submitted', 'under_review'])->count(),
            'interviewing' => Application::whereIn('status', ['interview_scheduled', 'interview_completed'])->count(),
            'offers' => Application::whereIn('status', ['offer_sent', 'offer_accepted', 'offer_rejected'])->count(),
            'hired' => Application::where('status', 'hired')->count(),
            'rejected' => Application::where('status', 'rejected')->count(),
            'today' => Application::whereDate('created_at', today())->count(),
        ];
    }

    public function getOpportunitiesProperty()
    {
        return AttachmentOpportunity::active()
            ->orderBy('title')
            ->get(['id', 'title']);
    }

    public function updateStatus($applicationId, $status)
    {
        $application = Application::findOrFail($applicationId);

        $validTransitions = [
            'submitted' => ['under_review', 'rejected'],
            'under_review' => ['shortlisted', 'rejected'],
            'shortlisted' => ['interview_scheduled', 'rejected'],
            'interview_scheduled' => ['interview_completed', 'rejected'],
            'interview_completed' => ['offer_sent', 'rejected'],
            'offer_sent' => ['offer_accepted', 'offer_rejected'],
            'offer_accepted' => ['hired'],
        ];

        if (!isset($validTransitions[$application->status]) || !in_array($status, $validTransitions[$application->status])) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Invalid status transition'
            ]);
            return;
        }

        $application->status = $status;

        // Set timestamps based on status
        switch ($status) {
            case 'under_review':
                $application->reviewed_at = now();
                break;
            case 'interview_scheduled':
                $application->interview_scheduled_at = now();
                break;
            case 'interview_completed':
                $application->interview_scheduled_at = now(); // or keep existing
                break;
            case 'offer_sent':
                $application->offer_sent_at = now();
                break;
            case 'offer_accepted':
            case 'offer_rejected':
                $application->offer_response_at = now();
                break;
            case 'hired':
                $application->reviewed_at = now();
                break;
        }

        $application->save();

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Application status updated successfully!'
        ]);

        $this->dispatch('refreshApplications');
    }

    public function scheduleInterview($applicationId)
    {
        // This would open a modal or redirect to interview scheduling form
        return redirect()->route('admin.applications.edit', $applicationId);
    }

    public function sendOffer($applicationId)
    {
        // This would open a modal or redirect to offer sending form
        return redirect()->route('admin.applications.edit', $applicationId);
    }

    public function viewApplication($applicationId)
    {
        return redirect()->route('admin.applications.show', $applicationId);
    }

    public function deleteApplication($applicationId)
    {
        $application = Application::findOrFail($applicationId);

        if (!in_array($application->status, ['draft', 'submitted'])) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Cannot delete application at this stage'
            ]);
            return;
        }

        $application->delete();

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Application deleted successfully!'
        ]);

        $this->dispatch('refreshApplications');
    }

    public function bulkUpdateStatus($status)
    {
        $applications = Application::whereIn('id', $this->selectedApplications)->get();

        foreach ($applications as $application) {
            // Validate transition for each application
            if (in_array($application->status, ['draft', 'submitted', 'under_review', 'shortlisted'])) {
                $application->status = $status;
                $application->save();
            }
        }

        $this->selectedApplications = [];
        $this->showBulkActions = false;

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Selected applications updated successfully!'
        ]);
    }

    public function render()
    {
        return view('livewire.admin.applications.index', [
            'applications' => $this->applications,
            'stats' => $this->stats,
            'opportunities' => $this->opportunities,
        ]);
    }
}
