<x-layouts.student>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark font-weight-bold">Notifications</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Notifications</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    
                    <div class="card card-outline card-success shadow-sm">
                        <div class="card-header border-0">
                            <h3 class="card-title">
                                <i class="fas fa-bell mr-1"></i> Recent Alerts
                            </h3>
                            <div class="card-tools">
                                @if($notifications->whereNull('read_at')->count() > 0)
                                    <form action="{{ route('student.notifications.markAll') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-tool text-primary font-weight-bold">
                                            <i class="fas fa-check-double mr-1"></i> Mark all as read
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                        
                        <div class="card-body p-0">
                            @if($notifications->isEmpty())
                                <div class="text-center py-5">
                                    <i class="fas fa-bell-slash fa-3x text-muted opacity-50 mb-3"></i>
                                    <h5 class="text-muted">No notifications found.</h5>
                                    <p class="small text-muted">You're all caught up!</p>
                                </div>
                            @else
                                <div class="list-group list-group-flush">
                                    @foreach($notifications as $notification)
                                        <div class="list-group-item list-group-item-action p-4 {{ $notification->unread() ? 'bg-light' : '' }}">
                                            <div class="d-flex align-items-start">
                                                <!-- Icon -->
                                                <div class="mr-3 mt-1">
                                                    <div class="icon-circle {{ $notification->unread() ? 'bg-primary' : 'bg-secondary' }}" 
                                                         style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                        <i class="{{ $notification->icon }} text-white"></i>
                                                    </div>
                                                </div>

                                                <!-- Content -->
                                                <div class="flex-grow-1">
                                                    <div class="d-flex w-100 justify-content-between">
                                                        <h5 class="mb-1 font-weight-bold {{ $notification->unread() ? 'text-dark' : 'text-muted' }}">
                                                            {{ $notification->title }}
                                                        </h5>
                                                        <small class="text-muted">
                                                            <i class="far fa-clock mr-1"></i> {{ $notification->time_ago }}
                                                        </small>
                                                    </div>
                                                    
                                                    <p class="mb-1 text-muted">
                                                        {{ $notification->message }}
                                                    </p>
                                                    
                                                    <div class="mt-2 d-flex justify-content-between align-items-center">
                                                        <span class="badge badge-light border">
                                                            {{ $notification->type_label }}
                                                        </span>
                                                        
                                                        <div class="btn-group">
                                                            @if($notification->unread())
                                                                <a href="{{ route('student.notifications.read', $notification->id) }}" class="btn btn-sm btn-primary shadow-sm">
                                                                    View Details <i class="fas fa-arrow-right ml-1"></i>
                                                                </a>
                                                            @else
                                                                @if($notification->url)
                                                                    <a href="{{ $notification->url }}" class="btn btn-sm btn-default text-muted">
                                                                        Review <i class="fas fa-external-link-alt ml-1"></i>
                                                                    </a>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        
                        <!-- Pagination -->
                        @if($notifications->hasPages())
                            <div class="card-footer bg-white">
                                <div class="d-flex justify-content-center">
                                    {{ $notifications->links() }}
                                </div>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </section>
</x-layouts.student>