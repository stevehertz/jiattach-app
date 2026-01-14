<footer class="bg-white border-t border-[#cfdbe7] px-10 py-12">
    <div class="flex flex-col md:flex-row justify-between gap-10 max-w-[960px] mx-auto">
        <div class="flex flex-col gap-4 max-w-xs">
            <div class="flex items-center gap-2 text-[#0d141b]">
                <div class="size-6 flex items-center justify-center text-primary">
                    <img src="{{ asset('img/logo-icon.png') }}" alt="{{ config('app.name') }}">
                </div>
                <h2 class="text-lg font-bold">
                    {{ config('app.name') }}
                </h2>
            </div>
            <p class="text-sm text-gray-500">
                Connecting Kenya's talent with opportunity. Building the workforce of tomorrow, today.
            </p>
        </div>
        <div class="flex flex-wrap gap-12">
            <div class="flex flex-col gap-3">
                <h4 class="text-[#0d141b] font-bold text-sm uppercase tracking-wider">Platform
                </h4>
                <a class="text-sm text-gray-600 hover:text-primary transition-colors"
                    href="{{ route('students') }}">Students</a>
                <a class="text-sm text-gray-600 hover:text-primary transition-colors"
                    href="{{ route('entrepreneurs') }}">Entrepreneurs</a>
                <a class="text-sm text-gray-600 hover:text-primary transition-colors"
                    href="{{ route('mentorship') }}">Mentorship</a>
            </div>
            <div class="flex flex-col gap-3">
                <h4 class="text-[#0d141b] font-bold text-sm uppercase tracking-wider">Company
                </h4>
                <a class="text-sm text-gray-600 hover:text-primary transition-colors"
                    href="{{ route('about') }}">About Us</a>
                <a class="text-sm text-gray-600 hover:text-primary transition-colors"
                    href="{{ route('contact') }}">Contact</a>
                <a class="text-sm text-gray-600 hover:text-primary transition-colors"
                    href="#">Privacy Policy</a>
            </div>
            <div class="flex flex-col gap-3">
                <h4 class="text-[#0d141b] font-bold text-sm uppercase tracking-wider">Connect
                </h4>
                <div class="flex gap-4">
                    <a class="text-gray-400 hover:text-primary transition-colors" href="#">
                        <span class="material-symbols-outlined">public</span>
                    </a>
                    <a class="text-gray-400 hover:text-primary transition-colors" href="#">
                        <span class="material-symbols-outlined">mail</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div
        class="max-w-[960px] mx-auto mt-12 pt-8 border-t border-gray-100 text-center text-sm text-gray-400">
        © 2026 {{ config('app.name') }} Kenya. All rights reserved.
    </div>
</footer>
