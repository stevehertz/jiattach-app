<x-layouts.guests>

    <div class="layout-container flex grow flex-col items-center justify-center p-4 py-12">
        <div class="w-full max-w-[480px] bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-[#cfdbe7] dark:border-gray-800 overflow-hidden">
            <div class="p-8 md:p-10 flex flex-col gap-6">
                <div class="flex flex-col items-center text-center gap-2">
                    <div
                        class="size-12 flex items-center justify-center rounded-full bg-green-50 dark:bg-green-900/20 text-primary mb-2">
                        <span class="material-symbols-outlined text-3xl">lock</span>
                    </div>
                    <h1 class="text-[#0d141b] dark:text-white text-2xl font-bold leading-tight tracking-[-0.015em]">
                        Welcome Back
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">
                        Enter your credentials to access your account.
                    </p>
                </div>
                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ session('status') }}
                    </div>
                @endif
                <form action="{{ route('login') }}" method="POST" class="flex flex-col gap-5">
                    @csrf
                    <div class="flex flex-col gap-2">
                        <label class="text-[#0d141b] dark:text-gray-200 text-sm font-medium leading-normal"
                            for="email">Email or Username</label>
                        <input
                            class="flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-[#0d141b] dark:text-white bg-[#f6f7f8] dark:bg-gray-800 focus:bg-transparent border border-[#cfdbe7] dark:border-gray-700 focus:border-primary px-4 py-3 text-sm focus:outline-none focus:ring-0 transition-colors placeholder:text-gray-400"
                            id="email" value="{{ old('email') }}" name="email"
                            placeholder="e.g. johndoe@example.com" type="text" required autofocus
                            autocomplete="email" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <div class="flex justify-between items-center">
                            <label class="text-[#0d141b] dark:text-gray-200 text-sm font-medium leading-normal"
                                for="password">Password</label>
                        </div>
                        <div class="relative">
                            <input
                                class="flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-[#0d141b] dark:text-white bg-[#f6f7f8] dark:bg-gray-800 focus:bg-transparent border border-[#cfdbe7] dark:border-gray-700 focus:border-primary px-4 py-3 text-sm focus:outline-none focus:ring-0 transition-colors placeholder:text-gray-400"
                                id="password" name="password" placeholder="Enter your password" type="password"
                                autocomplete="current-password" required viewable />
                            <button
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 hover:text-primary transition-colors"
                                type="button">
                                <span class="material-symbols-outlined text-xl">visibility_off</span>
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <div class="relative flex items-center">
                                <input
                                    class="peer h-5 w-5 cursor-pointer appearance-none rounded border border-[#cfdbe7] dark:border-gray-600 bg-white dark:bg-gray-800 checked:bg-primary checked:border-primary transition-all"
                                    type="checkbox" />
                                <span
                                    class="absolute text-white opacity-0 peer-checked:opacity-100 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 pointer-events-none">
                                    <span class="material-symbols-outlined text-sm font-bold">check</span>
                                </span>
                            </div>
                            <span
                                class="text-gray-600 dark:text-gray-300 text-sm font-medium group-hover:text-[#0d141b] dark:group-hover:text-white transition-colors">Remember
                                me</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a class="text-sm font-semibold text-primary hover:text-primary/80 transition-colors"
                                href="{{ route('password.request') }}" wire:navigate>Forgot Password?
                            </a>
                        @endif
                    </div>
                    <button
                        class="flex w-full cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-5 bg-primary text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-primary/90 transition-all shadow-md hover:shadow-lg mt-2"
                        type="submit">
                        <span class="truncate">Log In</span>
                    </button>
                </form>
                <div class="flex items-center gap-3 w-full">
                    <div class="h-px bg-[#e7edf3] dark:bg-gray-700 flex-1"></div>
                    <p class="text-gray-500 dark:text-gray-400 text-xs font-medium uppercase tracking-wider">Or
                        continue with</p>
                    <div class="h-px bg-[#e7edf3] dark:bg-gray-700 flex-1"></div>
                </div>
                <div class="flex gap-4">
                    <button
                        class="flex-1 flex items-center justify-center gap-2 h-10 rounded-lg border border-[#cfdbe7] dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                                fill="#4285F4"></path>
                            <path
                                d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                                fill="#34A853"></path>
                            <path
                                d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                                fill="#FBBC05"></path>
                            <path
                                d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                                fill="#EA4335"></path>
                        </svg>
                        <span class="text-[#0d141b] dark:text-white text-sm font-medium">Google</span>
                    </button>
                    <button
                        class="flex-1 flex items-center justify-center gap-2 h-10 rounded-lg border border-[#cfdbe7] dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-5 h-5 text-[#0077b5]" fill="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z">
                            </path>
                        </svg>
                        <span class="text-[#0d141b] dark:text-white text-sm font-medium">LinkedIn</span>
                    </button>
                </div>
                <div class="text-center mt-2">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Don't have an account?
                        <a class="font-bold text-primary hover:underline" href="{{ route('register') }}" wire:navigate>Sign up</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

</x-layouts.guests>
