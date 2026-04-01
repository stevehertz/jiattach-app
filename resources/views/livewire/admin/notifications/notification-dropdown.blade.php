<div>
    {{-- resources/views/livewire/admin/notification-dropdown.blade.php --}}
    <!-- Notifications Dropdown Menu -->
    <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
            <i class="far fa-bell"></i>
            @if ($unreadCount > 0)
                <span class="badge badge-warning navbar-badge">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
            @endif
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
            <span class="dropdown-item dropdown-header">
                <i class="fas fa-bell mr-2"></i> {{ $unreadCount }} Notification(s)
            </span>

            <div class="dropdown-divider"></div>

            @forelse($notifications as $notification)
                <a href="{{ $notification['url'] }}"
                    class="dropdown-item {{ is_null($notification['read_at']) ? 'bg-light' : '' }}"
                    wire:click.prevent="markAsRead({{ $notification['id'] }})">
                    <div class="d-flex align-items-center">
                        <div class="mr-2 flex-shrink-0">
                            <i class="fas {{ $notification['icon'] }} fa-fw"></i>
                        </div>
                        <div class="flex-grow-1 min-width-0">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <strong class="text-truncate"
                                    style="max-width: 180px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                                    title="{{ $notification['title'] }}">
                                    {{ $notification['title'] }}
                                </strong>
                                <span class="text-muted text-sm flex-shrink-0" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 80px;" title="{{ $notification['time_ago'] }}">
                                    {{ $notification['time_ago'] }}
                                </span>
                            </div>
                            <div class="text-sm text-muted text-truncate"
                                style="max-width: 100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                                title="{{ $notification['message'] }}">
                                {{ Str::limit($notification['message'], 20) }}
                            </div>
                        </div>
                        @if (is_null($notification['read_at']))
                            <div class="ml-2 flex-shrink-0">
                                <span class="badge badge-primary badge-pill">New</span>
                            </div>
                        @endif
                    </div>
                </a>
                @if (!$loop->last)
                    <div class="dropdown-divider"></div>
                @endif
            @empty
                <div class="dropdown-item text-center py-4">
                    <i class="fas fa-bell-slash fa-2x text-muted mb-2 d-block"></i>
                    <p class="text-muted mb-0">No notifications yet</p>
                    <small class="text-muted">New notifications will appear here</small>
                </div>
            @endforelse



            @if ($notifications->count() > 0)
                <div class="dropdown-divider"></div>
                <div class="dropdown-item dropdown-footer d-flex justify-content-between">
                    @if ($unreadCount > 0)
                        <button wire:click="markAllAsRead" class="btn btn-link btn-sm p-0 text-muted">
                            <i class="fas fa-check-double mr-1"></i> Mark all as read
                        </button>
                    @endif
                    <a href="{{ route('admin.notifications.index') }}" class="text-muted">
                        See All Notifications
                        <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            @endif
        </div>
    </li>

    @push('styles')
        <style>
            /* Optional: Add hover effect for unread notifications */
            .dropdown-item.bg-light:hover {
                background-color: #e9ecef !important;
            }

            /* Make sure the dropdown doesn't overflow on mobile */
            @media (max-width: 576px) {
                .dropdown-menu {
                    position: fixed !important;
                    top: 60px !important;
                    left: 10px !important;
                    right: 10px !important;
                    width: auto !important;
                    transform: none !important;
                }
            }
        </style>
    @endpush


</div>
