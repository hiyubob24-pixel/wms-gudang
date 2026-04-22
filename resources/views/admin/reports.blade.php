<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-slate-200 leading-tight">
            {{ __('Laporan & Grafik') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            
            <div class="rounded-xl bg-white dark:bg-slate-900 p-4 shadow-sm dark:shadow-none sm:rounded-lg sm:p-6">
                <h3 class="mb-4 border-b pb-2 text-lg font-bold text-gray-800 dark:text-slate-200">Tren Arus Barang (6 Bulan Terakhir)</h3>
                <div class="relative h-72 w-full sm:h-80">
                    <canvas id="reportChart"></canvas>
                </div>
            </div>

            <div class="rounded-xl bg-white dark:bg-slate-900 p-4 shadow-sm dark:shadow-none sm:rounded-lg sm:p-6">
                <h3 class="mb-4 border-b pb-2 text-lg font-bold text-gray-800 dark:text-slate-200">Rincian Perbandingan per Bulan</h3>

                <div class="space-y-3 md:hidden">
                    @forelse($tableReports as $report)
                        <article class="rounded-xl border border-gray-200 dark:border-slate-700 p-4 shadow-sm dark:shadow-none">
                            <div class="flex items-start justify-between gap-3">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-slate-100">{{ $report['month'] }}</h4>
                                <span class="text-sm font-bold {{ $report['net'] >= 0 ? 'text-green-600' : 'text-orange-500' }}">
                                    {{ $report['net'] > 0 ? '+' : '' }}{{ $report['net'] }}
                                </span>
                            </div>

                            <dl class="mt-4 space-y-2 text-sm">
                                <div class="flex items-start justify-between gap-4">
                                    <dt class="text-gray-500 dark:text-slate-400">Barang Masuk</dt>
                                    <dd class="font-bold text-blue-600 dark:text-indigo-400">+{{ $report['in'] }}</dd>
                                </div>
                                <div class="flex items-start justify-between gap-4">
                                    <dt class="text-gray-500 dark:text-slate-400">Barang Keluar</dt>
                                    <dd class="font-bold text-red-600 dark:text-rose-400">-{{ $report['out'] }}</dd>
                                </div>
                                <div class="flex items-start justify-between gap-4">
                                    <dt class="text-gray-500 dark:text-slate-400">Selisih Bersih</dt>
                                    <dd class="font-bold {{ $report['net'] >= 0 ? 'text-green-600' : 'text-orange-500' }}">
                                        {{ $report['net'] > 0 ? '+' : '' }}{{ $report['net'] }}
                                    </dd>
                                </div>
                            </dl>
                        </article>
                    @empty
                        <div class="rounded-xl border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500 dark:text-slate-400">
                            Belum ada data laporan.
                        </div>
                    @endforelse
                </div>

                <div class="hidden overflow-x-auto md:block">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700 border">
                        <thead class="bg-gray-50 dark:bg-slate-800">
                            <tr>
                                <th class="border-b px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-slate-400">Bulan</th>
                                <th class="border-b px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-slate-400">Barang Masuk</th>
                                <th class="border-b px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-slate-400">Barang Keluar</th>
                                <th class="border-b px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-slate-400">Selisih Bersih (Net)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-slate-700 bg-white dark:bg-slate-900">
                            @forelse($tableReports as $report)
                                <tr class="hover:bg-gray-50 dark:bg-slate-800">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900 dark:text-slate-100">{{ $report['month'] }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-bold text-blue-600 dark:text-indigo-400">+{{ $report['in'] }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-bold text-red-600 dark:text-rose-400">-{{ $report['out'] }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-bold {{ $report['net'] >= 0 ? 'text-green-600' : 'text-orange-500' }}">
                                        {{ $report['net'] > 0 ? '+' : '' }}{{ $report['net'] }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-6 text-center text-sm text-gray-500 dark:text-slate-400">Belum ada data laporan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('reportChart').getContext('2d');
            const isMobile = window.matchMedia('(max-width: 640px)').matches;
            
            const labels = {!! json_encode($chartLabels) !!};
            const dataIn = {!! json_encode($chartDataIn) !!};
            const dataOut = {!! json_encode($chartDataOut) !!};

            new Chart(ctx, {
                type: 'line', // Menggunakan grafik garis agar tren lebih jelas terlihat
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Kuantitas Masuk',
                            data: dataIn,
                            borderColor: 'rgba(59, 130, 246, 1)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 2,
                            pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                            fill: true,
                            tension: 0.3
                        },
                        {
                            label: 'Kuantitas Keluar',
                            data: dataOut,
                            borderColor: 'rgba(239, 68, 68, 1)',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            borderWidth: 2,
                            pointBackgroundColor: 'rgba(239, 68, 68, 1)',
                            fill: true,
                            tension: 0.3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            ticks: {
                                autoSkip: true,
                                maxRotation: 0,
                            },
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
                                padding: isMobile ? 12 : 20,
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleFont: { size: isMobile ? 12 : 14 },
                            bodyFont: { size: isMobile ? 11 : 13 },
                            padding: 10
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
