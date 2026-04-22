<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
		<link rel="icon" type="image/png" href="{{ asset('image/logo.png') }}">
         <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Dark mode initialization (FOUC prevention) -->
        <script>
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark')
            } else {
                document.documentElement.classList.remove('dark')
            }
        </script>
    </head>
    <body class="guest-shell font-sans text-gray-900 dark:text-slate-100 antialiased" x-data="{ darkMode: document.documentElement.classList.contains('dark') }" @toggle-dark-mode.window="darkMode = !darkMode" x-init="$watch('darkMode', val => { if(val) { document.documentElement.classList.add('dark'); localStorage.theme = 'dark'; } else { document.documentElement.classList.remove('dark'); localStorage.theme = 'light'; } })">
        <div class="fixed top-4 right-4 z-50 sm:top-6 sm:right-6">
            <button x-data="{ isDark: document.documentElement.classList.contains('dark') }" @toggle-dark-mode.window="isDark = document.documentElement.classList.contains('dark')" @click="$dispatch('toggle-dark-mode')" type="button" class="app-nav-pill inline-flex items-center justify-center rounded-2xl border border-slate-200 dark:border-slate-700/60 bg-white/80 dark:bg-slate-800/80 p-2.5 text-slate-500 dark:text-slate-400 shadow-sm backdrop-blur transition hover:border-indigo-200 hover:bg-indigo-50 dark:hover:border-indigo-700/50 dark:hover:bg-indigo-900/40">
                <svg x-show="!isDark" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
                <svg x-show="isDark" style="display: none;" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </button>
        </div>

        <div class="min-h-screen bg-white dark:bg-slate-900 px-4 py-8 sm:px-6">
            <div class="app-auth-stage mx-auto flex min-h-[calc(100vh-4rem)] w-full max-w-5xl flex-col items-center justify-center">
                <div class="app-fade-up text-center">
                    <a href="/" class="inline-flex items-center justify-center">
                        <span class="flex h-20 w-20 items-center justify-center overflow-hidden" style="border-radius:9999px;">
                            <x-application-logo class="block h-full w-full" />
                        </span>
                    </a>
                    <p class="app-kicker mt-6">Widhi Limited Company</p>
                    <h1 class="mt-3 text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-100 sm:text-3xl">MANAGEMENT SYSTEM</h1>
                    <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-slate-500 dark:text-slate-400 sm:text-base">
                        Kelola stok, transaksi &amp; laporan secara efisien.
                    </p>
                </div>

                <div class="app-auth-card app-fade-up app-delay-1 mt-8 w-full sm:max-w-md">
                    <div class="px-6 py-6 sm:px-8 sm:py-7">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>

        <x-app-toast-stack />
    </body>
</html>
