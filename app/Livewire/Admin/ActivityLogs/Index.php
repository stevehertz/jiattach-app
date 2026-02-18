<?php

namespace App\Livewire\Admin\ActivityLogs;

use App\Models\ActivityLog;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    
    public $search = '';
    public $log_name = '';
    public $event = '';
    public $user_id = '';
    public $date_from = '';
    public $date_to = '';
    public $days_to_clear = 30;
    
    public $showClearModal = false;
    
    protected $queryString = [
        'search' => ['except' => ''],
        'log_name' => ['except' => ''],
        'event' => ['except' => ''],
        'user_id' => ['except' => ''],
        'date_from' => ['except' => ''],
        'date_to' => ['except' => ''],
    ];
    public function render()
    {
         $query = ActivityLog::with('causer')->latest();
        
        // Apply filters
        if ($this->log_name) {
            $query->where('log_name', $this->log_name);
        }
        
        if ($this->event) {
            $query->where('event', $this->event);
        }
        
        if ($this->user_id) {
            $query->where('causer_id', $this->user_id);
        }
        
        if ($this->date_from) {
            $query->whereDate('created_at', '>=', $this->date_from);
        }
        
        if ($this->date_to) {
            $query->whereDate('created_at', '<=', $this->date_to);
        }
        
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('description', 'like', "%{$this->search}%")
                  ->orWhere('event', 'like', "%{$this->search}%")
                  ->orWhere('ip_address', 'like', "%{$this->search}%")
                  ->orWhereHas('causer', function ($q) {
                      $q->where('first_name', 'like', "%{$this->search}%")
                        ->orWhere('last_name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                  });
            });
        }
        
        $logs = $query->paginate(25);
        
        // Get filter data
        $logNames = ActivityLog::distinct()->pluck('log_name')->filter()->values();
        $events = ActivityLog::distinct()->pluck('event')->filter()->values();
        $users = User::whereIn('id', ActivityLog::distinct()->pluck('causer_id'))->get();
        
        $todayLogs = ActivityLog::today()->count();
        $totalLogs = ActivityLog::count();
        
        return view('livewire.admin.activity-logs.index',  compact(
            'logs', 
            'logNames', 
            'events', 
            'users',
            'todayLogs',
            'totalLogs'
        ));
    }

     public function applyFilters()
    {
        $this->resetPage();
    }
    
    public function resetFilters()
    {
        $this->reset(['search', 'log_name', 'event', 'user_id', 'date_from', 'date_to']);
        $this->resetPage();
    }
    
    public function clearOldLogs()
    {
        $deleted = ActivityLog::where('created_at', '<', now()->subDays($this->days_to_clear))->delete();
        
        $this->showClearModal = false;
        $this->dispatchBrowserEvent('notify', [
            'type' => 'success',
            'message' => "Successfully cleared {$deleted} logs older than {$this->days_to_clear} days."
        ]);
    }
    
    public function exportLogs()
    {
        $logs = ActivityLog::with('causer')->latest()->limit(1000)->get();
        
        $csvContent = "ID,Description,Event,User,IP Address,Date\n";
        
        foreach ($logs as $log) {
            $csvContent .= implode(',', [
                $log->id,
                '"' . str_replace('"', '""', $log->description) . '"',
                $log->event ?? 'N/A',
                $log->causer ? '"' . str_replace('"', '""', $log->causer->full_name) . '"' : 'System',
                $log->ip_address ?? 'N/A',
                $log->created_at->format('Y-m-d H:i:s')
            ]) . "\n";
        }
        
        return response()->streamDownload(function () use ($csvContent) {
            echo $csvContent;
        }, 'activity-logs-' . now()->format('Y-m-d') . '.csv');
    }
}
