<?php

namespace App\Livewire\Admin\Mentorship;

use App\Models\Mentorship;
use App\Models\MentorshipReview;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Reviews extends Component
{
    use WithPagination;
    
    public $search = '';
    public $reviewType = '';
    public $mentorshipFilter = '';
    public $ratingFilter = '';
    public $statusFilter = '';
    public $dateRange = '';
    public $sortBy = 'published_at';
    public $sortDirection = 'desc';
    public $perPage = 15;
    
    public $selectedReviews = [];
    public $selectAll = false;
    
    // Statistics
    public $totalReviews;
    public $publishedReviews;
    public $averageRating;
    public $featuredReviews;
    
    // For response modal
    public $showResponseModal = false;
    public $selectedReviewId;
    public $responseText = '';
    
    public function mount()
    {
        $this->loadStatistics();
    }
    
    public function loadStatistics()
    {
        $this->totalReviews = MentorshipReview::count();
        $this->publishedReviews = MentorshipReview::where('status', 'published')->count();
        
        // Calculate average rating
        $publishedReviews = MentorshipReview::where('status', 'published')
            ->whereNotNull('overall_rating')
            ->get();
            
        if ($publishedReviews->count() > 0) {
            $this->averageRating = round($publishedReviews->avg('overall_rating'), 2);
        } else {
            $this->averageRating = 0;
        }
        
        $this->featuredReviews = MentorshipReview::where('is_featured', true)
            ->where('status', 'published')
            ->count();
    }
    
    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedReviews = $this->reviews->pluck('id')->toArray();
        } else {
            $this->selectedReviews = [];
        }
    }
    
    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        
        $this->sortBy = $field;
    }
    
    public function publishReview($reviewId)
    {
        $review = MentorshipReview::findOrFail($reviewId);
        $review->update(['status' => 'published', 'published_at' => now()]);
        
        $this->loadStatistics();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Review published']);
    }
    
    public function unpublishReview($reviewId)
    {
        $review = MentorshipReview::findOrFail($reviewId);
        $review->update(['status' => 'submitted', 'published_at' => null]);
        
        $this->loadStatistics();
        $this->dispatch('notify', ['type' => 'warning', 'message' => 'Review unpublished']);
    }
    
    public function featureReview($reviewId)
    {
        $review = MentorshipReview::findOrFail($reviewId);
        $review->update(['is_featured' => true]);
        
        $this->loadStatistics();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Review featured']);
    }
    
    public function unfeatureReview($reviewId)
    {
        $review = MentorshipReview::findOrFail($reviewId);
        $review->update(['is_featured' => false]);
        
        $this->loadStatistics();
        $this->dispatch('notify', ['type' => 'warning', 'message' => 'Review unfeatured']);
    }
    
    public function flagReview($reviewId)
    {
        $review = MentorshipReview::findOrFail($reviewId);
        $review->update([
            'status' => 'flagged',
            'flagged_by' => Auth::id(),
            'flagged_at' => now(),
            'flag_reason' => 'Flagged by admin'
        ]);
        
        $this->dispatch('notify', ['type' => 'warning', 'message' => 'Review flagged']);
    }
    
    public function unflagReview($reviewId)
    {
        $review = MentorshipReview::findOrFail($reviewId);
        $review->update([
            'status' => 'published',
            'flagged_by' => null,
            'flagged_at' => null,
            'flag_reason' => null
        ]);
        
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Review unflagged']);
    }
    
    public function deleteSelected()
    {
        if (empty($this->selectedReviews)) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Please select reviews to delete']);
            return;
        }
        
        MentorshipReview::whereIn('id', $this->selectedReviews)->delete();
        
        $this->selectedReviews = [];
        $this->selectAll = false;
        $this->loadStatistics();
        
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Selected reviews deleted']);
    }
    
    public function openResponseModal($reviewId)
    {
        $this->selectedReviewId = $reviewId;
        $review = MentorshipReview::find($reviewId);
        $this->responseText = $review->response ?? '';
        $this->showResponseModal = true;
    }
    
    public function closeResponseModal()
    {
        $this->showResponseModal = false;
        $this->selectedReviewId = null;
        $this->responseText = '';
    }
    
    public function saveResponse()
    {
        $this->validate([
            'responseText' => 'required|min:10|max:1000'
        ]);
        
        $review = MentorshipReview::findOrFail($this->selectedReviewId);
        $review->update([
            'response' => $this->responseText,
            'responded_at' => now()
        ]);
        
        $this->closeResponseModal();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Response saved successfully']);
    }
    
    public function getReviewsProperty()
    {
        $query = MentorshipReview::query()
            ->with([
                'reviewer.user',
                'reviewee.user',
                'mentorship.mentor.user',
                'mentorship.mentee.user'
            ]);
            
        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('overall_comment', 'like', '%' . $this->search . '%')
                  ->orWhereHas('reviewer.user', function ($q) {
                      $q->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                  })
                  ->orWhereHas('reviewee.user', function ($q) {
                      $q->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                  });
            });
        }
        
        // Review type filter
        if ($this->reviewType) {
            $query->where('review_type', $this->reviewType);
        }
        
        // Mentorship filter
        if ($this->mentorshipFilter) {
            $query->where('mentorship_id', $this->mentorshipFilter);
        }
        
        // Rating filter
        if ($this->ratingFilter) {
            switch ($this->ratingFilter) {
                case '5_stars':
                    $query->where('overall_rating', 5);
                    break;
                case '4_plus':
                    $query->where('overall_rating', '>=', 4);
                    break;
                case '3_plus':
                    $query->where('overall_rating', '>=', 3);
                    break;
                case '2_plus':
                    $query->where('overall_rating', '>=', 2);
                    break;
                case '1_plus':
                    $query->where('overall_rating', '>=', 1);
                    break;
                case 'positive':
                    $query->where('overall_rating', '>=', 4);
                    break;
                case 'negative':
                    $query->where('overall_rating', '<=', 2);
                    break;
                case 'neutral':
                    $query->whereBetween('overall_rating', [2.1, 3.9]);
                    break;
            }
        }
        
        // Status filter
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }
        
        // Date range filter
        if ($this->dateRange) {
            switch ($this->dateRange) {
                case 'today':
                    $query->whereDate('published_at', Carbon::today());
                    break;
                case 'this_week':
                    $query->whereBetween('published_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereBetween('published_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
                    break;
                case 'last_7_days':
                    $query->where('published_at', '>=', Carbon::now()->subDays(7));
                    break;
                case 'last_30_days':
                    $query->where('published_at', '>=', Carbon::now()->subDays(30));
                    break;
            }
        }
        
        return $query->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage);
    }
    
    public function getMentorshipsProperty()
    {
        return Mentorship::where('status', 'completed')
            ->with(['mentor.user', 'mentee.user'])
            ->get();
    }
    
    public function getRatingDistributionProperty()
    {
        $distribution = [
            '5_stars' => 0,
            '4_stars' => 0,
            '3_stars' => 0,
            '2_stars' => 0,
            '1_star' => 0,
        ];
        
        $reviews = MentorshipReview::where('status', 'published')
            ->whereNotNull('overall_rating')
            ->get();
            
        foreach ($reviews as $review) {
            $rating = (int) round($review->overall_rating);
            if ($rating >= 1 && $rating <= 5) {
                $key = $rating . '_stars';
                if (isset($distribution[$key])) {
                    $distribution[$key]++;
                }
            }
        }
        
        return $distribution;
    }
    public function render()
    {
        return view('livewire.admin.mentorship.reviews', [
            'reviews' => $this->reviews,
            'mentorships' => $this->mentorships,
            'ratingDistribution' => $this->ratingDistribution,
            'totalReviews' => $this->totalReviews,
            'publishedReviews' => $this->publishedReviews,
            'averageRating' => $this->averageRating,
            'featuredReviews' => $this->featuredReviews,
        ]);
    }
}
