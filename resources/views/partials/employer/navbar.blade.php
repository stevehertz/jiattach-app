<nav class="main-header navbar navbar-expand navbar-success navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('employer.dashboard') }}" class="nav-link">Dashboard</a>
        </li>

        @php
            $primaryOrg = auth()->user()->primaryOrganization();
        @endphp

        <!-- Organization Name Badge -->
        @if($primaryOrg)
            <li class="nav-item d-none d-md-inline-block">
                <span class="nav-link">
                    <span class="badge badge-success font-weight-normal px-3 py-1" style="font-size: 0.9rem;">
                        <i class="fas fa-building mr-1"></i>
                        {{ $primaryOrg->name }}
                        @if($primaryOrg->is_verified)
                            <i class="fas fa-check-circle ml-1" title="Verified Organization"></i>
                        @endif
                    </span>
                </span>
            </li>
        @endif
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Notifications Dropdown -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-bell"></i>
                @php
                    $unreadNotifications = auth()->user()->unreadNotifications->count();
                @endphp
                @if($unreadNotifications > 0)
                    <span class="badge badge-warning navbar-badge">{{ $unreadNotifications }}</span>
                @endif
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-header">{{ $unreadNotifications }} Notifications</span>
                <div class="dropdown-divider"></div>

                @forelse(auth()->user()->unreadNotifications->take(5) as $notification)
                    <a href="#" class="dropdown-item">
                        <i class="fas {{ $notification->data['icon'] ?? 'fa-bell' }} mr-2"></i>
                        {{ $notification->data['message'] ?? 'New notification' }}
                        <span class="float-right text-muted text-sm">
                            {{ $notification->created_at->diffForHumans() }}
                        </span>
                    </a>
                    <div class="dropdown-divider"></div>
                @empty
                    <a href="#" class="dropdown-item text-center text-muted">
                        <i class="fas fa-check-circle mr-2"></i> No new notifications
                    </a>
                @endforelse

                @if($unreadNotifications > 0)
                    <a href="#" class="dropdown-item dropdown-footer">View All Notifications</a>
                @endif
            </div>
        </li>

        <!-- Messages Dropdown -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-envelope"></i>
                @php
                    $unreadMessages = \App\Models\Conversation::forUser(auth()->id())
                        ->whereHas('messages', function($q) {
                            $q->where('sender_id', '!=', auth()->id())
                              ->whereNull('read_at');
                        })
                        ->count();
                @endphp
                @if($unreadMessages > 0)
                    <span class="badge badge-danger navbar-badge">{{ $unreadMessages }}</span>
                @endif
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-header">{{ $unreadMessages }} New Messages</span>
                <div class="dropdown-divider"></div>
                <a href="{{ route('employer.chat') }}" class="dropdown-item dropdown-footer">
                    Open Messages
                </a>
            </div>
        </li>

        <!-- Organization Switcher (if multiple organizations) -->
        @php
            $organizations = auth()->user()->organizations()->wherePivot('is_active', true)->get();
        @endphp
        @if($organizations->count() > 1)
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="fas fa-building"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <span class="dropdown-header">Switch Organization</span>
                    <div class="dropdown-divider"></div>
                    @foreach($organizations as $org)
                        <a href="#" class="dropdown-item"
                           wire:click="switchOrganization({{ $org->id }})">
                            @if($primaryOrg && $primaryOrg->id == $org->id)
                                <i class="fas fa-check text-success mr-2"></i>
                            @else
                                <i class="far fa-circle mr-2"></i>
                            @endif
                            {{ $org->name }}
                        </a>
                    @endforeach
                </div>
            </li>
        @endif

        <!-- User Account Dropdown -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                @php
                    $initials = getInitials(auth()->user()->full_name);
                    $colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
                    $color = $colors[crc32(auth()->user()->email) % count($colors)];
                @endphp
                <div class="d-inline-block avatar-initials bg-{{ $color }} img-circle elevation-1"
                     style="width: 30px; height: 30px; line-height: 30px; text-align: center; color: white; font-weight: bold; font-size: 14px;">
                    {{ $initials }}
                </div>
                <span class="d-none d-md-inline ml-2">{{ auth()->user()->first_name }}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <div class="dropdown-header text-center">
                    <strong>{{ auth()->user()->full_name }}</strong><br>
                    <small class="text-muted">Employer Account</small>
                    @if($primaryOrg)
                        <br>
                        <small class="text-success">
                            <i class="fas fa-building mr-1"></i>{{ $primaryOrg->name }}
                        </small>
                    @endif
                </div>
                <div class="dropdown-divider"></div>

                <a href="{{ route('employer.profile') }}" class="dropdown-item">
                    <i class="fas fa-user mr-2"></i> My Profile
                </a>

                <a href="{{ route('employer.organization.profile') }}" class="dropdown-item">
                    <i class="fas fa-building mr-2"></i> Organization Profile
                </a>

                <a href="{{ route('employer.settings') }}" class="dropdown-item">
                    <i class="fas fa-cog mr-2"></i> Settings
                </a>

                <div class="dropdown-divider"></div>

                <form method="POST" action="{{ route('logout') }}" class="w-100">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger" style="cursor: pointer;">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </button>
                </form>
            </div>
        </li>
    </ul>
</nav>
