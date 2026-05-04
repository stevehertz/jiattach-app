<x-layouts.app>
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Employers Management</h1>
                </div>
                <div class="col-sm-6">
                    <!-- Optional: breadcrumbs, actions -->
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @livewire('admin.employers.employers-table')
        </div>
    </section>
</x-layouts.app>