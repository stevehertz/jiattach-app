<?php

namespace App\Models;

use App\Traits\LogsModelActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Placement extends Model
{
    use HasFactory, SoftDeletes, LogsModelActivity;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'admin_id',
        'organization_id',
        'status',
        'notes',
        'start_date',
        'end_date',
        'duration_days',
        'department',
        'supervisor_name',
        'supervisor_contact',
        'requirements',
        'stipend',
        'admin_notified_at',
        'student_notified_at',
        'placement_confirmed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'requirements' => 'array',
        'stipend' => 'decimal:2',
        'admin_notified_at' => 'datetime',
        'student_notified_at' => 'datetime',
        'placement_confirmed_at' => 'datetime',
    ];

     /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'status_label',
        'duration_months',
        'is_active',
        'remaining_days',
        'progress_percentage',
    ];

}
