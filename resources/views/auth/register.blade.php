<x-guest-layout>
    <div class="app-fade-up mb-6">
        <p class="app-kicker">Registrasi</p>
        <h2 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-100">Buat akun baru</h2>
        <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">
            Daftarkan akun untuk mulai mengakses pengelolaan gudang dan pelacakan stok secara real-time.
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:items-center sm:justify-between">
            <a class="app-link text-sm font-medium hover:underline rounded-md focus:outline-none focus:ring-2 focus:ring-sky-300 focus:ring-offset-2" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="w-full justify-center sm:w-auto">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
