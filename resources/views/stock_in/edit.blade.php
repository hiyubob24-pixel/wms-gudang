<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Riwayat Barang Masuk') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form action="{{ route('stock-in.update', $stockIn->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="product_id" class="block text-gray-700 font-medium mb-2">Produk</label>
                            <select name="product_id" id="product_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ $stockIn->product_id == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="rak_id" class="block text-gray-700 font-medium mb-2">Lokasi Rak Target</label>
                            <select name="rak_id" id="rak_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach($raks as $rak)
                                    <option value="{{ $rak->id }}" {{ $stockIn->rak_id == $rak->id ? 'selected' : '' }}>
                                        {{ $rak->display_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="quantity" class="block text-gray-700 font-medium mb-2">Jumlah Barang (Qty)</label>
                            <input type="number" name="quantity" id="quantity" value="{{ $stockIn->quantity }}" min="1" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div class="mb-6">
                            <label for="date_time" class="block text-gray-700 font-medium mb-2">Tanggal & Jam</label>
                            <input type="datetime-local" name="date_time" id="date_time" value="{{ date('Y-m-d\TH:i', strtotime($stockIn->date_time)) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-gray-700 hover:bg-gray-50 mr-3 transition ease-in-out duration-150">
                                Batal
                            </a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                                Simpan & Kalibrasi Stok
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>