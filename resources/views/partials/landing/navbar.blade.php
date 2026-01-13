<header
    class="sticky top-0 z-50 flex items-center justify-between whitespace-nowrap border-b border-solid border-b-[#e7edf3] dark:border-b-gray-800 bg-white/90 dark:bg-background-dark/90 backdrop-blur-md px-10 py-3">
    <a href="{{ route('home') }}" wire:navigate>
        <div class="flex items-center gap-4 text-[#0d141b] dark:text-white">
            <div class="size-8 flex items-center justify-center text-primary">
                {{-- <span class="material-symbols-outlined text-4xl">connect_without_contact</span> --}}
                <img src="{{ asset('img/logo-icon.png') }}" alt="{{ config('app.name') }}">
            </div>
            <h2 class="text-[#0d141b] dark:text-white text-xl font-bold leading-tight tracking-[-0.015em]">
                {{ config('app.name') }}
            </h2>
        </div>
    </a>
    <div class="flex flex-1 justify-end gap-8">
        <div class="hidden lg:flex items-center gap-9">
            <a class="text-[#0d141b] dark:text-gray-200 text-sm font-medium leading-normal hover:text-primary transition-colors {{ request()->routeIs('students') ? 'text-primary font-semibold' : '' }}"
                href="{{ route('students') }}" wire:navigate>
                Students    
            </a>
            <a class="text-[#0d141b] dark:text-gray-200 text-sm font-medium leading-normal hover:text-primary transition-colors {{ request()->routeIs('entreprenuers') ? 'text-primary font-semibold' : '' }}"
                href="{{ route('entrepreneurs') }}" wire:navigate>Entrepreneurs
            </a>
            <a class="text-[#0d141b] dark:text-gray-200 text-sm font-medium leading-normal hover:text-primary transition-colors {{ request()->routeIs('mentorship') ? 'text-primary font-semibold' : '' }}"
                href="{{ route('mentorship') }}" wire:navigate>Mentorship
            </a>
            <a class="text-[#0d141b] dark:text-gray-200 text-sm font-medium leading-normal hover:text-primary transition-colors {{ request()->routeIs('about') ? 'text-primary font-semibold' : '' }}"
                href="{{ route('about') }}" wire:navigate>About Us
            </a>
            <a class="text-[#0d141b] dark:text-gray-200 text-sm font-medium leading-normal hover:text-primary transition-colors {{ request()->routeIs('contact') ? 'text-primary font-semibold' : '' }}"
                href="{{ route('contact') }}" wire:navigate>Contact
            </a>
        </div>
        <div class="flex gap-2">
            <a href="" class="flex min-w-[84px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-primary text-white text-sm font-bold leading-normal tracking-[0.015em] hover:bg-primary/90 transition-colors" wire:navigate>
                <span class="truncate">Get Started</span>
            </a>
            <a href="" class="flex min-w-[84px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-[#e7edf3] dark:bg-gray-800 text-[#0d141b] dark:text-white text-sm font-bold leading-normal tracking-[0.015em] hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors" wire:navigate>
                <span class="truncate">Log In</span>
            </a>
        </div>
    </div>
</header>