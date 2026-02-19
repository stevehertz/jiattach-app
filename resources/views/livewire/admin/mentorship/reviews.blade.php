<div>
    {{-- If you look to others for fulfillment, you will never truly be fulfilled. --}}

    <section class="content">
        <div class="container-fluid">
            <!-- Statistics -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $totalReviews }}</h3>
                            <p>Total Reviews</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            View All <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $publishedReviews }}</h3>
                            <p>Published Reviews</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            View Published <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $averageRating }}/5</h3>
                            <p>Average Rating</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            View Ratings <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>{{ $featuredReviews }}</h3>
                            <p>Featured Reviews</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-award"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            View Featured <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Rating Distribution -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Rating Distribution</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @php
                                    $totalRated = array_sum($ratingDistribution);
                                @endphp
                                @foreach ($ratingDistribution as $stars => $count)
                                    @php
                                        $starCount = (int) str_replace('_stars', '', $stars);
                                        $percentage = $totalRated > 0 ? round(($count / $totalRated) * 100, 1) : 0;
                                        $colorClass =
                                            [
                                                '5_stars' => 'bg-success',
                                                '4_stars' => 'bg-info',
                                                '3_stars' => 'bg-warning',
                                                '2_stars' => 'bg-orange',
                                                '1_star' => 'bg-danger',
                                            ][$stars] ?? 'bg-secondary';
                                    @endphp
                                    <div class="col-md-2 col-sm-4 col-6 text-center mb-3">
                                        <div class="mb-1">
                                            {!! str_repeat('<i class="fas fa-star text-warning"></i>', $starCount) !!}
                                            {!! str_repeat('<i class="far fa-star text-warning"></i>', 5 - $starCount) !!}
                                        </div>
                                        <div class="h4 mb-1">{{ $count }}</div>
                                        <div class="text-muted">reviews</div>
                                        <div class="progress mt-2">
                                            <div class="progress-bar {{ $colorClass }}"
                                                style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ $percentage }}%</small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Filter Reviews & Ratings</h3>
                    <div class="card-tools">
                        @if (count($selectedReviews) > 0)
                            <div class="btn-group">
                                <button type="button" class="btn btn-danger btn-sm"
                                    onclick="confirm('Delete selected reviews?') || event.stopImmediatePropagation()"
                                    wire:click="deleteSelected">
                                    <i class="fas fa-trash"></i> Delete Selected ({{ count($selectedReviews) }})
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                                    placeholder="Search reviews...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <select wire:model.live="reviewType" class="form-control">
                                    <option value="">All Types</option>
                                    <option value="mentor_to_mentee">Mentor → Mentee</option>
                                    <option value="mentee_to_mentor">Mentee → Mentor</option>
                                    <option value="mutual">Mutual</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <select wire:model.live="mentorshipFilter" class="form-control">
                                    <option value="">All Mentorships</option>
                                    @foreach ($mentorships as $mentorship)
                                        <option value="{{ $mentorship->id }}">
                                            {{ $mentorship->title }} ({{ $mentorship->mentor->user->first_name }} →
                                            {{ $mentorship->mentee->user->first_name }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <select wire:model.live="ratingFilter" class="form-control">
                                    <option value="">All Ratings</option>
                                    <option value="5_stars">5 Stars</option>
                                    <option value="4_plus">4+ Stars</option>
                                    <option value="3_plus">3+ Stars</option>
                                    <option value="positive">Positive (4-5)</option>
                                    <option value="neutral">Neutral (3)</option>
                                    <option value="negative">Negative (1-2)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <select wire:model.live="statusFilter" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="draft">Draft</option>
                                    <option value="submitted">Submitted</option>
                                    <option value="published">Published</option>
                                    <option value="flagged">Flagged</option>
                                    <option value="hidden">Hidden</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <select wire:model.live="perPage" class="form-control">
                                    <option value="10">10 per page</option>
                                    <option value="15">15 per page</option>
                                    <option value="25">25 per page</option>
                                    <option value="50">50 per page</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reviews Table -->
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="50">
                                        <div class="icheck-primary">
                                            <input type="checkbox" wire:model="selectAll" id="selectAll">
                                            <label for="selectAll"></label>
                                        </div>
                                    </th>
                                    <th wire:click="sortBy('published_at')" style="cursor: pointer;">
                                        Date
                                        @if ($sortBy === 'published_at')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fas fa-sort"></i>
                                        @endif
                                    </th>
                                    <th>Review</th>
                                    <th>Rating</th>
                                    <th>Mentorship</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reviews as $review)
                                    @php
                                        $sentimentClass = match (true) {
                                            $review->overall_rating >= 4 => 'text-success',
                                            $review->overall_rating <= 2 => 'text-danger',
                                            default => 'text-warning',
                                        };
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="icheck-primary">
                                                <input type="checkbox" wire:model="selectedReviews"
                                                    value="{{ $review->id }}" id="review_{{ $review->id }}">
                                                <label for="review_{{ $review->id }}"></label>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($review->published_at)
                                                {{ formatDate($review->published_at) }}
                                                <br>
                                                <small class="text-muted">
                                                    {{ $review->published_at->diffForHumans() }}
                                                </small>
                                            @else
                                                <span class="text-muted">Not published</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <div class="mr-3">
                                                    {!! getUserAvatar($review->reviewer->user, 40) !!}
                                                </div>
                                                <div>
                                                    <strong>{{ $review->reviewer_name }}</strong>
                                                    @if ($review->is_anonymous)
                                                        <small class="badge badge-secondary">Anonymous</small>
                                                    @endif
                                                    <br>
                                                    <small class="text-muted">
                                                        reviewed
                                                        <strong>{{ $review->reviewee_name }}</strong>
                                                    </small>
                                                    <div class="mt-1">
                                                        <small class="{{ $sentimentClass }}">
                                                            {{ $review->review_summary }}
                                                        </small>
                                                    </div>
                                                    @if ($review->has_response)
                                                        <div class="mt-1">
                                                            <small class="text-info">
                                                                <i class="fas fa-reply"></i> Has response
                                                            </small>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <div class="h3 mb-0 {{ $sentimentClass }}">
                                                    {{ number_format($review->overall_rating, 1) }}
                                                </div>
                                                <div class="text-warning">
                                                    {!! getRatingStars($review->overall_rating) !!}
                                                </div>
                                                <small class="text-muted">/5.0</small>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($review->mentorship)
                                                <div>
                                                    <strong>{{ $review->mentorship->title }}</strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ $review->mentorship->mentor->user->first_name }} →
                                                        {{ $review->mentorship->mentee->user->first_name }}
                                                    </small>
                                                </div>
                                            @else
                                                <span class="text-danger">Mentorship not found</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span
                                                class="badge 
                                                @if ($review->review_type === 'mentee_to_mentor') badge-info
                                                @elseif($review->review_type === 'mentor_to_mentee') badge-primary
                                                @else badge-secondary @endif">
                                                {{ $review->review_type_label }}
                                            </span>
                                            @if ($review->is_session_review)
                                                <br>
                                                <small class="badge badge-warning">Session Review</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($review->status === 'published')
                                                <span class="badge badge-success">Published</span>
                                                @if ($review->is_featured)
                                                    <br>
                                                    <small class="badge badge-warning">Featured</small>
                                                @endif
                                            @elseif($review->status === 'draft')
                                                <span class="badge badge-secondary">Draft</span>
                                            @elseif($review->status === 'submitted')
                                                <span class="badge badge-info">Submitted</span>
                                            @elseif($review->status === 'flagged')
                                                <span class="badge badge-danger">Flagged</span>
                                            @elseif($review->status === 'hidden')
                                                <span class="badge badge-dark">Hidden</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-info btn-sm"
                                                    title="View Details"
                                                    onclick="window.location='{{ route('admin.mentorships.show', $review->mentorship_id) }}?review={{ $review->id }}'">
                                                    <i class="fas fa-eye"></i>
                                                </button>

                                                @if ($review->status === 'submitted' || $review->status === 'draft')
                                                    <button type="button" class="btn btn-success btn-sm"
                                                        wire:click="publishReview({{ $review->id }})"
                                                        title="Publish Review">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @endif

                                                @if ($review->status === 'published')
                                                    <button type="button" class="btn btn-warning btn-sm"
                                                        wire:click="unpublishReview({{ $review->id }})"
                                                        title="Unpublish">
                                                        <i class="fas fa-times"></i>
                                                    </button>

                                                    @if ($review->is_featured)
                                                        <button type="button" class="btn btn-secondary btn-sm"
                                                            wire:click="unfeatureReview({{ $review->id }})"
                                                            title="Remove Featured">
                                                            <i class="fas fa-star"></i>
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-warning btn-sm"
                                                            wire:click="featureReview({{ $review->id }})"
                                                            title="Feature Review">
                                                            <i class="fas fa-award"></i>
                                                        </button>
                                                    @endif

                                                    @if ($review->status !== 'flagged')
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            wire:click="flagReview({{ $review->id }})"
                                                            title="Flag Review">
                                                            <i class="fas fa-flag"></i>
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-success btn-sm"
                                                            wire:click="unflagReview({{ $review->id }})"
                                                            title="Unflag Review">
                                                            <i class="fas fa-flag-checkered"></i>
                                                        </button>
                                                    @endif

                                                    @if (!$review->has_response)
                                                        <button type="button" class="btn btn-primary btn-sm"
                                                            wire:click="openResponseModal({{ $review->id }})"
                                                            title="Add Response">
                                                            <i class="fas fa-reply"></i>
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-star fa-3x mb-3"></i>
                                                <h4>No Reviews Found</h4>
                                                <p>There are no reviews matching your filters.</p>
                                                <button type="button" class="btn btn-primary"
                                                    wire:click="$set('search', '')">
                                                    <i class="fas fa-times"></i> Clear Filters
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer clearfix">
                    <div class="float-left">
                        <span class="text-muted">
                            Showing {{ $reviews->firstItem() ?? 0 }} to {{ $reviews->lastItem() ?? 0 }}
                            of {{ $reviews->total() }} entries
                        </span>
                    </div>
                    <div class="float-right">
                        {{ $reviews->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Response Modal -->
    @if ($showResponseModal)
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1"
            role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Response to Review</h5>
                        <button type="button" class="close" wire:click="closeResponseModal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="responseText">Response</label>
                            <textarea class="form-control" id="responseText" wire:model="responseText" rows="6"
                                placeholder="Enter your response to this review..."></textarea>
                            @error('responseText')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> This response will be visible to both the reviewer and
                            other users viewing this review.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            wire:click="closeResponseModal">Cancel</button>
                        <button type="button" class="btn btn-primary" wire:click="saveResponse">Save
                            Response</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
