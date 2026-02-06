<x-layouts.app :title="$administrator->full_name . ' | Administrator Profile'">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Administrator Profile</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('admin.administrators.index') }}">Administrators</a></li>
                        <li class="breadcrumb-item active">{{ $administrator->full_name }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            @livewire('admin.administrators.show', ['administrator' => $administrator])
        </div>
    </section>

</x-layouts.app>
