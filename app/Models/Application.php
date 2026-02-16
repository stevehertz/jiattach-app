<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Application extends Model
{
    use HasFactory, SoftDeletes;

     protected $fillable = [
        'user_id',
        'student_id',
        'attachment_opportunity_id',
        'match_score',
        'cover_letter',
        'submitted_at',
        'status', // pending, reviewing, shortlisted, offered, accepted, rejected
        'employer_notes',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'match_score' => 'float',
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function opportunity()
    {
        return $this->belongsTo(AttachmentOpportunity::class, 'attachment_opportunity_id');
    }

    public function placement()
    {
        return $this->hasOne(Placement::class);
    }

    // Status Helper
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'accepted' => 'success',
            'offered' => 'primary',
            'shortlisted' => 'info',
            'reviewing' => 'warning',
            'rejected' => 'danger',
            default => 'secondary'
        };
    }
}
