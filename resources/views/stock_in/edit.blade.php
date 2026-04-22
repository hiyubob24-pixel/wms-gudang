<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-slate-200 leading-tight">
            {{ __('Edit Riwayat Barang Masuk') }}
        </h2>
    </x-slot>

    <div class="py-8 sm:py-12">
        <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
            <div class="app-form-card p-6 sm:p-8">
                <form action="{{ route('stock-in.update', $stockIn->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-5">
                        <div>
                            <label class="app-field-label" for="product_id">Produk</label>
                            <div class="app-field-select-wrap">
                                <select name="product_id" id="product_id" class="app-field-select">
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ $stockIn->product_id == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}{{ method_exists($product, 'trashed') && $product->trashed() ? ' (Arsip)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="app-field-label" for="rak_id">Lokasi Rak Target</label>
                            <div class="app-field-select-wrap">
                                <select name="rak_id" id="rak_id" class="app-field-select">
                                    @foreach($raks as $rak)
                                        <option value="{{ $rak->id }}" {{ $stockIn->rak_id == $rak->id ? 'selected' : '' }}>
                                            {{ $rak->display_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="app-field-label" for="quantity">Jumlah Barang (Qty)</label>
                            <input type="number" name="quantity" id="quantity"
                                value="{{ $stockIn->quantity }}"
                                min="1"
                                class="app-field-input">
                        </div>

                        <div>
                            <label class="app-field-label" for="date_time">Tanggal & Jam</label>
                            <input type="datetime-local" name="date_time" id="date_time"
                                value="{{ date('Y-m-d\TH:i', strtotime($stockIn->date_time)) }}"
                                class="app-field-input">
                        </div>
                    </div>

                    <div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
                        <a href="{{ url()->previous() }}"
                            class="inline-flex items-center justify-center rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-5 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-300 transition hover:bg-slate-50 dark:hover:bg-slate-800">
                            Batal
                        </a>
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-2xl bg-indigo-600 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-indigo-500/25 transition hover:bg-indigo-700 active:scale-95">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Simpan & Kalibrasi Stok
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
