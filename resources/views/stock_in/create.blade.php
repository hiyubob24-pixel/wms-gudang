<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Form Barang Masuk') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                        <p class="font-bold">Kesalahan!</p>
                        <p>{{ session('error') }}</p>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('stock-in.store') }}">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Produk</label>
                        <select name="product_id" class="w-full border rounded p-2 focus:ring focus:ring-blue-200" required>
                            <option value="">-- Pilih Produk --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Rak Tujuan (Hanya Rak Tersedia)</label>
                        <select name="rak_id" class="w-full border rounded p-2 focus:ring focus:ring-blue-200" required>
                            <option value="">-- Pilih Rak --</option>
                            @forelse($raks as $rak)
                                <option value="{{ $rak->id }}" {{ old('rak_id') == $rak->id ? 'selected' : '' }}>
                                    {{ $rak->display_name }} 
                                </option>
                            @empty
                                <option value="" disabled>Semua rak saat ini penuh!</option>
                            @endforelse
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Jumlah (Qty)</label>
                        <input type="number" name="quantity" value="{{ old('quantity') }}" class="w-full border rounded p-2 focus:ring focus:ring-blue-200" required min="1" placeholder="Masukkan jumlah barang">
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-gray-700 font-bold mb-2">Tanggal & Jam Masuk</label>
                        <input type="datetime-local" name="date_time" value="{{ old('date_time') }}" class="w-full border rounded p-2 focus:ring focus:ring-blue-200" required>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow transition duration-150">
                            Simpan Barang Masuk
                        </button>
                        <a href="{{ route('stock-in.index') }}" class="text-gray-600 hover:underline text-sm">
                            Lihat Riwayat
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>