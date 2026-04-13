<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Stok Saat Ini') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                                <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rak (Zona)</th>
                                <th class="py-3 px-6 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Stok</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($stocks as $stock)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="font-medium text-gray-900">{{ $stock->product->name }}</div>
                                    @if($stock->product->sku)
                                        <div class="text-sm text-gray-500">SKU: {{ $stock->product->sku }}</div>
                                    @endif
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="font-medium text-gray-900">
                                        {{ $stock->rak->nomor_rak }} - {{ $stock->rak->tingkat }} - {{ $stock->rak->bagian }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        ({{ $stock->rak->name }})
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-center whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ $stock->quantity }}
                                    </span>
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