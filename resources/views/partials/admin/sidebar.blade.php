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
        <img src="{{ asset('img/logo-icon.png') }}" alt="{{ config('app.name') }}" class="brand-image img-circle elevation-3" style="opacity: .8">
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
            <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
                
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ $isRouteActive('admin.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-header">USER MANAGEMENT</li>
                
                <li class="nav-item">
                    <a href="{{ route('admin.users.index') }}" class="nav-link {{ $isRouteActive('admin.users.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users-cog"></i>
                        <p>All System Users</p>
                    </a>
                </li>

                <li class="nav-item {{ $isRouteActive('admin.administrators.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ $isRouteActive('admin.administrators.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-shield"></i>
                        <p>Administrators <i class="fas fa-angle-left right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.administrators.index') }}" class="nav-link {{ $isRouteActive('admin.administrators.index') ? 'active' : '' }}">
                                <i class="far {{ $isRouteActive('admin.administrators.index') ? 'fa-dot-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Manage Staff</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.administrators.create') }}" class="nav-link {{ $isRouteActive('admin.administrators.create') ? 'active' : '' }}">
                                <i class="far {{ $isRouteActive('admin.administrators.create') ? 'fa-dot-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Add New Staff</p>
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
                            <a href="{{ route('admin.students.index') }}" class="nav-link {{ $isRouteActive('admin.students.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Students</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.students.seeking') }}" class="nav-link {{ $isRouteActive('admin.students.seeking') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon text-warning"></i>
                                <p>Seeking Attachment</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.students.on-attachment') }}" class="nav-link {{ $isRouteActive('admin.students.on-attachment') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon text-success"></i>
                                <p>Placed Students</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-header">OPPORTUNITIES</li>
                <li class="nav-item {{ $isRouteActive('admin.opportunities.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ $isRouteActive('admin.opportunities.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-briefcase"></i>
                        <p>Opportunities <i class="fas fa-angle-left right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.opportunities.index') }}" class="nav-link {{ $isRouteActive('admin.opportunities.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>View All</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.opportunities.pending') }}" class="nav-link {{ $isRouteActive('admin.opportunities.pending') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon text-danger"></i>
                                <p>Pending Review</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.opportunities.create') }}" class="nav-link {{ $isRouteActive('admin.opportunities.create') ? 'active' : '' }}">
                                <i class="far fa-plus-square nav-icon"></i>
                                <p>Post New</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-header">SYSTEM</li>
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}" id="logout-form">
                        @csrf
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-link">
                            <i class="nav-icon fas fa-sign-out-alt text-danger"></i>
                            <p>Logout</p>
                        </a>
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</aside>