@props(['user' => null])

@php
    // Optimized helper for active routes
    $isRouteActive = fn($routes) => request()->routeIs(is_array($routes) ? $routes : [$routes]);

    $user = $user ?? auth()->user();
    $initials = getInitials($user->full_name ?? 'User');
    $colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
    $color = $colors[crc32($user->email ?? 'guest') % count($colors)];
@endphp

<aside class="main-sidebar sidebar-light-success elevation-4">
    <a href="{{ route('admin.dashboard') }}" class="brand-link bg-success">
        <img src="{{ asset('img/logo-icon.png') }}" alt="{{ config('app.name') }}"
            class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">{{ config('app.name') }}</span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <div class="avatar-initials bg-{{ $color }} img-circle elevation-2"
                    style="width: 45px; height: 45px; line-height: 45px; text-align: center; color: white; font-weight: bold; font-size: 16px;">
                    {{ $initials }}
                </div>
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ $user->full_name ?? 'Administrator' }}</a>
            </div>
        </div>

        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search menu...">
                <div class="input-group-append">
                    <button class="btn btn-sidebar"><i class="fas fa-search fa-fw"></i></button>
                </div>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu"
                data-accordion="false">

                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}"
                        class="nav-link {{ $isRouteActive('admin.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-header">USER MANAGEMENT</li>

                <li class="nav-item">
                    <a href="{{ route('admin.users.index') }}"
                        class="nav-link {{ $isRouteActive('admin.users.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users-cog"></i>
                        <p>All System Users</p>
                    </a>
                </li>

                <li class="nav-item {{ $isRouteActive('admin.administrators.*') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ $isRouteActive('admin.administrators.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-shield"></i>
                        <p>Administrators <i class="fas fa-angle-left right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.administrators.index') }}"
                                class="nav-link {{ $isRouteActive('admin.administrators.index') ? 'active' : '' }}">
                                <i
                                    class="far {{ $isRouteActive('admin.administrators.index') ? 'fa-dot-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Manage Staff</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.administrators.create') }}"
                                class="nav-link {{ $isRouteActive('admin.administrators.create') ? 'active' : '' }}">
                                <i
                                    class="far {{ $isRouteActive('admin.administrators.create') ? 'fa-dot-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Add New Staff</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- MENTORS SECTION -->
                <li class="nav-item {{ $isRouteActive('admin.mentors.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ $isRouteActive('admin.mentors.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chalkboard-teacher"></i>
                        <p>Mentors <i class="fas fa-angle-left right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.mentors.index') }}"
                                class="nav-link {{ $isRouteActive('admin.mentors.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Mentors</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.mentors.verified') }}"
                                class="nav-link {{ $isRouteActive('admin.mentors.verified') ? 'active' : '' }}">
                                <i class="far fa-check-circle nav-icon text-success"></i>
                                <p>Verified Mentors</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.mentors.featured') }}"
                                class="nav-link {{ $isRouteActive('admin.mentors.featured') ? 'active' : '' }}">
                                <i class="far fa-star nav-icon text-warning"></i>
                                <p>Featured Mentors</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.mentors.available') }}"
                                class="nav-link {{ $isRouteActive('admin.mentors.available') ? 'active' : '' }}">
                                <i class="far fa-clock nav-icon text-info"></i>
                                <p>Available Now</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.mentors.create') }}"
                                class="nav-link {{ $isRouteActive('admin.mentors.create') ? 'active' : '' }}">
                                <i class="far fa-plus-square nav-icon"></i>
                                <p>Add New Mentor</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- MENTORSHIPS SECTION (Active Relationships) -->
                <li class="nav-item {{ $isRouteActive('admin.mentorships.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ $isRouteActive('admin.mentorships.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-handshake"></i>
                        <p>Mentorships <i class="fas fa-angle-left right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.mentorships.index') }}"
                                class="nav-link {{ $isRouteActive('admin.mentorships.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Mentorships</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.mentorships.active') }}"
                                class="nav-link {{ $isRouteActive('admin.mentorships.active') ? 'active' : '' }}">
                                <i class="far fa-play-circle nav-icon text-success"></i>
                                <p>Active Mentorships</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.mentorships.upcoming.sessions') }}"
                                class="nav-link {{ $isRouteActive('admin.mentorships.upcoming.sessions') ? 'active' : '' }}">
                                <i class="far fa-calendar-alt nav-icon text-info"></i>
                                <p>Upcoming Sessions</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.mentorships.completed') }}"
                                class="nav-link {{ $isRouteActive('admin.mentorships.completed') ? 'active' : '' }}">
                                <i class="far fa-check-circle nav-icon text-secondary"></i>
                                <p>Completed</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.mentorships.reviews') }}"
                                class="nav-link {{ $isRouteActive('admin.mentorships.reviews') ? 'active' : '' }}">
                                <i class="far fa-star nav-icon text-warning"></i>
                                <p>Reviews & Ratings</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.mentorships.create') }}"
                                class="nav-link {{ $isRouteActive('admin.mentorships.create') ? 'active' : '' }}">
                                <i class="far fa-plus-square nav-icon"></i>
                                <p>Create New</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item {{ $isRouteActive('admin.students.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ $isRouteActive('admin.students.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-graduate"></i>
                        <p>Student Tracker <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.students.index') }}"
                                class="nav-link {{ $isRouteActive('admin.students.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Students</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.students.seeking') }}"
                                class="nav-link {{ $isRouteActive('admin.students.seeking') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon text-warning"></i>
                                <p>Seeking Attachment</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.students.on-attachment') }}"
                                class="nav-link {{ $isRouteActive('admin.students.on-attachment') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon text-success"></i>
                                <p>Placed Students</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item {{ $isRouteActive('admin.organizations.*') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ $isRouteActive('admin.organizations.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-building"></i>
                        <p>
                            Organizations
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.organizations.index') }}"
                                class="nav-link {{ $isRouteActive('admin.organizations.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Organizations</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.organizations.create') }}"
                                class="nav-link {{ $isRouteActive('admin.organizations.create') ? 'active' : '' }}">
                                <i class="far fa-plus-square nav-icon"></i>
                                <p>Add New</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-header">OPPORTUNITIES & PLACEMENTS</li>

                <li class="nav-item {{ $isRouteActive('admin.opportunities.*') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ $isRouteActive('admin.opportunities.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-briefcase"></i>
                        <p>Opportunities <i class="fas fa-angle-left right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.opportunities.index') }}"
                                class="nav-link {{ $isRouteActive('admin.opportunities.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>View All</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.opportunities.pending') }}"
                                class="nav-link {{ $isRouteActive('admin.opportunities.pending') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon text-danger"></i>
                                <p>Pending Review</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.opportunities.create') }}"
                                class="nav-link {{ $isRouteActive('admin.opportunities.create') ? 'active' : '' }}">
                                <i class="far fa-plus-square nav-icon"></i>
                                <p>Post New</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- APPLICATIONS SECTION -->
                <li class="nav-item {{ $isRouteActive('admin.applications.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ $isRouteActive('admin.applications.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-signature"></i>
                        <p>Applications <i class="fas fa-angle-left right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.applications.index') }}"
                                class="nav-link {{ $isRouteActive('admin.applications.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Applications</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.applications.pending') }}"
                                class="nav-link {{ $isRouteActive('admin.applications.pending') ? 'active' : '' }}">
                                <i class="far fa-clock nav-icon text-warning"></i>
                                <p>Pending Review</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.applications.interviewing') }}"
                                class="nav-link {{ $isRouteActive('admin.applications.interviewing') ? 'active' : '' }}">
                                <i class="far fa-calendar-check nav-icon text-info"></i>
                                <p>Interview Stage</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.applications.offers') }}"
                                class="nav-link {{ $isRouteActive('admin.applications.offers') ? 'active' : '' }}">
                                <i class="far fa-check-circle nav-icon text-success"></i>
                                <p>Offer Stage</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.applications.hired') }}"
                                class="nav-link {{ $isRouteActive('admin.applications.hired') ? 'active' : '' }}">
                                <i class="far fa-user-check nav-icon text-success"></i>
                                <p>Hired/Placed</p>
                            </a>
                        </li>
                        <li class="nav-divider"></li>
                        <li class="nav-item">
                            <a href="{{ route('admin.applications.analytics') }}"
                                class="nav-link {{ $isRouteActive('admin.applications.analytics') ? 'active' : '' }}">
                                <i class="far fa-chart-bar nav-icon"></i>
                                <p>Analytics</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item {{ $isRouteActive('admin.placements.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ $isRouteActive('admin.placements.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-check-double"></i>
                        <p>Placements <i class="fas fa-angle-left right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.placements.index') }}"
                                class="nav-link {{ $isRouteActive('admin.placements.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Placements</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-header">REPORTS & ANALYTICS</li>

                <li class="nav-item">
                    <a href="{{ route('admin.reports.analytics-dashboard') }}"
                        class="nav-link {{ $isRouteActive('admin.reports.analytics-dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>Analytics Dashboard</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.reports.placement-reports') }}"
                        class="nav-link {{ $isRouteActive('admin.reports.placement-reports') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>Placements Report</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.reports.user-statistics') }}"
                        class="nav-link {{ $isRouteActive('admin.reports.user-statistics') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>User Statistics</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.reports.opportunity-analytics') }}"
                        class="nav-link {{ $isRouteActive('admin.reports.opportunity-analytics') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-pie"></i>
                        <p>Opportunity Analytics</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.reports.application-reports') }}"
                        class="nav-link {{ $isRouteActive('admin.reports.application-reports') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-clipboard-list"></i>
                        <p>Application Reports</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.reports.financial-reports') }}"
                        class="nav-link {{ $isRouteActive('admin.reports.financial-reports') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                        <p>Financial Report</p>
                    </a>
                </li>

                <li class="nav-header">SETTINGS</li>

                <li class="nav-item">
                    <a href="{{ route('admin.settings.general') }}"
                        class="nav-link {{ $isRouteActive('admin.settings.general') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>General Settings</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.settings.email') }}"
                        class="nav-link {{ $isRouteActive('admin.settings.email') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-envelope"></i>
                        <p>Email Settings</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.settings.payment') }}"
                        class="nav-link {{ $isRouteActive('admin.settings.payment') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-credit-card"></i>
                        <p>Payment Settings</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.settings.notifications') }}"
                        class="nav-link {{ $isRouteActive('admin.settings.notifications') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-bell"></i>
                        <p>Notifications</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.settings.security') }}"
                        class="nav-link {{ $isRouteActive('admin.settings.security') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-shield-alt"></i>
                        <p>Security</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.settings.backup') }}"
                        class="nav-link {{ $isRouteActive('admin.settings.backup') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-database"></i>
                        <p>Backup</p>
                    </a>
                </li>

                <li class="nav-header">SYSTEM UTILITY</li>

                <li class="nav-item">
                    <a href="{{ route('admin.activity-logs') }}"
                        class="nav-link {{ $isRouteActive('admin.activity-logs') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-history"></i>
                        <p>Activity Logs</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.system-health') }}"
                        class="nav-link {{ $isRouteActive('admin.system-health') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-heartbeat"></i>
                        <p>System Health</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.database') }}"
                        class="nav-link {{ $isRouteActive('admin.database') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-database"></i>
                        <p>Database</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.help') }}"
                        class="nav-link {{ $isRouteActive('admin.help') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-question-circle"></i>
                        <p>Help</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.documentation') }}"
                        class="nav-link {{ $isRouteActive('admin.documentation') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-book"></i>
                        <p>Documentation</p>
                    </a>
                </li>

                <li class="nav-header">SYSTEM</li>
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}" id="logout-form">
                        @csrf
                        <a href="#"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            class="nav-link">
                            <i class="nav-icon fas fa-sign-out-alt text-danger"></i>
                            <p>Logout</p>
                        </a>
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</aside>