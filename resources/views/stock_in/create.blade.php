<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-slate-200 leading-tight">
            {{ __('Form Barang Masuk') }}
        </h2>
    </x-slot>

    <div class="py-8 sm:py-12">
        <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="mb-6 flex items-start gap-3 rounded-2xl border border-rose-200 dark:border-rose-700/50 bg-rose-50 dark:bg-rose-900/20 p-4 text-rose-700 dark:text-rose-300">
                    <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <ul class="list-disc list-inside space-y-1 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="app-form-card p-6 sm:p-8">
                <form method="POST" action="{{ route('stock-in.store') }}">
                    @csrf

                    <div class="space-y-5">
                        {{-- Produk --}}
                        <div>
                            <label class="app-field-label" for="product_id">Produk</label>
                            <div class="app-field-select-wrap">
                                <select name="product_id" id="product_id" class="app-field-select" required>
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
                            <label class="app-field-label" for="rak_id">Rak Tujuan <span class="font-normal text-slate-400">(Hanya rak tersedia)</span></label>
                            <div class="app-field-select-wrap">
                                <select name="rak_id" id="rak_id" class="app-field-select" required>
                                    <option value="">— Pilih Rak —</option>
                                    @forelse($raks as $rak)
                                        <option value="{{ $rak->id }}" {{ old('rak_id') == $rak->id ? 'selected' : '' }}>
                                            {{ $rak->display_name }}
                                        </option>
                                    @empty
                                        <option value="" disabled>Semua rak saat ini penuh!</option>
                                    @endforelse
                                </select>
                            </div>
                        </div>

                        {{-- Qty --}}
                        <div>
                            <label class="app-field-label" for="quantity">Jumlah (Qty)</label>
                            <input type="number" name="quantity" id="quantity"
                                value="{{ old('quantity') }}"
                                class="app-field-input"
                                required min="1"
                                placeholder="Masukkan jumlah barang">
                        </div>

                        {{-- Tanggal --}}
                        <div>
                            <label class="app-field-label" for="date_time">Tanggal & Jam Masuk</label>
                            <input type="datetime-local" name="date_time" id="date_time"
                                value="{{ old('date_time') }}"
                                class="app-field-input"
                                required>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
                        <a href="{{ route('stock-in.index') }}"
                            class="inline-flex items-center justify-center rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-5 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-300 transition hover:bg-slate-50 dark:hover:bg-slate-800">
                            Lihat Riwayat
                        </a>
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-2xl bg-indigo-600 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-indigo-500/25 transition hover:bg-indigo-700 active:scale-95">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Simpan Barang Masuk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
