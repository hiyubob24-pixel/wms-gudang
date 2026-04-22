<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-slate-200 leading-tight">
            {{ __('Dashboard Admin WMS') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Total Produk Card -->
                <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-slate-900/80 p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 transition duration-300 hover:shadow-md hover:border-indigo-300 dark:hover:border-indigo-500/50" style="isolation: isolate;">
                    <div class="absolute -right-6 -top-6 rounded-full bg-indigo-50 dark:bg-indigo-900/20 p-8 transition-transform duration-500 group-hover:scale-110">
                        <svg class="h-10 w-10 text-indigo-500 dark:text-indigo-400 opacity-60" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div class="relative z-10">
                        <div class="text-sm font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Master Produk</div>
                        <div class="mt-2 text-4xl font-extrabold text-slate-800 dark:text-slate-100">{{ $totalProducts }}</div>
                        <div class="mt-2 text-xs text-slate-400 dark:text-slate-500">Seluruh produk terdaftar di sistem</div>
                    </div>
                </div>

                <!-- Total Stok Card -->
                <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-slate-900/80 p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 transition duration-300 hover:shadow-md hover:border-emerald-300 dark:hover:border-emerald-500/50" style="isolation: isolate;">
                    <div class="absolute -right-6 -top-6 rounded-full bg-emerald-50 dark:bg-emerald-900/20 p-8 transition-transform duration-500 group-hover:scale-110">
                        <svg class="h-10 w-10 text-emerald-500 dark:text-emerald-400 opacity-60" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                        </svg>
                    </div>
                    <div class="relative z-10">
                        <div class="text-sm font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Kuantitas Stok</div>
                        <div class="mt-2 text-4xl font-extrabold text-slate-800 dark:text-slate-100">{{ $totalStock }}</div>
                        <div class="mt-2 text-xs text-slate-400 dark:text-slate-500">Total fisik semua barang</div>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden bg-white dark:bg-slate-900/80 p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-200/60 dark:border-slate-700/60 mb-8 transition hover:shadow-md">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">Perbandingan Arus Barang</h3>
                    <span class="rounded-full bg-slate-100 dark:bg-slate-800 px-3 py-1 text-xs font-medium text-slate-500 dark:text-slate-400">6 Bulan Terakhir</span>
                </div>
                <div class="relative h-72 sm:h-80 w-full">
                    <canvas id="stockChart"></canvas>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="overflow-hidden bg-white dark:bg-slate-900/80 shadow-sm border border-slate-200/60 dark:border-slate-700/60 rounded-3xl p-6 sm:p-8 transition hover:shadow-md">
                    <h3 class="text-md font-bold text-slate-800 dark:text-slate-100 mb-6 flex items-center gap-2">
                        <svg class="h-5 w-5 text-blue-500 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" />
                        </svg>
                        5 Barang Masuk Terakhir
                    </h3>
                    <ul class="divide-y divide-slate-100 dark:divide-slate-700/60">
                        @forelse($recentStockIns as $in)
                            <li class="py-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between group">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 break-words transition group-hover:text-indigo-600 dark:group-hover:text-indigo-400">{{ $in->product->name ?? 'Produk Tidak Ditemukan/Dihapus' }}</p>
                                    <p class="mt-1 flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        {{ \Carbon\Carbon::parse($in->date_time)->format('d M Y H:i') }} &bull; Rak: {{ $in->rak->name ?? '-' }}
                                    </p>
                                </div>
                                <div class="w-fit shrink-0 rounded-full bg-blue-50 dark:bg-indigo-900/30 px-3 py-1.5 text-sm font-bold text-blue-600 dark:text-indigo-400 ring-1 ring-inset ring-blue-500/10 dark:ring-indigo-500/20 shadow-sm">+{{ $in->quantity }}</div>
                            </li>
                        @empty
                            <li class="py-8 text-sm text-slate-500 dark:text-slate-400 text-center">Belum ada data transaksi masuk.</li>
                        @endforelse
                    </ul>
                </div>

                <div class="overflow-hidden bg-white dark:bg-slate-900/80 shadow-sm border border-slate-200/60 dark:border-slate-700/60 rounded-3xl p-6 sm:p-8 transition hover:shadow-md">
                    <h3 class="text-md font-bold text-slate-800 dark:text-slate-100 mb-6 flex items-center gap-2">
                        <svg class="h-5 w-5 text-red-500 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                        </svg>
                        5 Barang Keluar Terakhir
                    </h3>
                    <ul class="divide-y divide-slate-100 dark:divide-slate-700/60">
                        @forelse($recentStockOuts as $out)
                            <li class="py-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between group">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 break-words transition group-hover:text-rose-600 dark:group-hover:text-rose-400">{{ $out->product->name ?? 'Produk Tidak Ditemukan/Dihapus' }}</p>
                                    <p class="mt-1 flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        {{ \Carbon\Carbon::parse($out->date_time)->format('d M Y H:i') }} &bull; Rak: {{ $out->rak->name ?? '-' }}
                                    </p>
                                </div>
                                <div class="w-fit shrink-0 rounded-full bg-red-50 dark:bg-rose-900/30 px-3 py-1.5 text-sm font-bold text-red-600 dark:text-rose-400 ring-1 ring-inset ring-red-500/10 dark:ring-rose-500/20 shadow-sm">-{{ $out->quantity }}</div>
                            </li>
                        @empty
                            <li class="py-8 text-sm text-slate-500 dark:text-slate-400 text-center">Belum ada data transaksi keluar.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('stockChart').getContext('2d');
            const isMobile = window.matchMedia('(max-width: 640px)').matches;
            
            const labels = {!! json_encode($chartLabels) !!};
            const dataIn = {!! json_encode($chartDataIn) !!};
            const dataOut = {!! json_encode($chartDataOut) !!};

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Kuantitas Masuk',
                            data: dataIn,
                            backgroundColor: 'rgba(59, 130, 246, 0.9)', 
                            hoverBackgroundColor: 'rgba(59, 130, 246, 1)',
                            borderRadius: 4
                        },
                        {
                            label: 'Kuantitas Keluar',
                            data: dataOut,
                            backgroundColor: 'rgba(239, 68, 68, 0.9)',
                            hoverBackgroundColor: 'rgba(239, 68, 68, 1)',
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleFont: { size: 14 },
                            bodyFont: { size: 13 },
                            padding: 10
                        },
                        legend: {
                            position: isMobile ? 'bottom' : 'top',
                            labels: {
                                usePointStyle: true,
                                padding: isMobile ? 12 : 20
                            }
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
