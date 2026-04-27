<x-layouts.app>

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark font-weight-bold">
                        <i class="fas fa-comments mr-2"></i>Messages
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Messages</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Chat List Sidebar -->
                <div class="col-md-4 px-0">
                    <livewire:admin.chat.chat-list />
                </div>

                <!-- Chat Box -->
                <div class="col-md-8 px-0">
                    <livewire:admin.chat.chat-box />
                </div>
            </div>
        </div>
    </section>

</x-layouts.app>