{{-- resources/views/layouts/employer.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.employer.head')
    @stack('styles')
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        @include('partials.employer.navbar')

        <!-- Main Sidebar Container -->
        @include('partials.employer.aside')

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            {{ $slot }}
        </div>

        <!-- Main Footer -->
        @include('partials.employer.footer')
    </div>

    <!-- REQUIRED SCRIPTS -->
    @include('partials.employer.foot')
    @stack('scripts')
</body>
</html>
