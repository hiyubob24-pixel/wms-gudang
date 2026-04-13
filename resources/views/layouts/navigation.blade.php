@php
    $user = Auth::user();
    $isAdmin = $user?->role === 'admin';
    $canManageTransactions = in_array($user?->role, ['admin', 'staff'], true);
    $userInitial = $user ? strtoupper(substr($user->name, 0, 1)) : 'U';
    $userRole = $user?->role ? ucfirst($user->role) : 'User';

    $navigationGroups = collect([
        [
            'key' => 'operasional',
            'label' => 'Operasional',
            'items' => [
                $canManageTransactions ? ['label' => 'Barang Masuk', 'description' => 'Penerimaan barang', 'route' => route('stock-in.index'), 'active' => request()->routeIs('stock-in.*')] : null,
                $canManageTransactions ? ['label' => 'Barang Keluar', 'description' => 'Pengeluaran stok', 'route' => route('stock-out.create'), 'active' => request()->routeIs('stock-out.*')] : null,
            ],
        ],
        [
            'key' => 'master',
            'label' => 'Master Data',
            'items' => [
                $isAdmin ? ['label' => 'Master Barang', 'description' => 'Data produk dan SKU', 'route' => route('products.index'), 'active' => request()->routeIs('products.*')] : null,
                $isAdmin ? ['label' => 'Master Rak', 'description' => 'Lokasi penyimpanan', 'route' => route('raks.index'), 'active' => request()->routeIs('raks.*')] : null,
                $isAdmin ? ['label' => 'Manajemen User', 'description' => 'Akses dan peran', 'route' => route('users.index'), 'active' => request()->routeIs('users.*')] : null,
            ],
        ],
        [
            'key' => 'monitoring',
            'label' => 'Monitoring',
            'items' => [
                $isAdmin ? ['label' => 'Lihat Stok', 'description' => 'Posisi stok terbaru', 'route' => route('stocks.index'), 'active' => request()->routeIs('stocks.*')] : null,
                $isAdmin ? ['label' => 'Laporan & Grafik', 'description' => 'Insight dan tren stok', 'route' => route('reports.index'), 'active' => request()->routeIs('reports.*')] : null,
            ],
        ],
    ])->map(function ($group) {
        $group['items'] = collect($group['items'])->filter()->values();
        $group['active'] = $group['items']->contains(fn ($item) => $item['active']);
        return $group;
    })->filter(fn ($group) => $group['items']->isNotEmpty())->values();

    $defaultGroup = $navigationGroups->first(fn ($group) => $group['active']) ?? $navigationGroups->first();
@endphp

