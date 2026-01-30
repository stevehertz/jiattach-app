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
                        Enter your new password here.
                    </p>
                </div>
                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ session('status') }}
                    </div>
                @endif
                <form action="{{ route('password.update') }}" method="POST" class="flex flex-col gap-5">
                    @csrf

                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div class="flex flex-col gap-2">
                        <label class="text-[#0d141b] dark:text-gray-200 text-sm font-medium leading-normal"
                            for="email">Email</label>
                        <input
                            class="flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-[#0d141b] dark:text-white bg-[#f6f7f8] dark:bg-gray-800 focus:bg-transparent border border-[#cfdbe7] dark:border-gray-700 focus:border-primary px-4 py-3 text-sm focus:outline-none focus:ring-0 transition-colors placeholder:text-gray-400"
                            id="email" value="{{ old('email', $request->email) }}" name="email"
                            placeholder="e.g. johndoe@example.com" type="text" required autofocus
                            autocomplete="email"  />
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
                                autocomplete="current-password" viewable required autocomplete="new-password" />
                            <button
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 hover:text-primary transition-colors"
                                type="button">
                                <span class="material-symbols-outlined text-xl">visibility_off</span>
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <div class="flex justify-between items-center">
                            <label class="text-[#0d141b] dark:text-gray-200 text-sm font-medium leading-normal"
                                for="password">{{ __('Confirm Password') }}</label>
                        </div>
                        <div class="relative">
                            <input
                                class="flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-[#0d141b] dark:text-white bg-[#f6f7f8] dark:bg-gray-800 focus:bg-transparent border border-[#cfdbe7] dark:border-gray-700 focus:border-primary px-4 py-3 text-sm focus:outline-none focus:ring-0 transition-colors placeholder:text-gray-400"
                                id="password_confirmation" name="password_confirmation" placeholder="Retype your password" type="password"
                                autocomplete="current-password" viewable required autocomplete="new-password" />
                            <button
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 hover:text-primary transition-colors"
                                type="button">
                                <span class="material-symbols-outlined text-xl">visibility_off</span>
                            </button>
                        </div>
                    </div>

                    <button
                        class="flex w-full cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-5 bg-primary text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-primary/90 transition-all shadow-md hover:shadow-lg mt-2"
                        type="submit">
                        <span class="truncate">
                            {{ __('Reset Password') }}
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>

</x-layouts.guests>
