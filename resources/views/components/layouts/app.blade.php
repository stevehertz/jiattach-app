<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.admin.head')
    @stack('styles')
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Navbar -->
        @include('partials.admin.navbar')
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
         <livewire:admin.layouts.sidebar />

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            {{ $slot }}
        </div>
        <!-- /.content-wrapper -->

        <!-- Footer -->
        @include('partials.admin.footer')
        <!-- /.footer -->

    </div>
    <!-- ./wrapper -->
    @include('partials.admin.foot')
    @stack('scripts')
</body>

</html>
