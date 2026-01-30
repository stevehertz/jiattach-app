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
                        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
                    </p>
                </div>
                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ session('status') }}
                    </div>
                @endif
                <form action="{{ route('password.email') }}" method="POST" class="flex flex-col gap-5">
                    @csrf
                    <div class="flex flex-col gap-2">
                        <label class="text-[#0d141b] dark:text-gray-200 text-sm font-medium leading-normal"
                            for="email">Email </label>
                        <input
                            class="flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-[#0d141b] dark:text-white bg-[#f6f7f8] dark:bg-gray-800 focus:bg-transparent border border-[#cfdbe7] dark:border-gray-700 focus:border-primary px-4 py-3 text-sm focus:outline-none focus:ring-0 transition-colors placeholder:text-gray-400"
                            id="email" value="{{ old('email') }}" name="email"
                            placeholder="e.g. johndoe@example.com" type="text" required autofocus
                            autocomplete="email" />
                    </div>

                    <button
                        class="flex w-full cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-5 bg-primary text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-primary/90 transition-all shadow-md hover:shadow-lg mt-2"
                        type="submit">
                        <span class="truncate">
                            Request new password
                        </span>
                    </button>
                </form>
                <div class="text-center mt-2">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Remembered your password?
                        <a class="font-bold text-primary hover:underline" href="{{ route('login') }}" wire:navigate>Sign in</a>
                    </p>
                </div>
            </div>
        </div>
    </div>



</x-layouts.guests>
