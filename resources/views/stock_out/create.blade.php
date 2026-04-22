<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-slate-200 leading-tight">Form Barang Keluar</h2>
    </x-slot>

    <div class="py-8 sm:py-12">
        <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
            <div class="app-form-card p-6 sm:p-8">
                <form method="POST" action="{{ route('stock-out.store') }}">
                    @csrf

                    <div class="space-y-5">
                        {{-- Produk --}}
                        <div>
                            <label class="app-field-label" for="product_id">Pilih Produk</label>
                            <div class="app-field-select-wrap">
                                <select id="product_id" name="product_id" class="app-field-select" required onchange="fetchAvailableRaks(this.value)">
                                    <option value="">— Pilih Produk —</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Rak --}}
                        <div>
                            <label class="app-field-label" for="rak_id">
                                Lokasi Rak
                                <span class="ml-1 font-normal text-rose-400">(Hanya yang memiliki stok)</span>
                            </label>
                            <div class="app-field-select-wrap">
                                <select id="rak_id" name="rak_id" class="app-field-select" required disabled>
                                    <option value="">Silakan pilih produk dulu...</option>
                                </select>
                            </div>
                            <p id="msg-status" class="app-field-hint italic"></p>
                        </div>

                        {{-- Qty --}}
                        <div>
                            <label class="app-field-label" for="quantity">Jumlah Keluar</label>
                            <input type="number" name="quantity" id="quantity"
                                value="{{ old('quantity') }}"
                                class="app-field-input"
                                required min="1"
                                placeholder="Masukkan jumlah">
                        </div>

                        {{-- Tanggal --}}
                        <div>
                            <label class="app-field-label" for="date_time">Tanggal & Jam</label>
                            <input type="datetime-local" name="date_time" id="date_time"
                                value="{{ old('date_time') }}"
                                class="app-field-input"
                                required>
                        </div>
                    </div>

                    <div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
                        <a href="{{ route('stock-out.index') }}"
                            class="inline-flex items-center justify-center rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-5 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-300 transition hover:bg-slate-50 dark:hover:bg-slate-800">
                            Lihat Riwayat
                        </a>
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-2xl bg-rose-600 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-rose-500/25 transition hover:bg-rose-700 active:scale-95">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Simpan Transaksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const previousRakId = @json(old('rak_id'));

        function setStatusColor(el, type) {
            el.className = 'app-field-hint italic';
            if (type === 'success') el.classList.add('text-emerald-600', 'dark:text-emerald-400');
            else if (type === 'error') el.classList.add('text-rose-600', 'dark:text-rose-400');
            else el.classList.add('text-slate-500', 'dark:text-slate-400');
        }

        function fetchAvailableRaks(productId) {
            const rakSelect = document.getElementById('rak_id');
            const statusMsg = document.getElementById('msg-status');

            rakSelect.innerHTML = '<option value="">— Mencari lokasi stok... —</option>';
            rakSelect.disabled = true;
            statusMsg.innerText = '';
            setStatusColor(statusMsg, 'neutral');

            if (!productId) {
                rakSelect.innerHTML = '<option value="">Silakan pilih produk dulu...</option>';
                return;
            }

            fetch(`/api/get-raks-by-product/${productId}`)
                .then(response => response.json())
                .then(data => {
                    rakSelect.innerHTML = '';
                    if (data.length > 0) {
                        rakSelect.innerHTML = '<option value="">— Pilih Rak Tersedia —</option>';
                        data.forEach(item => {
                            let opt = document.createElement('option');
                            opt.value = item.id;
                            opt.text = item.display_name;
                            if (previousRakId && String(previousRakId) === String(item.id)) opt.selected = true;
                            rakSelect.appendChild(opt);
                        });
                        rakSelect.disabled = false;
                        statusMsg.innerText = `✓ Ditemukan ${data.length} rak yang berisi produk ini.`;
                        setStatusColor(statusMsg, 'success');
                    } else {
                        rakSelect.innerHTML = '<option value="">Stok kosong di semua rak</option>';
                        statusMsg.innerText = '✕ Produk ini belum pernah masuk atau stoknya sudah 0.';
                        setStatusColor(statusMsg, 'error');
                    }
                })
                .catch(() => {
                    rakSelect.innerHTML = '<option value="">Gagal mengambil data</option>';
                    statusMsg.innerText = '✕ Gagal mengambil data rak. Coba lagi.';
                    setStatusColor(statusMsg, 'error');
                });
        }

        window.addEventListener('DOMContentLoaded', () => {
            const preSelected = document.getElementById('product_id').value;
            if (preSelected) fetchAvailableRaks(preSelected);
        });
    </script>
</x-app-layout>
