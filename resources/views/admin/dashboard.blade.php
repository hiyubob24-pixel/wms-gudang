<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Admin WMS') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <div class="text-gray-500 text-sm font-semibold uppercase">Total Master Produk</div>
                    <div class="text-3xl font-bold text-gray-800 mt-2">{{ $totalProducts }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <div class="text-gray-500 text-sm font-semibold uppercase">Total Kuantitas Stok Fisik</div>
                    <div class="text-3xl font-bold text-gray-800 mt-2">{{ $totalStock }}</div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Perbandingan Arus Barang (6 Bulan Terakhir)</h3>
                <div class="relative h-80 w-full">
                    <canvas id="stockChart"></canvas>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-md font-bold text-gray-800 mb-4 border-b pb-2">5 Barang Masuk Terakhir</h3>
                    <ul class="divide-y divide-gray-200">
                        @forelse($recentStockIns as $in)
                            <li class="py-3 flex justify-between items-center">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $in->product->name ?? 'Produk Tidak Ditemukan/Dihapus' }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($in->date_time)->format('d M Y H:i') }} | Rak: {{ $in->rak->name ?? '-' }}
                                    </p>
                                </div>
                                <div class="text-sm font-bold text-blue-600 bg-blue-100 px-3 py-1 rounded-full">+{{ $in->quantity }}</div>
                            </li>
                        @empty
                            <li class="py-3 text-sm text-gray-500 text-center">Belum ada data transaksi masuk.</li>
                        @endforelse
                    </ul>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-md font-bold text-gray-800 mb-4 border-b pb-2">5 Barang Keluar Terakhir</h3>
                    <ul class="divide-y divide-gray-200">
                        @forelse($recentStockOuts as $out)
                            <li class="py-3 flex justify-between items-center">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $out->product->name ?? 'Produk Tidak Ditemukan/Dihapus' }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($out->date_time)->format('d M Y H:i') }} | Rak: {{ $out->rak->name ?? '-' }}
                                    </p>
                                </div>
                                <div class="text-sm font-bold text-red-600 bg-red-100 px-3 py-1 rounded-full">-{{ $out->quantity }}</div>
                            </li>
                        @empty
                            <li class="py-3 text-sm text-gray-500 text-center">Belum ada data transaksi keluar.</li>
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
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 20
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