<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-slate-200 leading-tight">Dashboard Staff</h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="overflow-hidden rounded-3xl border border-emerald-100 bg-gradient-to-br from-emerald-50 via-white to-sky-50 shadow-sm dark:shadow-none">
                <div class="grid gap-6 p-5 sm:p-6 lg:grid-cols-[1.5fr_1fr] lg:items-center">
                    <div>
                        <span class="inline-flex rounded-full bg-white dark:bg-slate-900 px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-emerald-700 dark:text-emerald-300 shadow-sm dark:shadow-none">
                            Ringkasan Operasional
                        </span>
                        <h3 class="mt-4 text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-100 sm:text-3xl">
                            Aktivitas gudang Anda kini lebih mudah dipantau.
                        </h3>
                        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600 dark:text-slate-400 sm:text-base">
                            Pantau jumlah transaksi hari ini, arus barang 7 hari terakhir, dan lanjutkan pencatatan barang masuk atau keluar langsung dari dashboard.
                        </p>

                        <div class="mt-5 flex flex-col gap-3 sm:flex-row">
                            <a href="{{ route('stock-in.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm dark:shadow-none transition hover:bg-blue-700">
                                Input Barang Masuk
                            </a>
                            <a href="{{ route('stock-out.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-red-600 px-4 py-3 text-sm font-semibold text-white shadow-sm dark:shadow-none transition hover:bg-red-700">
                                Input Barang Keluar
                            </a>
                        </div>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-3 lg:grid-cols-1">
                        <div class="rounded-2xl border border-white/80 bg-white dark:bg-slate-900/90 p-4 shadow-sm dark:shadow-none">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Transaksi Hari Ini</p>
                            <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-slate-100">{{ $todayStockInCount + $todayStockOutCount }}</p>
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ $todayStockInCount }} masuk dan {{ $todayStockOutCount }} keluar</p>
                        </div>
                        <div class="rounded-2xl border border-white/80 bg-white dark:bg-slate-900/90 p-4 shadow-sm dark:shadow-none">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Produk Minggu Ini</p>
                            <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-slate-100">{{ $productsHandledThisWeek }}</p>
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Jumlah produk yang Anda tangani</p>
                        </div>
                        <div class="rounded-2xl border border-white/80 bg-white dark:bg-slate-900/90 p-4 shadow-sm dark:shadow-none">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Rak Aktif Hari Ini</p>
                            <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-slate-100">{{ $activeRaksToday }}</p>
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Lokasi rak yang tersentuh hari ini</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <article class="overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-5 shadow-sm dark:shadow-none">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Aktivitas Hari Ini</p>
                    <p class="mt-3 text-3xl font-bold text-slate-900 dark:text-slate-100">{{ $todayStockInCount + $todayStockOutCount }}</p>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Total transaksi yang Anda catat hari ini.</p>
                </article>

                <article class="overflow-hidden rounded-2xl border border-blue-100 bg-gradient-to-br from-blue-50 to-white p-5 shadow-sm dark:shadow-none">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-blue-500">Qty Masuk Minggu Ini</p>
                    <p class="mt-3 text-3xl font-bold text-slate-900 dark:text-slate-100">{{ $weekQtyIn }}</p>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Akumulasi barang masuk yang Anda input sejak awal minggu.</p>
                </article>

                <article class="overflow-hidden rounded-2xl border border-red-100 bg-gradient-to-br from-red-50 to-white p-5 shadow-sm dark:shadow-none">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-red-500">Qty Keluar Minggu Ini</p>
                    <p class="mt-3 text-3xl font-bold text-slate-900 dark:text-slate-100">{{ $weekQtyOut }}</p>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Akumulasi barang keluar yang Anda catat sejak awal minggu.</p>
                </article>

                <article class="overflow-hidden rounded-2xl border border-amber-100 bg-gradient-to-br from-amber-50 to-white p-5 shadow-sm dark:shadow-none">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-amber-600">Selisih Minggu Ini</p>
                    <p class="mt-3 text-3xl font-bold text-slate-900 dark:text-slate-100">{{ $weekQtyIn - $weekQtyOut }}</p>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Perbandingan kuantitas masuk dan keluar yang Anda proses.</p>
                </article>
            </section>

            <section class="grid gap-4 xl:grid-cols-[1.65fr_1fr] xl:gap-6">
                <div class="overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-4 shadow-sm dark:shadow-none sm:rounded-3xl sm:p-6">
                    <div class="flex flex-col gap-2 border-b border-slate-100 dark:border-slate-700/60 pb-4 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Grafik Aktivitas 7 Hari Terakhir</h3>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Membantu Anda melihat ritme input barang masuk dan keluar selama seminggu terakhir.</p>
                        </div>
                        <span class="text-xs font-medium uppercase tracking-[0.18em] text-slate-400">By Qty</span>
                    </div>

                    <div class="relative mt-4 h-72 w-full sm:mt-5 sm:h-80">
                        <canvas id="staffActivityChart"></canvas>
                    </div>
                </div>

                <div class="overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-4 shadow-sm dark:shadow-none sm:rounded-3xl sm:p-6">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Fokus Operasional</h3>
                    <div class="mt-4 space-y-3 sm:mt-5">
                        <div class="rounded-xl border border-slate-100 dark:border-slate-700/60 bg-slate-50 dark:bg-slate-800 p-4 sm:rounded-2xl">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Arah Aktivitas</p>
                            <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-slate-100">
                                @if($weekQtyIn > $weekQtyOut)
                                    Minggu ini lebih banyak aktivitas penerimaan barang.
                                @elseif($weekQtyIn < $weekQtyOut)
                                    Minggu ini lebih banyak aktivitas pengeluaran barang.
                                @else
                                    Aktivitas masuk dan keluar minggu ini seimbang.
                                @endif
                            </p>
                        </div>

                        <div class="rounded-xl border border-slate-100 dark:border-slate-700/60 bg-slate-50 dark:bg-slate-800 p-4 sm:rounded-2xl">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Prioritas Cepat</p>
                            <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">Gunakan tombol input di atas untuk langsung mencatat aktivitas baru tanpa perlu masuk ke menu riwayat terlebih dahulu.</p>
                        </div>

                        <div class="rounded-xl border border-slate-100 dark:border-slate-700/60 bg-slate-50 dark:bg-slate-800 p-4 sm:rounded-2xl">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Cakupan Kerja</p>
                            <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">Anda sudah menangani {{ $productsHandledThisWeek }} produk minggu ini dan menyentuh {{ $activeRaksToday }} rak hari ini.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-4 shadow-sm dark:shadow-none sm:rounded-3xl sm:p-6">
                <div class="flex flex-col gap-2 border-b border-slate-100 dark:border-slate-700/60 pb-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Aktivitas Terakhir Anda</h3>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Daftar transaksi terbaru yang Anda catat untuk membantu pengecekan cepat.</p>
                    </div>
                    <span class="text-xs font-medium uppercase tracking-[0.18em] text-slate-400">Terbaru ke Terdahulu</span>
                </div>

                <div class="mt-5 space-y-3">
                    @forelse($recentActivities as $activity)
                        <article class="flex flex-col gap-3 rounded-2xl border border-slate-100 dark:border-slate-700/60 p-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $activity['type'] === 'in' ? 'bg-blue-100 dark:bg-indigo-900/30 text-blue-700' : 'bg-red-100 dark:bg-rose-900/30 text-red-700' }}">
                                        {{ $activity['label'] }}
                                    </span>
                                    <p class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($activity['date_time'])->format('d M Y, H:i') }}</p>
                                </div>
                                <p class="mt-2 break-words text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $activity['product_name'] }}</p>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Rak: {{ $activity['rak_name'] }}</p>
                            </div>

                            <div class="shrink-0">
                                <span class="inline-flex rounded-full px-3 py-1 text-sm font-bold {{ $activity['type'] === 'in' ? 'bg-blue-100 dark:bg-indigo-900/30 text-blue-700' : 'bg-red-100 dark:bg-rose-900/30 text-red-700' }}">
                                    {{ $activity['type'] === 'in' ? '+' : '-' }}{{ $activity['quantity'] }}
                                </span>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500 dark:text-slate-400">
                            Belum ada aktivitas yang tercatat atas nama Anda. Mulai dari input barang masuk atau barang keluar untuk melihat ringkasannya di dashboard.
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const canvas = document.getElementById('staffActivityChart');

            if (!canvas) {
                return;
            }

            const isMobile = window.matchMedia('(max-width: 640px)').matches;
            const labels = {!! json_encode($staffChartLabels) !!};
            const dataIn = {!! json_encode($staffChartDataIn) !!};
            const dataOut = {!! json_encode($staffChartDataOut) !!};

            new Chart(canvas.getContext('2d'), {
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        {
                            label: 'Barang Masuk',
                            data: dataIn,
                            borderColor: 'rgba(37, 99, 235, 1)',
                            backgroundColor: 'rgba(37, 99, 235, 0.12)',
                            fill: true,
                            tension: 0.35,
                            pointRadius: 4,
                            pointHoverRadius: 5,
                            borderWidth: 2
                        },
                        {
                            label: 'Barang Keluar',
                            data: dataOut,
                            borderColor: 'rgba(220, 38, 38, 1)',
                            backgroundColor: 'rgba(220, 38, 38, 0.10)',
                            fill: true,
                            tension: 0.35,
                            pointRadius: 4,
                            pointHoverRadius: 5,
                            borderWidth: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(148, 163, 184, 0.15)'
                            },
                            ticks: {
                                precision: 0
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: isMobile ? 'bottom' : 'top',
                            labels: {
                                usePointStyle: true,
                                padding: isMobile ? 12 : 20
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.92)',
                            padding: 10,
                            titleFont: {
                                size: 13
                            },
                            bodyFont: {
                                size: 12
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
