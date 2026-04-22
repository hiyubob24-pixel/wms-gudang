    <x-app-layout>
        <x-slot name="header">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-slate-100 tracking-tight">
                        Dashboard Operasional
                    </h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Ringkasan aktivitas gudang hari ini.</p>
                </div>
                <div class="hidden sm:block">
                    <button type="button" class="inline-flex items-center gap-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 shadow-sm transition hover:bg-slate-50 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900">
                        <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Unduh Laporan
                    </button>
                </div>
            </div>
        </x-slot>

        <div class="py-6 sm:py-10">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Stat Cards -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Card 1 -->
                    <div class="app-fade-up relative overflow-hidden rounded-2xl bg-white dark:bg-slate-900 p-6 shadow-sm border border-slate-200 dark:border-slate-700 group">
                        <dt>
                            <div class="absolute rounded-xl bg-indigo-50 dark:bg-indigo-900/30 p-3">
                                <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                            </div>
                            <p class="ml-16 truncate text-sm font-medium text-slate-500 dark:text-slate-400">Total Stok Masuk</p>
                        </dt>
                        <dd class="ml-16 flex items-baseline pb-1 sm:pb-2">
                            <p class="text-2xl font-bold text-slate-900 dark:text-slate-100">1,240</p>
                            <p class="ml-2 flex items-baseline text-sm font-semibold text-emerald-600 dark:text-emerald-400">
                                <svg class="h-4 w-4 self-center" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                                <span class="sr-only">Naik</span>
                                12%
                            </p>
                        </dd>
                    </div>

                    <!-- Card 2 -->
                    <div class="app-fade-up app-delay-1 relative overflow-hidden rounded-2xl bg-white dark:bg-slate-900 p-6 shadow-sm border border-slate-200 dark:border-slate-700 group">
                        <dt>
                            <div class="absolute rounded-xl bg-sky-50 dark:bg-sky-900/30 p-3">
                                <svg class="h-6 w-6 text-sky-600 dark:text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <p class="ml-16 truncate text-sm font-medium text-slate-500 dark:text-slate-400">Barang Keluar</p>
                        </dt>
                        <dd class="ml-16 flex items-baseline pb-1 sm:pb-2">
                            <p class="text-2xl font-bold text-slate-900 dark:text-slate-100">865</p>
                            <p class="ml-2 flex items-baseline text-sm font-semibold text-rose-600 dark:text-rose-400">
                                <svg class="h-4 w-4 self-center" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                <span class="sr-only">Turun</span>
                                4.5%
                            </p>
                        </dd>
                    </div>

                    <!-- Card 3 -->
                    <div class="app-fade-up app-delay-2 relative overflow-hidden rounded-2xl bg-white dark:bg-slate-900 p-6 shadow-sm border border-slate-200 dark:border-slate-700 group">
                        <dt>
                            <div class="absolute rounded-xl bg-emerald-50 dark:bg-emerald-900/30 p-3">
                                <svg class="h-6 w-6 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <p class="ml-16 truncate text-sm font-medium text-slate-500 dark:text-slate-400">Kapasitas Rak</p>
                        </dt>
                        <dd class="ml-16 flex items-baseline pb-1 sm:pb-2">
                            <p class="text-2xl font-bold text-slate-900 dark:text-slate-100">72%</p>
                        </dd>
                        <!-- Mini progress bar -->
                        <div class="absolute bottom-0 left-0 right-0 h-1.5 bg-slate-100 dark:bg-slate-800">
                            <div class="h-1.5 bg-emerald-500 dark:bg-emerald-400" style="width: 72%"></div>
                        </div>
                    </div>

                    <!-- Card 4 -->
                    <div class="app-fade-up app-delay-3 relative overflow-hidden rounded-2xl bg-white dark:bg-slate-900 p-6 shadow-sm border border-slate-200 dark:border-slate-700 group">
                        <dt>
                            <div class="absolute rounded-xl bg-amber-50 dark:bg-amber-900/30 p-3">
                                <svg class="h-6 w-6 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <p class="ml-16 truncate text-sm font-medium text-slate-500 dark:text-slate-400">Stok Menipis</p>
                        </dt>
                        <dd class="ml-16 flex items-baseline pb-1 sm:pb-2">
                            <p class="text-2xl font-bold text-slate-900 dark:text-slate-100">14</p>
                            <p class="ml-2 flex items-baseline text-sm font-semibold text-slate-500 dark:text-slate-400">Item perlu restock</p>
                        </dd>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Chart -->
                    <div class="app-fade-up app-delay-4 lg:col-span-2 overflow-hidden rounded-2xl bg-white dark:bg-slate-900 shadow-sm border border-slate-200 dark:border-slate-700">
                        <div class="border-b border-slate-200 dark:border-slate-700/60 bg-slate-50/50 dark:bg-slate-800/30 px-6 py-4">
                            <h3 class="text-base font-semibold leading-6 text-slate-900 dark:text-slate-100">Tren Barang Masuk vs Keluar</h3>
                        </div>
                        <div class="p-6 relative h-80">
                            <canvas id="mainChart"></canvas>
                        </div>
                    </div>

                    <!-- Activity List -->
                    <div class="app-fade-up app-delay-5 overflow-hidden rounded-2xl bg-white dark:bg-slate-900 shadow-sm border border-slate-200 dark:border-slate-700">
                        <div class="border-b border-slate-200 dark:border-slate-700/60 bg-slate-50/50 dark:bg-slate-800/30 px-6 py-4 flex items-center justify-between">
                            <h3 class="text-base font-semibold leading-6 text-slate-900 dark:text-slate-100">Aktivitas Terbaru</h3>
                        </div>
                        <div class="p-6">
                            <div class="flow-root">
                                <ul role="list" class="-mb-8">
                                    <li>
                                        <div class="relative pb-8">
                                            <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-slate-200 dark:bg-slate-700" aria-hidden="true"></span>
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center ring-8 ring-white dark:ring-slate-900">
                                                        <svg class="h-4 w-4 text-emerald-600 dark:text-emerald-400" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                                    <div>
                                                        <p class="text-sm text-slate-500 dark:text-slate-400">Stok masuk <span class="font-medium text-slate-900 dark:text-slate-200">Indomie Goreng (50 dus)</span></p>
                                                    </div>
                                                    <div class="whitespace-nowrap text-right text-sm text-slate-500 dark:text-slate-400">
                                                        <time datetime="2024-04-22">10m</time>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="relative pb-8">
                                            <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-slate-200 dark:bg-slate-700" aria-hidden="true"></span>
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full bg-sky-100 dark:bg-sky-900/40 flex items-center justify-center ring-8 ring-white dark:ring-slate-900">
                                                        <svg class="h-4 w-4 text-sky-600 dark:text-sky-400" viewBox="0 0 20 20" fill="currentColor">
                                                            <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6z" />
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                                    <div>
                                                        <p class="text-sm text-slate-500 dark:text-slate-400"><span class="font-medium text-slate-900 dark:text-slate-200">Budi</span> mengeluarkan 12 unit Kopi Kapal Api</p>
                                                    </div>
                                                    <div class="whitespace-nowrap text-right text-sm text-slate-500 dark:text-slate-400">
                                                        <time datetime="2024-04-22">1h</time>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="relative pb-8">
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center ring-8 ring-white dark:ring-slate-900">
                                                        <svg class="h-4 w-4 text-amber-600 dark:text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                                    <div>
                                                        <p class="text-sm text-slate-500 dark:text-slate-400">Peringatan: Stok <span class="font-medium text-slate-900 dark:text-slate-200">Minyak Goreng</span> menipis</p>
                                                    </div>
                                                    <div class="whitespace-nowrap text-right text-sm text-slate-500 dark:text-slate-400">
                                                        <time datetime="2024-04-22">2h</time>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const isDarkMode = document.documentElement.classList.contains('dark');
                const textColor = isDarkMode ? '#cbd5e1' : '#64748b';
                const gridColor = isDarkMode ? 'rgba(51, 65, 85, 0.4)' : 'rgba(226, 232, 240, 0.6)';

                const ctx = document.getElementById('mainChart');
                if (ctx) {
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                            datasets: [
                                {
                                    label: 'Barang Masuk',
                                    data: [65, 59, 80, 81, 56, 55, 40],
                                    borderColor: '#4f46e5',
                                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                                    borderWidth: 2,
                                    tension: 0.4,
                                    fill: true
                                },
                                {
                                    label: 'Barang Keluar',
                                    data: [28, 48, 40, 19, 86, 27, 90],
                                    borderColor: '#10b981',
                                    backgroundColor: 'transparent',
                                    borderWidth: 2,
                                    tension: 0.4,
                                    borderDash: [5, 5]
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    labels: { color: textColor, font: { family: 'Inter, sans-serif', size: 12 } }
                                },
                                tooltip: {
                                    backgroundColor: isDarkMode ? '#1e293b' : '#ffffff',
                                    titleColor: isDarkMode ? '#f8fafc' : '#0f172a',
                                    bodyColor: isDarkMode ? '#cbd5e1' : '#475569',
                                    borderColor: isDarkMode ? '#334155' : '#e2e8f0',
                                    borderWidth: 1,
                                    padding: 10,
                                    boxPadding: 4,
                                    usePointStyle: true
                                }
                            },
                            scales: {
                                y: {
                                    grid: { color: gridColor, drawBorder: false },
                                    ticks: { color: textColor, padding: 10 }
                                },
                                x: {
                                    grid: { display: false, drawBorder: false },
                                    ticks: { color: textColor, padding: 10 }
                                }
                            },
                            interaction: {
                                mode: 'index',
                                intersect: false,
                            },
                        }
                    });
                }
            });
        </script>
        @endpush
    </x-app-layout>
