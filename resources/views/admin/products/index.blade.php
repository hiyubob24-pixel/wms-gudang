<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-slate-200 leading-tight">Master Barang</h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-xl bg-white dark:bg-slate-900 shadow-sm dark:shadow-none sm:rounded-lg">
                <div class="p-4 text-gray-900 dark:text-slate-100 sm:p-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <a href="{{ route('products.create') }}" class="inline-flex w-full items-center justify-center rounded-lg bg-green-500 px-4 py-2 text-sm font-semibold text-white shadow-sm dark:shadow-none transition hover:bg-green-600 sm:w-auto">+ Tambah Barang</a>
                            <p class="mt-3 text-sm text-gray-500 dark:text-slate-400">
                                Produk dengan riwayat transaksi tetap bisa dihapus dari master data selama tidak memiliki stok aktif.
                            </p>
                        </div>
                    </div>

                    <div class="space-y-3 md:hidden">
                        @forelse($products as $product)
                            <article class="rounded-xl border border-gray-200 dark:border-slate-700 p-4 shadow-sm dark:shadow-none">
                                <div class="min-w-0">
                                    <h3 class="break-words text-sm font-semibold text-gray-900 dark:text-slate-100">{{ $product->name }}</h3>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-slate-400">SKU: {{ $product->sku ?: '-' }}</p>
                                </div>

                                <dl class="mt-4 space-y-2 text-sm">
                                    <div class="flex items-start justify-between gap-4">
                                        <dt class="text-gray-500 dark:text-slate-400">Stok aktif</dt>
                                        <dd class="text-right font-medium text-gray-900 dark:text-slate-100">{{ $product->active_stocks_count }}</dd>
                                    </div>
                                    <div class="flex items-start justify-between gap-4">
                                        <dt class="text-gray-500 dark:text-slate-400">Riwayat transaksi</dt>
                                        <dd class="text-right font-medium text-gray-900 dark:text-slate-100">{{ $product->stock_ins_count + $product->stock_outs_count }}</dd>
                                    </div>
                                </dl>

                                <div class="mt-4 flex flex-wrap gap-3 border-t border-gray-100 dark:border-slate-700/60 pt-3 text-sm font-medium">
                                    <a href="{{ route('products.edit', $product) }}" class="text-blue-600 dark:text-indigo-400 hover:text-blue-700">Edit</a>
                                    @if($product->active_stocks_count)
                                        <span class="text-gray-400" title="Produk masih memiliki stok aktif">Hapus</span>
                                    @else
                                        <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Hapus produk dari master data? Riwayat transaksi akan tetap tersimpan.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-500">Hapus</button>
                                        </form>
                                    @endif
                                </div>
                            </article>
                        @empty
                            <div class="rounded-xl border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500 dark:text-slate-400">
                                Belum ada data barang.
                            </div>
                        @endforelse
                    </div>

                    <div class="hidden overflow-x-auto md:block">
                        <table class="min-w-full bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700">
                            <thead class="bg-gray-50 dark:bg-slate-800 border-b">
                                <tr>
                                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase">Nama Barang</th>
                                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase">SKU</th>
                                    <th class="py-3 px-6 text-center text-xs font-medium text-gray-500 dark:text-slate-400 uppercase">Stok Aktif</th>
                                    <th class="py-3 px-6 text-center text-xs font-medium text-gray-500 dark:text-slate-400 uppercase">Riwayat</th>
                                    <th class="py-3 px-6 text-center text-xs font-medium text-gray-500 dark:text-slate-400 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                                @forelse($products as $product)
                                    <tr class="hover:bg-gray-50 dark:bg-slate-800 transition duration-150">
                                        <td class="py-4 px-6 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-slate-100">{{ $product->name }}</td>
                                        <td class="py-4 px-6 whitespace-nowrap text-sm text-gray-600 dark:text-slate-400">{{ $product->sku ?: '-' }}</td>
                                        <td class="py-4 px-6 text-center whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 dark:bg-indigo-900/30 text-blue-800">
                                                {{ $product->active_stocks_count }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 text-center whitespace-nowrap text-sm text-gray-700 dark:text-slate-300">
                                            {{ $product->stock_ins_count + $product->stock_outs_count }}
                                        </td>
                                        <td class="py-4 px-6 text-center whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('products.edit', $product) }}" class="text-blue-600 dark:text-indigo-400 hover:text-blue-700">Edit</a>
                                            @if($product->active_stocks_count)
                                                <span class="ml-3 text-gray-400" title="Produk masih memiliki stok aktif">Hapus</span>
                                            @else
                                                <form method="POST" action="{{ route('products.destroy', $product) }}" class="inline" onsubmit="return confirm('Hapus produk dari master data? Riwayat transaksi akan tetap tersimpan.')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="ml-3 text-red-500 hover:text-red-600 dark:text-rose-400">Hapus</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-6 text-center text-sm text-gray-500 dark:text-slate-400">Belum ada data barang.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
