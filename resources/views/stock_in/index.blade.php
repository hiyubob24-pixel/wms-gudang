<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Riwayat Barang Masuk') }}
            </h2>
            <a href="{{ route('stock-in.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                + Input Barang Masuk
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase">Rak Tujuan</th>
                                <th class="py-3 px-6 text-center text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase">Petugas</th>
                                <th class="py-3 px-6 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($stockIns as $item)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-4 px-6 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($item->date_time)->format('d M Y, H:i') }}
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->product->name }}
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->rak->nomor_rak }} - {{ $item->rak->tingkat }} ({{ $item->rak->bagian }})
                                </td>
                                <td class="py-4 px-6 text-center whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        +{{ $item->quantity }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap text-sm font-medium text-gray-700">
                                    {{ $item->user->name ?? 'Sistem/Dihapus' }}
                                </td>
                                <td class="py-4 px-6 text-center whitespace-nowrap text-sm font-medium">
                                    @if(auth()->user()->role === 'admin')
                                        <a href="{{ route('stock-in.edit', $item->id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                    @else
                                        <span class="text-gray-400 italic">No Access</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>