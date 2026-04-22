<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-slate-200 leading-tight">
                {{ __('Riwayat Barang Masuk') }}
            </h2>
            <a href="{{ route('stock-in.create') }}" class="inline-flex w-full items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm dark:shadow-none transition hover:bg-blue-700 sm:w-auto">
                + Input Barang Masuk
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-xl bg-white dark:bg-slate-900 shadow-sm dark:shadow-none sm:rounded-lg">
                <div class="p-4 text-gray-900 dark:text-slate-100 sm:p-6">
                    <div class="space-y-3 md:hidden">
                        @forelse($stockIns as $item)
                            <article class="rounded-xl border border-gray-200 dark:border-slate-700 p-4 shadow-sm dark:shadow-none">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <h3 class="break-words text-sm font-semibold text-gray-900 dark:text-slate-100">{{ $item->product->name }}</h3>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-slate-400">{{ \Carbon\Carbon::parse($item->date_time)->format('d M Y, H:i') }}</p>
                                    </div>
                                    <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-800">+{{ $item->quantity }}</span>
                                </div>

                                <dl class="mt-4 space-y-2 text-sm">
                                    <div class="flex items-start justify-between gap-4">
                                        <dt class="text-gray-500 dark:text-slate-400">Rak Tujuan</dt>
                                        <dd class="max-w-[60%] break-words text-right font-medium text-gray-900 dark:text-slate-100">{{ $item->rak->nomor_rak }} - {{ $item->rak->tingkat }} ({{ $item->rak->bagian }})</dd>
                                    </div>
                                    <div class="flex items-start justify-between gap-4">
                                        <dt class="text-gray-500 dark:text-slate-400">Petugas</dt>
                                        <dd class="max-w-[60%] break-words text-right font-medium text-gray-900 dark:text-slate-100">{{ $item->user->name ?? 'Sistem/Dihapus' }}</dd>
                                    </div>
                                </dl>

                                <div class="mt-4 border-t border-gray-100 dark:border-slate-700/60 pt-3 text-sm font-medium">
                                    @if(auth()->user()->role === 'admin')
                                        <a href="{{ route('stock-in.edit', $item->id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                    @else
                                        <span class="italic text-gray-400">No Access</span>
                                    @endif
                                </div>
                            </article>
                        @empty
                            <div class="rounded-xl border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500 dark:text-slate-400">
                                Belum ada riwayat barang masuk.
                            </div>
                        @endforelse
                    </div>

                    <div class="hidden overflow-x-auto md:block">
                        <table class="min-w-full bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700">
                            <thead class="bg-gray-50 dark:bg-slate-800 border-b">
                                <tr>
                                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase">Tanggal</th>
                                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase">Produk</th>
                                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase">Rak Tujuan</th>
                                    <th class="py-3 px-6 text-center text-xs font-medium text-gray-500 dark:text-slate-400 uppercase">Jumlah</th>
                                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase">Petugas</th>
                                    <th class="py-3 px-6 text-center text-xs font-medium text-gray-500 dark:text-slate-400 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                                @forelse($stockIns as $item)
                                    <tr class="hover:bg-gray-50 dark:bg-slate-800 transition duration-150">
                                        <td class="py-4 px-6 whitespace-nowrap text-sm text-gray-900 dark:text-slate-100">
                                            {{ \Carbon\Carbon::parse($item->date_time)->format('d M Y, H:i') }}
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap text-sm text-gray-900 dark:text-slate-100">
                                            {{ $item->product->name }}
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap text-sm text-gray-900 dark:text-slate-100">
                                            {{ $item->rak->nomor_rak }} - {{ $item->rak->tingkat }} ({{ $item->rak->bagian }})
                                        </td>
                                        <td class="py-4 px-6 text-center whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                +{{ $item->quantity }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap text-sm font-medium text-gray-700 dark:text-slate-300">
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
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-6 text-center text-sm text-gray-500 dark:text-slate-400">Belum ada riwayat barang masuk.</td>
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
