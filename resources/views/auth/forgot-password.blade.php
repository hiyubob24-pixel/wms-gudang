<x-guest-layout>
    <div class="app-fade-up mb-6">
        <p class="app-kicker">Reset Password</p>
        <h2 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-100">Lupa kata sandi?</h2>
        <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex justify-end pt-2">
            <x-primary-button class="w-full justify-center sm:w-auto">
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
