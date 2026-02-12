<div>
    {{-- The whole world belongs to you. --}}
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">List of Registered Organizations</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input wire:model.live.debounce.300ms="search" type="text" class="form-control float-right" placeholder="Search name or email...">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Owner</th>
                                <th>Industry</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($organizations as $org)
                                <tr>
                                    <td>
                                        <strong>{{ $org->name }}</strong><br>
                                        <small class="text-muted">{{ $org->email }}</small>
                                    </td>
                                    <td>{{ $org->user->full_name ?? 'N/A' }}</td>
                                    <td>{{ $org->industry }}</td>
                                    <td>{{ $org->county }}</td>
                                    <td>
                                        @if($org->is_verified)
                                            <span class="badge badge-success">Verified</span>
                                        @else
                                            <span class="badge badge-warning">Pending</span>
                                        @endif
                                        
                                        @if(!$org->is_active)
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('admin.organizations.show', $org->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.organizations.edit', $org->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <button wire:click="confirmDelete({{ $org->id }})" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">No organizations found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    {{ $organizations->links() }}
                </div>
            </div>
        </div>
    </section>

    <!-- SweetAlert Script -->
    @script
    <script>
        Livewire.on('show-delete-confirmation', ({ id }) => {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('deleteConfirmed', { id: id });
                }
            })
        });

        Livewire.on('toastr:success', ({ message }) => {
            toastr.success(message);
        });
    </script>
    @endscript
</div>
