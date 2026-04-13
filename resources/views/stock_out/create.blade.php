<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Form Barang Keluar</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-b border-gray-200">
                
                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-2 mb-4">{{ session('success') }}</div>
                @endif

                @if(session('error_stok'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-2 mb-4">
                        <strong>Gagal:</strong> {{ session('error_stok') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('stock-out.store') }}">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block font-bold mb-1">Pilih Produk</label>
                        <select id="product_id" name="product_id" class="w-full border rounded p-2" required onchange="fetchAvailableRaks(this.value)">
                            <option value="">-- Pilih Produk --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block font-bold mb-1 text-red-600">Pilih Lokasi Rak (Hanya yang memiliki Stok)</label>
                        <select id="rak_id" name="rak_id" class="w-full border rounded p-2 bg-gray-50" required disabled>
                            <option value="">Silakan pilih produk dulu...</option>
                        </select>
                        <p id="msg-status" class="text-xs mt-1 text-gray-500 italic"></p>
                    </div>

                    <div class="mb-4">
                        <label class="block font-bold mb-1">Jumlah Keluar</label>
                        <input type="number" name="quantity" value="{{ old('quantity') }}" class="w-full border rounded p-2" required min="1">
                    </div>

                    <div class="mb-4">
                        <label class="block font-bold mb-1">Tanggal & Jam</label>
                        <input type="datetime-local" name="date_time" value="{{ old('date_time') }}" class="w-full border rounded p-2" required>
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold px-6 py-2 rounded transition shadow-md">
                            Simpan Transaksi
                        </button>
                        <a href="{{ route('stock-out.index') }}" class="text-gray-600 hover:underline text-sm">Lihat Riwayat</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function fetchAvailableRaks(productId) {
            const rakSelect = document.getElementById('rak_id');
            const statusMsg = document.getElementById('msg-status');

            // Reset keadaan awal
            rakSelect.innerHTML = '<option value="">-- Mencari lokasi stok... --</option>';
            rakSelect.disabled = true;
            rakSelect.classList.add('bg-gray-50');
            statusMsg.innerText = "";

            if (!productId) {
                rakSelect.innerHTML = '<option value="">Silakan pilih produk dulu...</option>';
                return;
            }

            // Memanggil API Fetch ke Laravel Backend
            fetch(`/api/get-raks-by-product/${productId}`)
                .then(response => response.json())
                .then(data => {
                    rakSelect.innerHTML = '';
                    
                    if (data.length > 0) {
                        rakSelect.innerHTML = '<option value="">-- Pilih Rak (Tersedia) --</option>';
                        data.forEach(item => {
                            let opt = document.createElement('option');
                            opt.value = item.id;
                            opt.text = item.display_name;
                            rakSelect.appendChild(opt);
                        });
                        rakSelect.disabled = false;
                        rakSelect.classList.remove('bg-gray-50');
                        statusMsg.innerText = "Ditemukan " + data.length + " rak yang berisi produk ini.";
                        statusMsg.classList.replace('text-gray-500', 'text-green-600');
                    } else {
                        rakSelect.innerHTML = '<option value="">STOK KOSONG DI SEMUA RAK</option>';
                        statusMsg.innerText = "Produk ini belum pernah masuk atau stoknya sudah 0.";
                        statusMsg.classList.replace('text-gray-500', 'text-red-600');
                    }
                })
                .catch(error => {
                    console.error('Fetch Error:', error);
                    rakSelect.innerHTML = '<option value="">Gagal mengambil data</option>';
                });
        }

        // Jalankan otomatis jika ada data 'old' setelah validasi gagal
        window.addEventListener('DOMContentLoaded', (event) => {
            const preSelectedProduct = document.getElementById('product_id').value;
            if (preSelectedProduct) {
                fetchAvailableRaks(preSelectedProduct);
            }
        });
    </script>
</x-app-layout>