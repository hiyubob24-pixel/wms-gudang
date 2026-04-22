@php
    $defaultTitles = [
        'success' => 'Berhasil diproses',
        'error' => 'Aksi belum berhasil',
        'warning' => 'Perlu perhatian',
        'info' => 'Informasi',
    ];

    $defaultDurations = [
        'success' => 4200,
        'error' => 6200,
        'warning' => 5600,
        'info' => 4600,
    ];

    $icons = [
        'success' => 'OK',
        'error' => '!',
        'warning' => '!',
        'info' => 'i',
    ];

    $flashToasts = [];

    $pushToast = function (string $type, string $message, ?string $title = null, ?int $duration = null, bool $persistent = false) use (&$flashToasts, $defaultTitles, $defaultDurations, $icons) {
        $flashToasts[] = [
            'id' => uniqid('toast-', true),
            'type' => $type,
            'title' => $title ?? ($defaultTitles[$type] ?? 'Informasi'),
            'message' => $message,
            'icon' => $icons[$type] ?? 'i',
            'duration' => $duration ?? ($defaultDurations[$type] ?? 4800),
            'persistent' => $persistent,
        ];
    };

    if (session()->has('toast')) {
        $queuedToasts = session('toast');
        $queuedToasts = is_array($queuedToasts) && array_is_list($queuedToasts) ? $queuedToasts : [$queuedToasts];

        foreach ($queuedToasts as $toast) {
            if (! is_array($toast) || empty($toast['message'])) {
                continue;
            }

            $pushToast(
                $toast['type'] ?? 'info',
                (string) $toast['message'],
                $toast['title'] ?? null,
                isset($toast['duration']) ? (int) $toast['duration'] : null,
                (bool) ($toast['persistent'] ?? false),
            );
        }
    }

    if (session()->has('success')) {
        $pushToast('success', session('success'));
    }

    if (session()->has('error')) {
        $pushToast('error', session('error'));
    }

    if (session()->has('warning')) {
        $pushToast('warning', session('warning'));
    }

    if (session()->has('info')) {
        $pushToast('info', session('info'));
    }

    if (session()->has('error_stok')) {
        $pushToast('error', session('error_stok'), 'Stok belum mencukupi', 6800);
    }
@endphp

@if (! empty($flashToasts))
    <div
        x-data="appToastStack(@js($flashToasts))"
        x-init="init()"
        class="pointer-events-none fixed inset-x-0 top-4 z-[80]"
        aria-live="polite"
        aria-atomic="true"
    >
        <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 sm:items-end sm:px-6 lg:px-8">
            <template x-for="toast in toasts" :key="toast.id">
                <section
                    x-cloak
                    x-show="toast.visible"
                    @mouseenter="pause(toast.id)"
                    @mouseleave="resume(toast.id)"
                    x-transition:enter="transition-all duration-500 ease-[cubic-bezier(0.34,1.56,0.64,1)]"
                    x-transition:enter-start="translate-y-8 opacity-0 scale-90 blur-[4px] sm:translate-x-12 sm:translate-y-0 sm:scale-75"
                    x-transition:enter-end="translate-y-0 opacity-100 scale-100 blur-0 sm:translate-x-0"
                    x-transition:leave="transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)]"
                    x-transition:leave-start="translate-y-0 opacity-100 scale-100 blur-0"
                    x-transition:leave-end="-translate-y-4 opacity-0 scale-95 blur-sm sm:translate-x-10 sm:-translate-y-2"
                    class="app-toast pointer-events-auto w-full max-w-sm overflow-hidden rounded-[1.4rem] border p-4 shadow-xl dark:shadow-none backdrop-blur xl:max-w-md"
                    :class="`app-toast--${toast.type}`"
                    role="status"
                >
                    <div class="flex items-start gap-3">
                        <div class="app-toast__icon" :class="`app-toast__icon--${toast.type}`" aria-hidden="true">
                            <span x-text="toast.icon"></span>
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-slate-900 dark:text-slate-100" x-text="toast.title"></p>
                            <p class="mt-1 text-sm leading-6 text-slate-600 dark:text-slate-400" x-text="toast.message"></p>
                        </div>

                        <button type="button" class="app-toast__close" @click="dismiss(toast.id)" aria-label="Tutup notifikasi">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="mt-3 h-1 overflow-hidden rounded-full bg-slate-200/70" x-show="!toast.persistent">
                        <span class="app-toast__progress" :class="`app-toast__progress--${toast.type}`" :style="progressStyle(toast)"></span>
                    </div>
                </section>
            </template>
        </div>
    </div>
@endif
