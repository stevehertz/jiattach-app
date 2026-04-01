{{-- resources/views/livewire/admin/notifications-index.blade.php --}}
<div>
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Notifications</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Notifications</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <!-- Notification Stats Row -->
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $notifications->total() }}</h3>
                                    <p>Total Notifications</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-bell"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ $unreadCount }}</h3>
                                    <p>Unread Notifications</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                @if ($unreadCount > 0)
                                    <a href="#" wire:click="markAllAsRead" class="small-box-footer">
                                        Mark all as read <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $notifications->total() - $unreadCount }}</h3>
                                    <p>Read Notifications</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>{{ Auth::user()->notifications()->where('created_at', '>=', now()->subDays(7))->count() }}
                                    </h3>
                                    <p>Last 7 Days</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-calendar-week"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notifications Card -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-bell mr-1"></i>
                                Notification Center
                            </h3>

                            <div class="card-tools">
                                <!-- Filter Buttons -->
                                <div class="btn-group mr-2">
                                    <button wire:click="$set('filter', 'all')"
                                        class="btn btn-sm {{ $filter === 'all' ? 'btn-primary' : 'btn-default' }}">
                                        <i class="fas fa-list"></i> All
                                        <span class="badge badge-light ml-1">{{ $notifications->total() }}</span>
                                    </button>
                                    <button wire:click="$set('filter', 'unread')"
                                        class="btn btn-sm {{ $filter === 'unread' ? 'btn-primary' : 'btn-default' }}">
                                        <i class="fas fa-envelope"></i> Unread
                                        <span class="badge badge-warning ml-1">{{ $unreadCount }}</span>
                                    </button>
                                    <button wire:click="$set('filter', 'read')"
                                        class="btn btn-sm {{ $filter === 'read' ? 'btn-primary' : 'btn-default' }}">
                                        <i class="fas fa-check"></i> Read
                                    </button>
                                </div>

                                <!-- Bulk Actions Dropdown -->
                                @if (count($selectedNotifications) > 0)
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-info dropdown-toggle"
                                            data-toggle="dropdown">
                                            <i class="fas fa-tasks"></i> Bulk Actions
                                            ({{ count($selectedNotifications) }})
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <button class="dropdown-item" wire:click="markSelectedAsRead">
                                                <i class="fas fa-check-double text-success mr-2"></i> Mark as Read
                                            </button>
                                            <button class="dropdown-item" wire:click="deleteSelected">
                                                <i class="fas fa-trash text-danger mr-2"></i> Delete Selected
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="card-body p-0">
                            @if ($notifications->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width: 40px;">
                                                    <div class="icheck-primary">
                                                        <input type="checkbox" id="selectAll" wire:model="selectAll">
                                                        <label for="selectAll"></label>
                                                    </div>
                                                </th>
                                                <th style="width: 60px;">Icon</th>
                                                <th>Notification</th>
                                                <th style="width: 150px;">Date</th>
                                                <th style="width: 120px;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($notifications as $notification)
                                                <tr
                                                    class="{{ is_null($notification->read_at) ? 'bg-light-blue' : '' }}">
                                                    <td>
                                                        <div class="icheck-primary">
                                                            <input type="checkbox"
                                                                id="notification_{{ $notification->id }}"
                                                                value="{{ $notification->id }}"
                                                                wire:model="selectedNotifications">
                                                            <label for="notification_{{ $notification->id }}"></label>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <i
                                                            class="fas {{ $notification->data['icon'] ?? 'fa-bell' }} fa-2x 
                                                              {{ $notification->data['icon'] ?? '' }}"></i>
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <strong>{{ $notification->data['title'] ?? 'Notification' }}</strong>
                                                            @if (is_null($notification->read_at))
                                                                <span class="badge badge-primary ml-2">
                                                                    <i class="fas fa-dot-circle"></i> New
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <p class="mb-0 text-muted">
                                                            {{ $notification->data['message'] ?? '' }}</p>

                                                        @if (isset($notification->data['data']['student_name']))
                                                            <small class="text-info">
                                                                <i class="fas fa-user"></i>
                                                                {{ $notification->data['data']['student_name'] }}
                                                            </small>
                                                        @endif

                                                        @if (isset($notification->data['data']['institution']))
                                                            <small class="text-secondary ml-2">
                                                                <i class="fas fa-university"></i>
                                                                {{ $notification->data['data']['institution'] }}
                                                            </small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="text-nowrap">
                                                            <i class="far fa-calendar-alt text-muted"></i>
                                                            {{ $notification->created_at->format('M d, Y') }}
                                                        </div>
                                                        <small class="text-muted">
                                                            <i class="far fa-clock"></i>
                                                            {{ $notification->created_at->diffForHumans() }}
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            @if (isset($notification->data['url']))
                                                                <a href="{{ $notification->data['url'] }}"
                                                                    class="btn btn-info"
                                                                    wire:click="markAsRead('{{ $notification->id }}')"
                                                                    data-toggle="tooltip" title="View Details">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            @endif

                                                            @if (is_null($notification->read_at))
                                                                <button
                                                                    wire:click="markAsRead('{{ $notification->id }}')"
                                                                    class="btn btn-success" data-toggle="tooltip"
                                                                    title="Mark as Read">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                            @else
                                                                <button
                                                                    wire:click="markAsUnread('{{ $notification->id }}')"
                                                                    class="btn btn-warning" data-toggle="tooltip"
                                                                    title="Mark as Unread">
                                                                    <i class="fas fa-undo"></i>
                                                                </button>
                                                            @endif

                                                            <button
                                                                wire:click="deleteNotification('{{ $notification->id }}')"
                                                                class="btn btn-danger"
                                                                onclick="return confirm('Are you sure you want to delete this notification?')"
                                                                data-toggle="tooltip" title="Delete">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <div class="card-footer clearfix">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="dataTables_info">
                                                Showing {{ $notifications->firstItem() }} to
                                                {{ $notifications->lastItem() }}
                                                of {{ $notifications->total() }} notifications
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="float-right">
                                                {{ $notifications->links() }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-bell-slash fa-4x text-muted mb-3"></i>
                                    <h5>No notifications found</h5>
                                    <p class="text-muted">You're all caught up! New notifications will appear here.</p>

                                    @if ($filter !== 'all')
                                        <button wire:click="$set('filter', 'all')" class="btn btn-primary mt-3">
                                            <i class="fas fa-list"></i> View All Notifications
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Toast Notification Script -->
    @push('scripts')
        <script>
            document.addEventListener('livewire:init', function() {
                // Listen for toast events
                Livewire.on('show-toast', (event) => {
                    const {
                        type,
                        message
                    } = event;

                    // If you have SweetAlert2 installed
                    if (typeof Swal !== 'undefined') {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                        });

                        Toast.fire({
                            icon: type,
                            title: message
                        });
                    } else {
                        // Fallback for bootstrap toasts or alert
                        alert(message);
                    }
                });

                // Initialize tooltips
                if (typeof $ !== 'undefined') {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });
        </script>
    @endpush

    @push('styles')
        <style>
            .bg-light-blue {
                background-color: #e3f2fd !important;
            }

            .table-hover tbody tr:hover {
                background-color: rgba(0, 0, 0, .075);
                cursor: pointer;
            }

            .small-box .icon {
                font-size: 70px;
                top: 10px;
            }

            .icheck-primary {
                margin-bottom: 0;
            }

            .btn-group-sm>.btn,
            .btn-sm {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }

            .dataTables_info {
                padding-top: 0.755rem;
            }
        </style>
    @endpush
</div>
