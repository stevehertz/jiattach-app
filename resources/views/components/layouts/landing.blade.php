<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.landing.head')
</head>


<body>
    <div class="relative flex h-auto min-h-screen w-full flex-col bg-background-light group/design-root overflow-x-hidden">
        <!-- Navigation -->
        @include('partials.landing.navbar')

        {{ $slot }}
        
        <!-- Footer -->
        @include('partials.landing.foot')
    </div>

    @stack('scripts')
</body>

</html>
