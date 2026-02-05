<?php

namespace App\Livewire\Admin\Layouts;

use App\Services\SidebarService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Sidebar extends Component
{
    public function render()
    {
        $user = Auth::user();
        $sidebarService = app(SidebarService::class);
        $menu = $sidebarService->getSidebarMenu($user);
        $stats = $sidebarService->getStatistics();
        return view('livewire.admin.layouts.sidebar', [
            'menu' => $menu,
            'stats' => $stats,
            'user' => $user,
        ]);
    }

    public function getResourceColor($resource)
    {
        return match($resource) {
            'students' => 'success',
            'employers' => 'primary',
            'mentors' => 'warning',
            'administrators' => 'danger',
            'roles' => 'info',
            'opportunities' => 'warning',
            'applications' => 'info',
            'exchange-programs' => 'purple',
            'mentorships' => 'primary',
            'reports' => 'success',
            'settings' => 'secondary',
            default => 'secondary'
        };
    }

    public function getActionColor($action)
    {
        return match($action) {
            'active', 'verified', 'completed', 'hired' => 'success',
            'pending', 'seeking', 'open' => 'warning',
            'featured', 'available' => 'info',
            'on-attachment', 'ongoing' => 'primary',
            default => 'secondary'
        };
    }

    public function isActive($routeName)
    {
        return request()->routeIs($routeName . '*');
    }
}
