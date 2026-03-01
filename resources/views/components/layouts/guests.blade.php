<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.landing.head')
</head>


<body>
    <div class="relative flex h-auto min-h-screen w-full flex-col bg-background-light group/design-root overflow-x-hidden">
        <!-- Navigation -->
        @include('partials.landing.navbar')

         <!-- Main content container -->
        <main class="flex-1 flex items-center justify-center p-5">
            <div class="w-full max-w-4xl mx-auto mb-10 mt-10">
                <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl shadow-xl p-6 md:p-8 lg:p-10 border border-gray-100 dark:border-gray-700">
                    {{ $slot }}
                </div>

            </div>
        </main>

        <!-- Footer -->
        @include('partials.landing.foot')
    </div>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @livewireScripts
    @stack('scripts')
</body>

</html>
