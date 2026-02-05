<div>
    {{-- Do your work, then step back. --}}
    {{-- If your happiness depends on money, you will never be happy with yourself. --}}
    <aside class="main-sidebar sidebar-light-success elevation-4">
        <!-- Brand Logo -->
        <a href="{{ route('admin.dashboard') }}" class="brand-link bg-success">
            <img src="{{ asset('img/logo-icon.png') }}" alt="{{ config('app.name') }}"
                class="brand-image brand-success img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">
                {{ config('app.name') }}
            </span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    @php
                        $initials = getInitials($user->full_name);
                        $colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
                        $color = $colors[crc32($user->email) % count($colors)];
                    @endphp
                    <div class="avatar-initials bg-{{ $color }} img-circle elevation-2"
                        style="width: 45px; height: 45px; line-height: 45px; text-align: center; color: white; font-weight: bold; font-size: 16px;">
                        {{ $initials }}
                    </div>
                </div>
                <div class="info">
                    <a href="{{ route('admin.profile') }}" class="d-block">
                        {{ $user->full_name }}  
                    </a>
                    <small class="text-muted">
                        @if ($user->hasRole('super-admin'))
                            <span class="badge badge-danger">Super Admin</span>
                        @elseif ($user->hasRole('admin'))
                            <span class="badge badge-success">Admin</span>
                        @elseif ($user->hasRole('moderator'))
                            <span class="badge badge-info">Moderator</span>
                        @elseif ($user->hasRole('student'))
                            <span class="badge badge-primary">Student</span>
                        @elseif ($user->hasRole('employer'))
                            <span class="badge badge-warning">Employer</span>
                        @elseif ($user->hasRole('mentor'))
                            <span class="badge badge-purple">Mentor</span>
                        @elseif ($user->hasRole('entrepreneur'))
                            <span class="badge badge-teal">Entrepreneur</span>
                        @endif
                    </small>
                </div>
            </div>

            <!-- SidebarSearch Form -->
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
                    @foreach ($menu as $sectionKey => $section)
                        @if ($section['label'] !== 'Dashboard')
                            <li class="nav-header">{{ $section['label'] }}</li>
                        @endif

                        @foreach ($section['items'] as $item)
                            @if ($item['type'] === 'single')
                                @php
                                    $route = $item['route'];
                                    $active = $this->isActive($route['name']);
                                @endphp
                                <li class="nav-item">
                                    <a href="{{ route($route['name']) }}"
                                        class="nav-link {{ $active ? 'active' : '' }}" wire:navigate>
                                        <i class="nav-icon fas fa-{{ $route['icon'] }}"></i>
                                        <p>{{ $route['display_name'] }}</p>
                                    </a>
                                </li>
                            @elseif($item['type'] === 'parent')
                                @php
                                    $parent = $item['route'];
                                    $children = $item['children'];
                                    $parentActive = $this->isActive($parent['name']);
                                    $hasActiveChild = false;

                                    // Check if any child is active
                                    foreach ($children as $child) {
                                        if ($this->isActive($child['name'])) {
                                            $hasActiveChild = true;
                                            break;
                                        }
                                    }

                                    $menuOpen = $parentActive || $hasActiveChild;
                                @endphp
                                <li class="nav-item {{ $menuOpen ? 'menu-open' : '' }}">
                                    <a href="#" class="nav-link {{ $menuOpen ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-{{ $parent['icon'] }}"></i>
                                        <p>
                                            {{ $parent['display_name'] }}
                                            <i class="right fas fa-angle-left"></i>
                                            @if (isset($stats[$parent['resource']]))
                                                @php
                                                    $stat = $stats[$parent['resource']];
                                                    $total = $stat['total'] ?? 0;
                                                @endphp
                                                @if ($total > 0)
                                                    <span
                                                        class="badge badge-{{ $this->getResourceColor($parent['resource']) }} right">{{ $total }}</span>
                                                @endif
                                            @endif
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        @foreach ($children as $child)
                                            @php
                                                $active = $this->isActive($child['name']);
                                                $childStat = null;

                                                // Get statistic for this specific child
                                                if (isset($stats[$parent['resource']][$child['action']])) {
                                                    $childStat = $stats[$parent['resource']][$child['action']];
                                                }
                                            @endphp
                                            <li class="nav-item">
                                                <a href="{{ route($child['name']) }}"
                                                    class="nav-link {{ $active ? 'active' : '' }}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>{{ $child['display_name'] }}</p>
                                                    @if ($childStat)
                                                        <span
                                                            class="badge badge-{{ $this->getActionColor($child['action']) }} right">{{ $childStat }}</span>
                                                    @endif
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endif
                        @endforeach
                    @endforeach

                    <!-- Quick Links -->
                    <li class="nav-header">QUICK LINKS</li>
                    <li class="nav-item">
                        <a href="{{ route('home') }}" target="_blank" class="nav-link">
                            <i class="nav-icon fas fa-external-link-alt"></i>
                            <p>View Public Site</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="mailto:support@jiattach.co.ke" class="nav-link">
                            <i class="nav-icon fas fa-envelope"></i>
                            <p>Contact Support</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('[data-widget="treeview"]').Treeview('init');
            });
        </script>
    @endpush
</div>
