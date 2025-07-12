<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="max-w-md mx-auto mt-12 space-y-6">
        @csrf

        <!-- Login Field -->
        <div>
            <x-input-label for="login" :value="__('Nama Pengguna atau Emel')" />
            <x-text-input id="login" class="block mt-1 w-full" type="text" name="login" :value="old('login')" required autofocus autocomplete="login" />
            <x-input-error :messages="$errors->get('login')" class="mt-2" />
        </div>

        <!-- Password Field -->
        <div>
            <x-input-label for="password" :value="__('Kata Laluan')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember + Forgot Password -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Ingat saya') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 hover:underline">
                    {{ __('Lupa kata laluan?') }}
                </a>
            @endif

        </div>

        <!-- Submit Button -->
        <div class="flex justify-end">

            <x-primary-button class="px-6 py-2 rounded-md">
                {{ __('Log Masuk') }}
            </x-primary-button>
        </div>
    </form>

    <!-- Register Link at Bottom -->
    <div class="mt-6 text-center">
        <a href="{{ route('register') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
            {{ __('Tiada akaun? Daftar') }}
        </a>
    </div>
</x-guest-layout>
