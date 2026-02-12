<x-layouts.app>
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Organizations</h1>
                </div>
                <div class="col-sm-6">
                    <a href="{{ route('admin.organizations.create') }}" class="btn btn-primary float-sm-right">
                        <i class="fas fa-plus mr-1"></i> Add Organization
                    </a>
                </div>
            </div>
        </div>
    </section>

    <livewire:admin.organizations.index />


</x-layouts.app>