<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="view-transition" content="same-origin">

        <title>{{ config('app.name', 'Laravel') }}</title>
		<link rel="icon" type="image/png" href="{{ asset('image/logo.png') }}">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- Inline style for page progress bar (loads before CSS) --}}
        <style>
            #app-progress-bar {
                position: fixed;
                top: 0;
                left: 0;
                z-index: 9999;
                height: 3px;
                width: 0%;
                background: linear-gradient(90deg, #6366f1, #a855f7, #f43f5e);
                border-radius: 0 9999px 9999px 0;
                transition: width 200ms ease, opacity 400ms ease;
                opacity: 0;
                pointer-events: none;
                box-shadow: 0 0 12px rgba(167, 139, 250, 0.7);
            }
        </style>

        <script>
            // Dark mode initialization (FOUC prevention)
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark')
            } else {
                document.documentElement.classList.remove('dark')
            }
        </script>
    </head>
    @php
        $showAdminAiAssistant = auth()->check()
            && auth()->user()?->role === 'admin'
            && request()->routeIs('dashboard', 'stock-in.*', 'stock-out.*', 'products.*', 'raks.*', 'users.*', 'stocks.*', 'reports.*');
    @endphp
    <body class="app-shell font-sans antialiased" x-data="{ darkMode: document.documentElement.classList.contains('dark') }" @toggle-dark-mode.window="darkMode = !darkMode" x-init="$watch('darkMode', val => { if(val) { document.documentElement.classList.add('dark'); localStorage.theme = 'dark'; } else { document.documentElement.classList.remove('dark'); localStorage.theme = 'light'; } })">

        {{-- Premium page transition progress bar --}}
        <div id="app-progress-bar"></div>

        <div class="min-h-screen overflow-x-clip bg-white dark:bg-slate-900">
            @include('layouts.navigation')

            @isset($header)
                <header class="app-page-header bg-white dark:bg-slate-900 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main class="app-main">
                {{ $slot }}
            </main>
        </div>

        @if ($showAdminAiAssistant)
            <x-admin-ai-assistant />
        @endif

        <x-command-palette />
        <x-app-toast-stack />

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        {{-- Premium page navigation progress bar script --}}
        <script>
            (function () {
                const bar = document.getElementById('app-progress-bar');
                if (!bar) return;

                let timer = null;
                let progress = 0;

                function start() {
                    progress = 0;
                    bar.style.opacity = '1';
                    bar.style.width = '0%';
                    tick();
                }

                function tick() {
                    if (progress < 85) {
                        progress += Math.random() * 12;
                        if (progress > 85) progress = 85;
                        bar.style.width = progress + '%';
                        timer = setTimeout(tick, 200 + Math.random() * 300);
                    }
                }

                function finish() {
                    clearTimeout(timer);
                    bar.style.width = '100%';
                    setTimeout(() => {
                        bar.style.opacity = '0';
                        setTimeout(() => {
                            bar.style.width = '0%';
                        }, 400);
                    }, 200);
                }

                // Trigger on all navigation links
                document.addEventListener('click', function (e) {
                    const link = e.target.closest('a');
                    if (link && link.href && !link.target && !link.href.startsWith('#') &&
                        link.href.startsWith(window.location.origin) &&
                        !e.metaKey && !e.ctrlKey && !e.shiftKey) {
                        start();
                    }
                });

                // Trigger on form submits
                document.addEventListener('submit', function (e) {
                    if (!e.defaultPrevented) {
                        start();
                    }
                });

                // Finish on page load
                window.addEventListener('load', finish);
                window.addEventListener('pageshow', finish);
            })();
        </script>

        @stack('scripts')
    </body>
</html>
