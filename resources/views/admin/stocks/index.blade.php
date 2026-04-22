<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-slate-200 leading-tight">
            {{ __('Daftar Stok Saat Ini') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-xl bg-white dark:bg-slate-900 shadow-sm dark:shadow-none sm:rounded-lg">
                <div class="p-4 text-gray-900 dark:text-slate-100 sm:p-6">
                    <div class="space-y-3 md:hidden">
                        @forelse($stocks as $stock)
                            <article class="rounded-xl border border-gray-200 dark:border-slate-700 p-4 shadow-sm dark:shadow-none">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <h3 class="break-words text-sm font-semibold text-gray-900 dark:text-slate-100">{{ $stock->product->name }}</h3>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-slate-400">{{ $stock->product->sku ? 'SKU: '.$stock->product->sku : 'Tanpa SKU' }}</p>
                                    </div>
                                    <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-800">{{ $stock->quantity }}</span>
                                </div>
                                <dl class="mt-4 space-y-2 text-sm">
                                    <div class="flex items-start justify-between gap-4">
                                        <dt class="text-gray-500 dark:text-slate-400">Rak</dt>
                                        <dd class="max-w-[60%] break-words text-right font-medium text-gray-900 dark:text-slate-100">{{ $stock->rak->nomor_rak }} - {{ $stock->rak->tingkat }}</dd>
                                    </div>
                                    <div class="flex items-start justify-between gap-4">
                                        <dt class="text-gray-500 dark:text-slate-400">Zona</dt>
                                        <dd class="max-w-[60%] break-words text-right font-medium text-gray-900 dark:text-slate-100">{{ $stock->rak->bagian }}</dd>
                                    </div>
                                    <div class="flex items-start justify-between gap-4">
                                        <dt class="text-gray-500 dark:text-slate-400">Nama Rak</dt>
                                        <dd class="max-w-[60%] break-words text-right font-medium text-gray-900 dark:text-slate-100">{{ $stock->rak->name }}</dd>
                                    </div>
                                </dl>
                            </article>
                        @empty
                            <div class="rounded-xl border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500 dark:text-slate-400">
                                Belum ada data stok.
                            </div>
                        @endforelse
                    </div>

                    <div class="hidden overflow-x-auto md:block">
                        <table class="min-w-full bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700">
                            <thead class="bg-gray-50 dark:bg-slate-800 border-b">
                                <tr>
                                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">Produk</th>
                                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">Rak (Zona)</th>
                                    <th class="py-3 px-6 text-center text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">Jumlah Stok</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                                @forelse($stocks as $stock)
                                    <tr class="hover:bg-gray-50 dark:bg-slate-800 transition duration-150">
                                        <td class="py-4 px-6 whitespace-nowrap">
                                            <div class="font-medium text-gray-900 dark:text-slate-100">{{ $stock->product->name }}</div>
                                            @if($stock->product->sku)
                                                <div class="text-sm text-gray-500 dark:text-slate-400">SKU: {{ $stock->product->sku }}</div>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap">
                                            <div class="font-medium text-gray-900 dark:text-slate-100">
                                                {{ $stock->rak->nomor_rak }} - {{ $stock->rak->tingkat }} - {{ $stock->rak->bagian }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-slate-400">
                                                ({{ $stock->rak->name }})
                                            </div>
                                        </td>
                                        <td class="py-4 px-6 text-center whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                {{ $stock->quantity }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-6 text-center text-sm text-gray-500 dark:text-slate-400">Belum ada data stok.</td>
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
