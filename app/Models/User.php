<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Traits\LogsModelActivity;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Jetstream\HasProfilePhoto;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use LogsModelActivity;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'profile',
        'national_id',
        'date_of_birth',
        'gender',
        'county',
        'constituency',
        'ward',
        'bio',
        'disability_status',
        'disability_details',
        'is_active',
        'is_verified',
        'verification_token',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
        'full_name'
    ];

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->full_name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Check if user has disclosed disability status.
     */
    public function hasDisability(): bool
    {
        return $this->disability_status &&
            $this->disability_status !== 'none' &&
            $this->disability_status !== 'prefer_not_to_say';
    }

    /**
     * Get disability status label.
     */
    public function getDisabilityStatusLabelAttribute()
    {
        $labels = [
            'none' => 'No Disability',
            'mobility' => 'Mobility Impairment',
            'visual' => 'Visual Impairment',
            'hearing' => 'Hearing Impairment',
            'cognitive' => 'Cognitive Impairment',
            'other' => 'Other Disability',
            'prefer_not_to_say' => 'Prefer Not to Say',
        ];

        return $labels[$this->disability_status] ?? 'Not Specified';
    }

    /**
     * Relationship with student profile.
     */
    public function studentProfile()
    {
        return $this->hasOne(StudentProfile::class);
    }

    /**
     * Relationship with organization (for company users).
     */
    public function organization()
    {
        return $this->hasOne(Organization::class);
    }

    /**
     * Get the placements for this user (as a student).
     */
    public function placements()
    {
        return $this->hasMany(Placement::class, 'student_id');
    }

    /**
     * Get the placements assigned to this user (as an admin).
     */
    public function assignedPlacements()
    {
        return $this->hasMany(Placement::class, 'admin_id');
    }

    /**
     * Get the applications (system matches) for this user.
     */
    public function applications()
    {
        return $this->hasMany(Application::class, 'user_id');
    }

    /**
     * Get the latest active application/match.
     */
    public function latestApplication()
    {
        return $this->hasOne(Application::class, 'user_id')->latestOfMany();
    }

    /**
     * Get mentorships where user is a mentee.
     */
    public function mentorshipsAsMentee()
    {
        return $this->hasMany(Mentorship::class, 'mentee_id');
    }

    /**
     * Get the matches for this student.
     */
    // public function matches()
    // {
    //     return $this->hasMany(Match::class, 'student_id');
    // }

    /**
     * Get pending matches that haven't been reviewed.
     */
    // public function pendingMatches()
    // {
    //     return $this->hasMany(Match::class, 'student_id')
    //         ->where('status', 'pending')
    //         ->with('opportunity.organization');
    // }

    /**
     * Get the current active placement.
     */
    public function currentPlacement()
    {
        return $this->hasOne(Placement::class, 'student_id')
            ->where('status', 'placed')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->latest();
    }

    /**
     * Get the latest placement.
     */
    public function latestPlacement()
    {
        return $this->hasOne(Placement::class, 'student_id')->latest();
    }

    /**
     * Check if user has an active placement.
     */
    public function hasActivePlacement(): bool
    {
        return $this->placements()
            ->where('status', 'placed')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->exists();
    }

    /**
     * Check if user can receive new matches.
     */
    public function canReceiveMatches(): bool
    {
        // Can't receive matches if already placed or has pending accepted matches
        if ($this->hasActivePlacement()) {
            return false;
        }

        // Check if student profile is complete
        if ($this->hasRole('student') && !$this->studentProfile?->profile_completed) {
            return false;
        }

        // Check if there's a pending accepted match
        $hasAcceptedMatch = $this->matches()
            ->where('status', 'accepted')
            ->whereHas('placement', function ($query) {
                $query->whereIn('status', ['pending', 'processing', 'placed']);
            })
            ->exists();

        return !$hasAcceptedMatch;
    }

    /**
     * Get the placement status.
     */
    public function getPlacementStatusAttribute()
    {
        $placement = $this->latestPlacement;
        return $placement ? $placement->status : 'seeking';
    }

    /**
     * Get the placement status label.
     */
    public function getPlacementStatusLabelAttribute()
    {
        $placement = $this->latestPlacement;
        return $placement ? $placement->status_label : 'Seeking Placement';
    }

    /**
     * Scope to get only students.
     */
    public function scopeStudents($query)
    {
        return $query->role('student');
    }

    /**
     * Scope to get only companies.
     */
    public function scopeCompanies($query)
    {
        return $query->role('company');
    }

    /**
     * Scope to get verified users.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope to get active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Determine the landing page based on role.
     */
    public function getDashboardRoute(): string
    {
        return match (true) {
            $this->hasRole('student') => 'student.dashboard',
            default => 'admin.dashboard',
        };
    }

    /**
     * Check if the user's specific profile (Student or Org) is complete.
     */
    public function isProfileComplete(): bool
    {
        if ($this->hasRole('student')) {
            return $this->studentProfile?->profile_completeness === 100;
        }
        return true;
    }

    /**
     * Get the login attempts for the user.
     */
    public function loginAttempts()
    {
        return $this->hasMany(LoginAttempt::class, 'email', 'email');
    }

    /**
     * Get the activity logs for the user.
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class, 'causer_id');
    }

    /**
     * Check if user has two-factor authentication enabled.
     */
    public function hasTwoFactorEnabled()
    {
        return $this->twoFactorSetting && $this->twoFactorSetting->is_enabled;
    }

    /**
     * Get the two-factor authentication setting for the user.
     */
    public function twoFactorSetting()
    {
        return $this->hasOne(UserTwoFactorSetting::class);
    }
}
