<x-guest-layout>
    <div class="app-fade-up mb-6">
        <p class="app-kicker">Verifikasi Email</p>
        <h2 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-100">Satu langkah lagi</h2>
        <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">
            {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="app-status mb-4 text-sm font-medium text-green-600">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button class="w-full justify-center sm:w-auto">
                    {{ __('Resend Verification Email') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="app-link rounded-md text-sm font-medium hover:underline focus:outline-none focus:ring-2 focus:ring-sky-300 focus:ring-offset-2">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
