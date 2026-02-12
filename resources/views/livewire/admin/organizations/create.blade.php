<div>
    {{-- Nothing in the world is as soft and yielding as water. --}}
    <section class="content">
        <div class="container-fluid">
            <form wire:submit="save">
                <div class="row">
                    <!-- Left: Owner Info -->
                    <div class="col-md-6">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Account Owner</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input wire:model="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror">
                                    @error('first_name') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input wire:model="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror">
                                    @error('last_name') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group">
                                    <label>Owner Email (Login)</label>
                                    <input wire:model="owner_email" type="email" class="form-control @error('owner_email') is-invalid @enderror">
                                    @error('owner_email') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group">
                                    <label>Password</label>
                                    <input wire:model="password" type="password" class="form-control @error('password') is-invalid @enderror">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Organization Info -->
                    <div class="col-md-6">
                        <div class="card card-success">
                            <div class="card-header">
                                <h3 class="card-title">Organization Details</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Company Name</label>
                                    <input wire:model="name" type="text" class="form-control @error('name') is-invalid @enderror">
                                    @error('name') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group">
                                    <label>Official Email</label>
                                    <input wire:model="email" type="email" class="form-control @error('email') is-invalid @enderror">
                                </div>
                                <div class="form-group">
                                    <label>Industry</label>
                                    <select wire:model="industry" class="form-control">
                                        <option value="">Select...</option>
                                        <option value="Technology">Technology</option>
                                        <option value="Finance">Finance</option>
                                        <option value="Health">Health</option>
                                        <option value="Agriculture">Agriculture</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input wire:model="phone" type="text" class="form-control" id="company_phone">
                                </div>
                                <div class="form-group">
                                    <label>County</label>
                                    <input wire:model="county" type="text" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-4">
                        <button type="submit" class="btn btn-success float-right">Create Organization</button>
                        <a href="{{ route('admin.organizations.index') }}" class="btn btn-secondary float-right mr-2">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>