<nav x-data="{ open: false, mobileGroup: @js($defaultGroup['key'] ?? null) }" class="border-b border-slate-200 bg-white/95 backdrop-blur">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-500 via-sky-500 to-slate-900 text-white shadow-lg shadow-sky-100/80">
                    <x-application-logo class="block h-6 w-auto fill-current" />
                </span>
                <div class="hidden sm:block">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-slate-400">Warehouse</p>
                    <p class="text-sm font-semibold text-slate-900">{{ config('app.name', 'WMS App') }}</p>
                </div>
            </a>

            <div class="hidden lg:flex lg:items-center lg:gap-2">
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-slate-200 text-slate-600 hover:border-slate-300 hover:bg-slate-50 hover:text-slate-900' }} rounded-2xl border px-4 py-2 text-sm font-semibold transition">Dashboard</a>

                @foreach ($navigationGroups as $group)
                    <x-dropdown align="left" width="w-80" contentClasses="rounded-3xl border border-slate-200 bg-white p-2 shadow-xl shadow-slate-200/70">
                        <x-slot name="trigger">
                            <button class="{{ $group['active'] ? 'border-sky-200 bg-sky-50 text-sky-700' : 'border-slate-200 text-slate-600 hover:border-slate-300 hover:bg-slate-50 hover:text-slate-900' }} inline-flex items-center gap-2 rounded-2xl border px-4 py-2 text-sm font-semibold transition">
                                {{ $group['label'] }}
                                <svg class="h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="px-3 pb-2 pt-1 text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">{{ $group['label'] }}</div>
                            @foreach ($group['items'] as $item)
                                <x-dropdown-link :href="$item['route']" class="{{ $item['active'] ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }} rounded-2xl px-4 py-3">
                                    <span class="block text-sm font-semibold">{{ $item['label'] }}</span>
                                    <span class="mt-1 block text-xs {{ $item['active'] ? 'text-slate-200' : 'text-slate-500' }}">{{ $item['description'] }}</span>
                                </x-dropdown-link>
                            @endforeach
                        </x-slot>
                    </x-dropdown>
                @endforeach
            </div>
        </div>

        <div class="flex items-center gap-2">
            <div class="hidden lg:block">
                <x-dropdown align="right" width="w-72" contentClasses="rounded-3xl border border-slate-200 bg-white p-2 shadow-xl shadow-slate-200/70">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm transition hover:border-slate-300 hover:bg-slate-50">
                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-slate-900 text-sm font-semibold text-white">{{ $userInitial }}</span>
                            <span class="hidden text-left xl:block">
                                <span class="block font-semibold text-slate-900">{{ $user->name }}</span>
                                <span class="block text-xs text-slate-500">{{ $userRole }}</span>
                            </span>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="border-b border-slate-100 px-3 py-3">
                            <p class="text-sm font-semibold text-slate-900">{{ $user->name }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $user->email }}</p>
                        </div>
                        <div class="space-y-1 p-2">
                            <x-dropdown-link :href="route('profile.edit')" class="rounded-2xl px-3 py-2 font-medium text-slate-700 hover:bg-slate-50">Profil</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" class="rounded-2xl px-3 py-2 font-medium text-rose-600 hover:bg-rose-50" onclick="event.preventDefault(); this.closest('form').submit();">Keluar</x-dropdown-link>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <button type="button" @click="open = ! open" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 lg:hidden">
                <svg class="h-5 w-5 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round" />
                </svg>
                Menu
            </button>
        </div>
    </div>

    <div x-show="open" class="border-t border-slate-200 bg-slate-50/80 lg:hidden" style="display: none;">
        <div class="mx-auto max-w-7xl space-y-3 px-4 py-4 sm:px-6">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-slate-200 bg-white text-slate-700' }} block rounded-2xl border px-4 py-3" @click="open = false">
                <span class="block text-sm font-semibold">Dashboard</span>
                <span class="mt-1 block text-xs {{ request()->routeIs('dashboard') ? 'text-emerald-600' : 'text-slate-500' }}">Ringkasan aktivitas gudang</span>
            </a>

            @foreach ($navigationGroups as $group)
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                    <button type="button" class="flex w-full items-center justify-between px-4 py-3 text-left" @click="mobileGroup = mobileGroup === '{{ $group['key'] }}' ? null : '{{ $group['key'] }}'">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">{{ $group['label'] }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $group['items']->count() }} menu</p>
                        </div>
                        <svg class="h-4 w-4 text-slate-400 transition" :class="{ 'rotate-180': mobileGroup === '{{ $group['key'] }}' }" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div x-show="mobileGroup === '{{ $group['key'] }}'" class="space-y-2 border-t border-slate-100 p-2" style="display: none;">
                        @foreach ($group['items'] as $item)
                            <a href="{{ $item['route'] }}" class="{{ $item['active'] ? 'bg-slate-900 text-white' : 'bg-slate-50 text-slate-700' }} block rounded-2xl px-4 py-3" @click="open = false">
                                <span class="block text-sm font-semibold">{{ $item['label'] }}</span>
                                <span class="mt-1 block text-xs {{ $item['active'] ? 'text-slate-200' : 'text-slate-500' }}">{{ $item['description'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div class="grid grid-cols-2 gap-3 pt-2">
                <a href="{{ route('profile.edit') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700" @click="open = false">Profil</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-600">Keluar</button>
                </form>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-600">
                <div class="flex items-center gap-3">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-900 text-sm font-semibold text-white">{{ $userInitial }}</span>
                    <div class="min-w-0">
                        <p class="truncate font-semibold text-slate-900">{{ $user->name }}</p>
                        <p class="truncate text-xs text-slate-500">{{ $user->email }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
