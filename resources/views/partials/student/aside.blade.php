<aside class="main-sidebar sidebar-light-success elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('student.dashboard') }}" class="brand-link bg-success">
        <img src="{{ asset('img/logo-icon.png') }}" alt="{{ config('app.name') }}"
            class="brand-image brand-primary img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">
            {{ config('app.name') }}
        </span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                @php
                    $initials = getInitials(Auth::user()->full_name);
                    $colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
                    $color = $colors[crc32(Auth::user()->email) % count($colors)];
                @endphp
                <div class="avatar-initials bg-{{ $color }} img-circle elevation-2"
                     style="width: 45px; height: 45px; line-height: 45px; text-align: center; color: white; font-weight: bold; font-size: 16px;">
                    {{ $initials }}
                </div>
            </div>
            <div class="info">
                <a href="#" class="d-block">
                    {{ Auth::user()->full_name }}
                </a>
                <small class="text-muted">
                    Student
                    @if(Auth::user()->studentProfile)
                        • {{ Auth::user()->studentProfile->institution_name }}
                    @endif
                </small>
                @php
                    $placement = Auth::user()->placements()->latest()->first();
                @endphp
                @if($placement && $placement->status == 'placed')
                    <small class="text-success d-block mt-1">
                        <i class="fas fa-briefcase mr-1"></i>
                        Placed at {{ $placement->organization->name ?? 'Organization' }}
                    </small>
                @endif
            </div>
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search dashboard..."
                    aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('student.dashboard') }}"
                       class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}"
                       wire:navigate>
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- Profile -->
                <li class="nav-item">
                    <a href="{{ route('student.profile.show') }}"
                       class="nav-link {{ request()->routeIs('student.profile*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user"></i>
                        <p>My Profile</p>
                        @php
                            $profileCompleteness = Auth::user()->studentProfile->profile_completeness ?? 0;
                        @endphp
                        @if($profileCompleteness < 80)
                            <span class="badge badge-warning right">{{ $profileCompleteness }}%</span>
                        @endif
                    </a>
                </li>

                <!-- PLACEMENT SECTION (Updated) -->
                <li class="nav-header">PLACEMENT</li>

                <li class="nav-item">
                    <a href="{{ route('student.placement.status') }}"
                       class="nav-link {{ request()->routeIs('student.placement.status') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-briefcase"></i>
                        <p>My Placement</p>
                        @if($placement)
                            @php
                                $badgeClass = match($placement->status) {
                                    'placed' => 'badge-success',
                                    'processing' => 'badge-warning',
                                    'pending' => 'badge-secondary',
                                    default => 'badge-info'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }} right">
                                {{ ucfirst($placement->status) }}
                            </span>
                        @else
                            <span class="badge badge-secondary right">Pending</span>
                        @endif
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('student.placement.timeline') }}"
                       class="nav-link {{ request()->routeIs('student.placement.timeline') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-road"></i>
                        <p>Placement Journey</p>
                    </a>
                </li>

                <!-- REMOVED OPPORTUNITIES SECTION (No applications) -->
                <!-- <li class="nav-header">OPPORTUNITIES</li> -->

                <!-- MENTORSHIP SECTION (Keep as is) -->
                <li class="nav-header">MENTORSHIP</li>

                <li class="nav-item">
                    <a href="{{ route('student.mentorship.index') }}"
                       class="nav-link {{ request()->routeIs('student.mentorship.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-hands-helping"></i>
                        <p>My Mentors</p>
                        @php
                            $activeMentorships = \App\Models\Mentorship::where('mentee_id', Auth::id())
                                ->where('status', 'active')
                                ->count();
                        @endphp
                        @if($activeMentorships > 0)
                            <span class="badge badge-primary right">{{ $activeMentorships }}</span>
                        @endif
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('student.mentorship.find') }}"
                       class="nav-link {{ request()->routeIs('student.mentorship.find') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-search"></i>
                        <p>Find a Mentor</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('student.mentorship.sessions') }}"
                       class="nav-link {{ request()->routeIs('student.mentorship.sessions') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>Upcoming Sessions</p>
                    </a>
                </li>

                <!-- DOCUMENTS & RESOURCES -->
                <li class="nav-header">DOCUMENTS</li>

                <li class="nav-item">
                    <a href="{{ route('student.documents.index') }}"
                       class="nav-link {{ request()->routeIs('student.documents*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-folder"></i>
                        <p>My Documents</p>
                        @php
                            $docCount = 0;
                            if (Auth::user()->studentProfile) {
                                if (Auth::user()->studentProfile->cv_url) $docCount++;
                                if (Auth::user()->studentProfile->transcript_url) $docCount++;
                            }
                        @endphp
                        @if($docCount > 0)
                            <span class="badge badge-secondary right">{{ $docCount }}</span>
                        @endif
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('student.cv.templates.index') }}"
                       class="nav-link {{ request()->routeIs('student.cv.templates*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-download"></i>
                        <p>CV Templates</p>
                    </a>
                </li>

                <!-- ACTIVITY & NOTIFICATIONS -->
                <li class="nav-header">ACTIVITY</li>

                <li class="nav-item">
                    <a href="{{ route('student.notifications') }}"
                       class="nav-link {{ request()->routeIs('student.notifications') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-bell"></i>
                        <p>Notifications</p>
                        @php
                            $unreadNotifications = Auth::user()->unreadNotifications()
                                ->where('type', 'App\Notifications\PlacementNotification')
                                ->count();
                        @endphp
                        @if($unreadNotifications > 0)
                            <span class="badge badge-danger right">{{ $unreadNotifications }}</span>
                        @endif
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('student.activity') }}"
                       class="nav-link {{ request()->routeIs('student.activity') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-history"></i>
                        <p>Activity Log</p>
                    </a>
                </li>

                <!-- SUPPORT SECTION -->
                <li class="nav-header">SUPPORT</li>

                <li class="nav-item">
                    <a href="#"
                       class="nav-link {{ request()->routeIs('student.faq') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-question-circle"></i>
                        <p>FAQ & Help</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#"
                       class="nav-link {{ request()->routeIs('student.settings') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>Settings</p>
                    </a>
                </li>

                <!-- QUICK LINKS -->
                <li class="nav-header mt-4">QUICK LINKS</li>

                <li class="nav-item">
                    <a href="{{ route('home') }}" target="_blank" class="nav-link">
                        <i class="nav-icon fas fa-external-link-alt"></i>
                        <p>View Public Site</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('student.placement.request') }}" class="nav-link bg-gradient-success">
                        <i class="nav-icon fas fa-bullhorn"></i>
                        <p>Request Placement</p>
                    </a>
                </li>

                <!-- EMERGENCY CONTACT -->
                <li class="nav-header mt-3">SUPPORT CONTACT</li>
                <li class="nav-item">
                    <div class="nav-link text-center">
                        <small class="text-muted">
                            <i class="fas fa-headset mr-1"></i>
                            Placement Support<br>
                            <a href="mailto:placement@jiattach.co.ke" class="text-primary">
                                <i class="fas fa-envelope mr-1"></i>
                                placement@jiattach.co.ke
                            </a><br>
                            <i class="fas fa-phone mr-1"></i>
                            +254 700 123 456
                        </small>
                    </div>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
