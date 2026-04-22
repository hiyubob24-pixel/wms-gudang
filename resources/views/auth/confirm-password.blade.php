<x-guest-layout>
    <div class="app-fade-up mb-6">
        <p class="app-kicker">Konfirmasi</p>
        <h2 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-100">Area aman</h2>
        <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end pt-2">
            <x-primary-button class="w-full justify-center sm:w-auto">
                {{ __('Confirm') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
