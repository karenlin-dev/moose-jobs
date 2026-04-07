<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input
                id="email"
                class="block mt-1 w-full"
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
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input
                id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                required
                autocomplete="current-password"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label class="inline-flex items-center">
                <input
                    type="checkbox"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                    name="remember"
                >
                <span class="ms-2 text-sm text-gray-600">
                    {{ __('Remember me') }}
                </span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a
                    class="underline text-sm text-gray-600 hover:text-gray-900"
                    href="{{ route('password.request') }}"
                >
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
    <!-- 分隔线 -->
    <div class="mt-6 text-center text-gray-500">
        {{ __('Or login with') }}
    </div>

    <!-- 第三方登录按钮 -->
    <div class="flex justify-center mt-4 gap-4">
        <!-- Google -->
        <a href="{{ route('social.redirect', 'google') }}"
           class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
            {{ __('Google') }}
        </a>

        <!-- Facebook -->
        <a href="{{ route('social.redirect', 'facebook') }}"
           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            {{ __('Facebook') }}
        </a>
    </div>
</x-guest-layout>
