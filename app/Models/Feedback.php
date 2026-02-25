<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'user_id',
        'type',
        'message',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

     /**
     * Get the application that this feedback belongs to.
     */
    public function application()
    {
        return $this->belongsTo(Application::class);
    }

      /**
     * Get the user who sent this feedback.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

     /**
     * Get feedback type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'general' => 'General Feedback',
            'interview' => 'Interview Feedback',
            'offer' => 'Offer Related',
            'rejection' => 'Rejection Reason',
            default => ucfirst($this->type),
        };
    }

     /**
     * Get feedback type color.
     */
    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'general' => 'info',
            'interview' => 'primary',
            'offer' => 'success',
            'rejection' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get feedback type icon.
     */
    public function getTypeIconAttribute(): string
    {
        return match($this->type) {
            'general' => 'fas fa-comment',
            'interview' => 'fas fa-calendar-alt',
            'offer' => 'fas fa-handshake',
            'rejection' => 'fas fa-times-circle',
            default => 'fas fa-info-circle',
        };
    }
}

