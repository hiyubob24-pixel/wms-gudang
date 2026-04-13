<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Riwayat Barang Keluar') }}
            </h2>
            <a href="{{ route('stock-out.create') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded shadow transition duration-150">
                + Catat Barang Keluar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 overflow-x-auto">
                    
                    @if(session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <table class="min-w-full bg-white border border-gray-200">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase">Dari Rak</th>
                                <th class="py-3 px-6 text-center text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase">Petugas</th>
                                <th class="py-3 px-6 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($stockOuts as $item)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-4 px-6 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($item->date_time)->format('d M Y, H:i') }}
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap text-sm text-gray-900 font-medium">
                                    {{ $item->product->name }}
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->rak->nomor_rak }} - {{ $item->rak->tingkat }} ({{ $item->rak->bagian }})
                                </td>
                                <td class="py-4 px-6 text-center whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-bold rounded-full bg-red-100 text-red-800">
                                        -{{ $item->quantity }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap text-sm font-medium text-gray-700">
                                    {{ $item->user->name ?? 'Sistem/Dihapus' }}
                                </td>
                                <td class="py-4 px-6 text-center whitespace-nowrap text-sm font-medium">
                                    @if(auth()->user()->role === 'admin')
                                        <a href="{{ route('stock-out.edit', $item->id) }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1 rounded">Edit</a>
                                    @else
                                        <span class="text-gray-400 italic">No Access</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if($stockOuts->isEmpty())
                        <div class="text-center py-10 text-gray-500">
                            Belum ada riwayat barang keluar.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>