<nav class="main-header navbar navbar-expand navbar-success navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('student.dashboard') }}" class="nav-link">Dashboard</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('student.placement.status') }}" class="nav-link">My Placement</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('student.mentorship.find') }}" class="nav-link">Find Mentor</a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Notifications Dropdown -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-bell"></i>
                @php
                    $placementNotifications = Auth::user()->unreadNotifications()
                        ->where('type', 'App\Notifications\PlacementNotification')
                        ->count();
                @endphp
                @if($placementNotifications > 0)
                    <span class="badge badge-warning navbar-badge">{{ $placementNotifications }}</span>
                @endif
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-header">{{ $placementNotifications }} Placement Updates</span>
                <div class="dropdown-divider"></div>

                @php
                    // Get recent placement notifications
                    $recentPlacements = Auth::user()->placements()
                        ->with('organization')
                        ->latest()
                        ->limit(3)
                        ->get();

                    $placementUpdates = Auth::user()->notifications()
                        ->where('type', 'App\Notifications\PlacementNotification')
                        ->latest()
                        ->limit(2)
                        ->get();
                @endphp

                @if($recentPlacements->count() > 0)
                    @foreach($recentPlacements as $placement)
                        @if($placement->status == 'placed')
                            <a href="{{ route('student.placement.status') }}" class="dropdown-item">
                                <i class="fas fa-check-circle mr-2 text-success"></i>
                                Placed at {{ $placement->organization->name ?? 'organization' }}
                                <span class="float-right text-muted text-sm">
                                    {{ timeAgo($placement->updated_at) }}
                                </span>
                            </a>
                            <div class="dropdown-divider"></div>
                        @endif
                    @endforeach
                @endif

                @if($placementUpdates->count() > 0)
                    @foreach($placementUpdates as $notification)
                        <a href="{{ route('student.notifications') }}" class="dropdown-item">
                            <i class="fas fa-briefcase mr-2"></i>
                            {{ $notification->data['title'] ?? 'Placement Update' }}
                            <span class="float-right text-muted text-sm">
                                {{ timeAgo($notification->created_at) }}
                            </span>
                        </a>
                        <div class="dropdown-divider"></div>
                    @endforeach
                @endif

                @if($recentPlacements->count() == 0 && $placementUpdates->count() == 0)
                    <a href="#" class="dropdown-item dropdown-footer">
                        <i class="fas fa-info-circle mr-2"></i> No placement updates
                    </a>
                @else
                    <a href="{{ route('student.notifications') }}" class="dropdown-item dropdown-footer">
                        View All Notifications
                    </a>
                @endif
            </div>
        </li>

        <!-- Messages Dropdown -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-envelope"></i>
                @php
                    $unreadMessages = 0; // You'll implement this based on your messages system
                @endphp
                @if($unreadMessages > 0)
                    <span class="badge badge-danger navbar-badge">{{ $unreadMessages }}</span>
                @endif
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-header">{{ $unreadMessages }} New Messages</span>
                <div class="dropdown-divider"></div>

                <!-- Sample Messages -->
                @if(isset($placement) && $placement->admin)
                    <a href="#" class="dropdown-item">
                        <div class="media">
                            <div class="avatar-initials bg-primary img-circle mr-3"
                                 style="width: 40px; height: 40px; line-height: 40px;">
                                {{ getInitials($placement->admin->name) }}
                            </div>
                            <div class="media-body">
                                <h3 class="dropdown-item-title">
                                    Placement Coordinator
                                </h3>
                                <p class="text-sm">Your placement is being processed</p>
                                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 2 Hours Ago</p>
                            </div>
                        </div>
                    </a>
                    <div class="dropdown-divider"></div>
                @endif

                <a href="#" class="dropdown-item dropdown-footer">
                    See All Messages
                </a>
            </div>
        </li>

        <!-- User Account Dropdown -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                @php
                    $initials = getInitials(Auth::user()->full_name);
                    $colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
                    $color = $colors[crc32(Auth::user()->email) % count($colors)];
                @endphp
                <div class="d-inline-block avatar-initials bg-{{ $color }} img-circle elevation-1"
                     style="width: 30px; height: 30px; line-height: 30px; text-align: center; color: white; font-weight: bold; font-size: 14px;">
                    {{ $initials }}
                </div>
                <span class="d-none d-md-inline ml-2">{{ Auth::user()->first_name }}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <div class="dropdown-header text-center">
                    <strong>{{ Auth::user()->full_name }}</strong><br>
                    <small>Student Account</small>
                </div>
                <div class="dropdown-divider"></div>

                <a href="#" class="dropdown-item">
                    <i class="fas fa-user mr-2"></i> My Profile
                    @php
                        $profileCompleteness = Auth::user()->studentProfile->profile_completeness ?? 0;
                    @endphp
                    @if($profileCompleteness < 80)
                        <span class="float-right">
                            <span class="badge badge-warning">{{ $profileCompleteness }}%</span>
                        </span>
                    @endif
                </a>

                <a href="{{ route('student.placement.status') }}" class="dropdown-item">
                    <i class="fas fa-briefcase mr-2"></i> Placement Status
                    @if(isset($placement))
                        <span class="float-right">
                            <span class="badge badge-{{ $placement->status == 'placed' ? 'success' : 'warning' }}">
                                {{ ucfirst($placement->status) }}
                            </span>
                        </span>
                    @endif
                </a>

                <a href="#" class="dropdown-item">
                    <i class="fas fa-cog mr-2"></i> Settings
                </a>

                <a href="#" class="dropdown-item">
                    <i class="fas fa-file-alt mr-2"></i> My Documents
                </a>

                <div class="dropdown-divider"></div>

                <a href="{{ route('home') }}" target="_blank" class="dropdown-item">
                    <i class="fas fa-external-link-alt mr-2"></i> View Public Site
                </a>

                <a href="#" class="dropdown-item">
                    <i class="fas fa-question-circle mr-2"></i> Help & Support
                </a>

                <div class="dropdown-divider"></div>

                <!-- Logout -->
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