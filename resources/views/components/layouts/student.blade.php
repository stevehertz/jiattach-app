<!DOCTYPE html>
<html  lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.student.head')
    @stack('styles')
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        @include('partials.student.navbar')
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        @include('partials.student.aside')

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            {{ $slot }}
        </div>
        <!-- /.content-wrapper -->

        <!-- Main Footer -->
        @include('partials.student.footer')
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->
    @include('partials.student.foot')
    @stack('scripts')
</body>
</html>
