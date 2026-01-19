<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
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
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // Relationship with student profile
    public function studentProfile()
    {
        return $this->hasOne(StudentProfile::class);
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
     * Get the placement status.
     */
    public function getPlacementStatusAttribute()
    {
        $placement = $this->latestPlacement;
        return $placement ? $placement->status : 'pending';
    }

    /**
     * Get the placement status label.
     */
    public function getPlacementStatusLabelAttribute()
    {
        $placement = $this->latestPlacement;
        return $placement ? $placement->status_label : 'Not Applied';
    }

}
