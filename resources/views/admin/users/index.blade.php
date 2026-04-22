<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-slate-200 leading-tight">Manajemen User</h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-xl bg-white dark:bg-slate-900 shadow-sm dark:shadow-none sm:rounded-lg">
                <div class="p-4 text-gray-900 dark:text-slate-100 sm:p-6">
                    <a href="{{ route('users.create') }}" class="mb-4 inline-flex w-full items-center justify-center rounded-lg bg-green-500 px-4 py-2 text-sm font-semibold text-white shadow-sm dark:shadow-none transition hover:bg-green-600 sm:w-auto">+ Tambah User</a>

                    <div class="space-y-3 md:hidden">
                        @forelse($users as $user)
                            <article class="rounded-xl border border-gray-200 dark:border-slate-700 p-4 shadow-sm dark:shadow-none">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <h3 class="break-words text-sm font-semibold text-gray-900 dark:text-slate-100">{{ $user->name }}</h3>
                                        <p class="mt-1 break-all text-xs text-gray-500 dark:text-slate-400">{{ $user->email }}</p>
                                    </div>
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold text-white {{ $user->role == 'admin' ? 'bg-red-500' : 'bg-blue-500' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </div>

                                <dl class="mt-4 space-y-2 text-sm">
                                    <div class="flex items-start justify-between gap-4">
                                        <dt class="text-gray-500 dark:text-slate-400">Transaksi masuk</dt>
                                        <dd class="text-right font-medium text-gray-900 dark:text-slate-100">{{ $user->stock_ins_count }}</dd>
                                    </div>
                                    <div class="flex items-start justify-between gap-4">
                                        <dt class="text-gray-500 dark:text-slate-400">Transaksi keluar</dt>
                                        <dd class="text-right font-medium text-gray-900 dark:text-slate-100">{{ $user->stock_outs_count }}</dd>
                                    </div>
                                </dl>

                                <div class="mt-4 flex flex-wrap gap-3 border-t border-gray-100 dark:border-slate-700/60 pt-3 text-sm font-medium">
                                    <a href="{{ route('users.edit', $user) }}" class="text-blue-600 dark:text-indigo-400 hover:text-blue-700">Edit</a>
                                    @if($user->id !== auth()->id())
                                        @if($user->stock_ins_count || $user->stock_outs_count)
                                            <span class="text-gray-400" title="User masih tercatat pada transaksi">Hapus</span>
                                        @else
                                            <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Yakin hapus user ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-500">Hapus</button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </article>
                        @empty
                            <div class="rounded-xl border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500 dark:text-slate-400">
                                Belum ada data user.
                            </div>
                        @endforelse
                    </div>

                    <div class="hidden overflow-x-auto md:block">
                        <table class="min-w-full bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700">
                            <thead class="bg-gray-50 dark:bg-slate-800 border-b">
                                <tr>
                                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase">Nama</th>
                                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase">Email</th>
                                    <th class="py-3 px-6 text-center text-xs font-medium text-gray-500 dark:text-slate-400 uppercase">Role</th>
                                    <th class="py-3 px-6 text-center text-xs font-medium text-gray-500 dark:text-slate-400 uppercase">Riwayat</th>
                                    <th class="py-3 px-6 text-center text-xs font-medium text-gray-500 dark:text-slate-400 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                                @forelse($users as $user)
                                    <tr class="hover:bg-gray-50 dark:bg-slate-800 transition duration-150">
                                        <td class="py-4 px-6 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-slate-100">{{ $user->name }}</td>
                                        <td class="py-4 px-6 whitespace-nowrap text-sm text-gray-700 dark:text-slate-300">{{ $user->email }}</td>
                                        <td class="py-4 px-6 text-center whitespace-nowrap">
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold text-white {{ $user->role == 'admin' ? 'bg-red-500' : 'bg-blue-500' }}">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 text-center whitespace-nowrap text-sm text-gray-700 dark:text-slate-300">
                                            {{ $user->stock_ins_count + $user->stock_outs_count }}
                                        </td>
                                        <td class="py-4 px-6 text-center whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('users.edit', $user) }}" class="text-blue-600 dark:text-indigo-400 hover:text-blue-700">Edit</a>
                                            @if($user->id !== auth()->id())
                                                @if($user->stock_ins_count || $user->stock_outs_count)
                                                    <span class="ml-3 text-gray-400" title="User masih tercatat pada transaksi">Hapus</span>
                                                @else
                                                    <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline" onsubmit="return confirm('Yakin hapus user ini?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="ml-3 text-red-500 hover:text-red-600 dark:text-rose-400">Hapus</button>
                                                    </form>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-6 text-center text-sm text-gray-500 dark:text-slate-400">Belum ada data user.</td>
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
