@php
    $user = auth()->user();
    $primaryOrg = $user->primaryOrganization();
    $colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
    $color = $colors[crc32($user->email) % count($colors)];

    // Helper for active routes
    $isRouteActive = fn($routes) => request()->routeIs(is_array($routes) ? $routes : [$routes]);
@endphp

<aside class="main-sidebar sidebar-light-success elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('employer.dashboard') }}" class="brand-link bg-success">
        <img src="{{ asset('img/logo-icon.png') }}" alt="{{ config('app.name') }}"
            class="brand-image brand-primary img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">
            {{ config('app.name') }}
        </span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- User Panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <div class="avatar-initials bg-{{ $color }} img-circle elevation-2"
                     style="width: 45px; height: 45px; line-height: 45px; text-align: center; color: white; font-weight: bold; font-size: 16px;">
                    {{ getInitials($user->full_name) }}
                </div>
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ $user->full_name }}</a>
                <small class="text-muted">
                    Employer
                    @if($primaryOrg)
                        <br>
                        <span class="text-success">
                            <i class="fas fa-building mr-1"></i>
                            {{ Str::limit($primaryOrg->name, 20) }}
                        </span>
                    @endif
                </small>
            </div>
        </div>

        <!-- Sidebar Search -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search menu..."
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
                    <a href="{{ route('employer.dashboard') }}"
                       class="nav-link {{ $isRouteActive('employer.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- ORGANIZATION SECTION -->
                <li class="nav-header">ORGANIZATION</li>

                <li class="nav-item">
                    <a href="{{ route('employer.organization.profile') }}"
                       class="nav-link {{ $isRouteActive('employer.organization.profile') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-building"></i>
                        <p>
                            Organization Profile
                            @if($primaryOrg && !$primaryOrg->is_verified)
                                <span class="badge badge-warning right">Unverified</span>
                            @endif
                        </p>
                    </a>
                </li>

                @if($user->can('manage organization'))
                    <li class="nav-item">
                        <a href="{{ route('employer.organization.edit') }}"
                           class="nav-link {{ $isRouteActive('employer.organization.edit') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-edit"></i>
                            <p>Edit Organization</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('employer.organization.members') }}"
                           class="nav-link {{ $isRouteActive('employer.organization.members') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users-cog"></i>
                            <p>Team Members</p>
                        </a>
                    </li>
                @endif

                <!-- OPPORTUNITIES SECTION -->
                <li class="nav-header">OPPORTUNITIES</li>

                <li class="nav-item">
                    <a href="{{ route('employer.opportunities.index') }}"
                       class="nav-link {{ $isRouteActive('employer.opportunities.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-briefcase"></i>
                        <p>
                            My Opportunities
                            @php
                                $activeOpportunities = $primaryOrg ? $primaryOrg->opportunities()->where('status', 'active')->count() : 0;
                            @endphp
                            @if($activeOpportunities > 0)
                                <span class="badge badge-primary right">{{ $activeOpportunities }}</span>
                            @endif
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('employer.opportunities.create') }}"
                       class="nav-link {{ $isRouteActive('employer.opportunities.create') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-plus-circle"></i>
                        <p>Post New Opportunity</p>
                    </a>
                </li>

                <!-- APPLICATIONS & PLACEMENTS -->
                <li class="nav-header">APPLICANTS</li>

                <li class="nav-item">
                    <a href="{{ route('employer.applications.index') }}"
                       class="nav-link {{ $isRouteActive('employer.applications.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-alt"></i>
                        <p>
                            Applications
                            @php
                                $pendingApps = $primaryOrg ? \App\Models\Application::whereHas('opportunity', function($q) use ($primaryOrg) {
                                    $q->where('organization_id', $primaryOrg->id);
                                })->where('status', 'pending')->count() : 0;
                            @endphp
                            @if($pendingApps > 0)
                                <span class="badge badge-warning right">{{ $pendingApps }}</span>
                            @endif
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('employer.placements.index') }}"
                       class="nav-link {{ $isRouteActive('employer.placements.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-check"></i>
                        <p>
                            Placements
                            @php
                                $activePlacements = $primaryOrg ? \App\Models\Placement::where('organization_id', $primaryOrg->id)
                                    ->where('status', 'placed')
                                    ->where('start_date', '<=', now())
                                    ->where('end_date', '>=', now())
                                    ->count() : 0;
                            @endphp
                            @if($activePlacements > 0)
                                <span class="badge badge-success right">{{ $activePlacements }}</span>
                            @endif
                        </p>
                    </a>
                </li>

                <!-- MATCHING & RECRUITMENT -->
                <li class="nav-header">RECRUITMENT</li>

                <li class="nav-item">
                    <a href="{{ route('employer.matching.suggestions') }}"
                       class="nav-link {{ $isRouteActive('employer.matching.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-magic"></i>
                        <p>Matching Suggestions</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('employer.students.search') }}"
                       class="nav-link {{ $isRouteActive('employer.students.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-search"></i>
                        <p>Search Students</p>
                    </a>
                </li>

                <!-- COMMUNICATION -->
                <li class="nav-header">COMMUNICATION</li>

                <li class="nav-item">
                    <a href="{{ route('employer.chat') }}"
                       class="nav-link {{ $isRouteActive('employer.chat') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-comments"></i>
                        <p>
                            Messages
                            @php
                                $unreadMessages = \App\Models\Conversation::forUser(auth()->id())
                                    ->whereHas('messages', function($q) {
                                        $q->where('sender_id', '!=', auth()->id())
                                          ->whereNull('read_at');
                                    })
                                    ->count();
                            @endphp
                            @if($unreadMessages > 0)
                                <span class="badge badge-danger right">{{ $unreadMessages }}</span>
                            @endif
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#"
                       class="nav-link {{ $isRouteActive('employer.notifications') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-bell"></i>
                        <p>Notifications</p>
                    </a>
                </li>

                <!-- REPORTS -->
                <li class="nav-header">REPORTS</li>

                <li class="nav-item">
                    <a href="{{ route('employer.reports.overview') }}"
                       class="nav-link {{ $isRouteActive('employer.reports.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>Reports & Analytics</p>
                    </a>
                </li>

                <!-- SETTINGS -->
                <li class="nav-header">SETTINGS</li>

                <li class="nav-item">
                    <a href="{{ route('employer.settings') }}"
                       class="nav-link {{ $isRouteActive('employer.settings') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>Account Settings</p>
                    </a>
                </li>

                <!-- QUICK CONTACT -->
                <li class="nav-header mt-3">SUPPORT</li>
                <li class="nav-item">
                    <div class="nav-link text-center">
                        <small class="text-muted">
                            <i class="fas fa-headset mr-1"></i>
                            Employer Support<br>
                            <a href="mailto:employers@jiattach.co.ke" class="text-primary">
                                <i class="fas fa-envelope mr-1"></i>
                                employers@jiattach.co.ke
                            </a><br>
                            <i class="fas fa-phone mr-1"></i>
                            +254 700 123 456
                        </small>
                    </div>
                </li>
            </ul>
        </nav>
    </div>
</aside>
