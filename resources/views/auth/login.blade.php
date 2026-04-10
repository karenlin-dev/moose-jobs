<x-guest-layout>
    <div class="w-full max-w-md mx-auto">

        <!-- Title -->
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Welcome Back</h1>
            <p class="text-sm text-gray-500 mt-1">
                Login to your MooseJobs account
            </p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Email -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input
                    id="email"
                    class="block mt-1 w-full rounded-lg"
                    type="email"
                    name="email"
                    :value="old('email')"
                    required
                    autofocus
                    autocomplete="username"
                />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input
                    id="password"
                    class="block mt-1 w-full rounded-lg"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember -->
            <div class="flex items-center justify-between">
                <label class="inline-flex items-center">
                    <input type="checkbox"
                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                           name="remember">
                    <span class="ms-2 text-sm text-gray-600">Remember me</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-sm text-indigo-600 hover:underline"
                       href="{{ route('password.request') }}">
                        Forgot password?
                    </a>
                @endif
            </div>

            <!-- Login Button -->
            <x-primary-button class="w-full justify-center py-2">
                {{ __('Log in') }}
            </x-primary-button>
        </form>

        <!-- Divider -->
        <div class="my-6 flex items-center gap-4">
            <div class="flex-1 h-px bg-gray-200"></div>
            <span class="text-sm text-gray-500">or continue with</span>
            <div class="flex-1 h-px bg-gray-200"></div>
        </div>

        <!-- Social Login -->
        <div class="flex flex-col gap-3">
            <a href="{{ route('social.redirect', 'google') }}"
               class="flex items-center justify-center gap-2 px-4 py-2 border rounded-lg hover:bg-gray-50 transition">
                Continue with Google
            </a>

            <a href="{{ route('social.redirect', 'facebook') }}"
               class="flex items-center justify-center gap-2 px-4 py-2 border rounded-lg hover:bg-gray-50 transition">
                Continue with Facebook
            </a>
        </div>

        <!-- Register Link -->
        <div class="text-center mt-6 text-sm text-gray-600">
            Don’t have an account?
            <a href="{{ route('register') }}"
               class="text-indigo-600 hover:underline font-medium">
                Sign up
            </a>
        </div>

    </div>
</x-guest-layout>