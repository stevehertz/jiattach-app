<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BackupHistory extends Model
{
    use HasFactory;

     protected $fillable = [
        'file_name',
        'file_path',
        'file_size',
        'disk',
        'status',
        'error_message',
        'metadata',
        'created_by',
        'completed_at'
    ];

    protected $casts = [
        'metadata' => 'array',
        'completed_at' => 'datetime'
    ];

    /**
     * Get the user who created the backup
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get formatted file size
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = (int) $this->file_size;
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'completed' => 'badge-success',
            'failed' => 'badge-danger',
            'in_progress' => 'badge-warning',
            default => 'badge-secondary'
        };
    }

    /**
     * Scope completed backups
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope failed backups
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope recent backups
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
