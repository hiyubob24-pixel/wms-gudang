<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan & Grafik') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Tren Arus Barang (6 Bulan Terakhir)</h3>
                <div class="relative h-80 w-full">
                    <canvas id="reportChart"></canvas>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-sm">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Rincian Perbandingan per Bulan</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Bulan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Barang Masuk</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Barang Keluar</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Selisih Bersih (Net)</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($tableReports as $report)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $report['month'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 font-bold">+{{ $report['in'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-bold">-{{ $report['out'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold {{ $report['net'] >= 0 ? 'text-green-600' : 'text-orange-500' }}">
                                    {{ $report['net'] > 0 ? '+' : '' }}{{ $report['net'] }}
                                </td>
                            </tr>
                            @endforeach
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
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleFont: { size: 14 },
                            bodyFont: { size: 13 },
                            padding: 10
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>