@php
    $commands = [
        ['name' => 'Dashboard', 'route' => route('dashboard'), 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
        ['name' => 'Profil Saya', 'route' => route('profile.edit'), 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
    ];

    // If other routes exist, add them carefully
    if (Route::has('products.index')) {
        $commands[] = ['name' => 'Kelola Produk', 'route' => route('products.index'), 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'];
    }
    if (Route::has('raks.index')) {
        $commands[] = ['name' => 'Kelola Rak', 'route' => route('raks.index'), 'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'];
    }
    if (Route::has('stock-in.index')) {
        $commands[] = ['name' => 'Barang Masuk (Inbound)', 'route' => route('stock-in.index'), 'icon' => 'M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z'];
    }
    if (Route::has('stock-out.index')) {
        $commands[] = ['name' => 'Barang Keluar (Outbound)', 'route' => route('stock-out.index'), 'icon' => 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1'];
    }
@endphp

<div
    x-data="{
        open: false,
        search: '',
        staticCommands: @js($commands),
        dynamicCommands: [],
        isLoading: false,
        selectedIndex: 0,
        debounceTimeout: null,
        
        get filteredCommands() {
            if (this.search.trim() === '') return this.staticCommands;
            
            const staticFiltered = this.staticCommands.filter(cmd => cmd.name.toLowerCase().includes(this.search.toLowerCase()));
            return [...staticFiltered, ...this.dynamicCommands];
        },
        
        fetchDynamicResults() {
            const query = this.search.trim();
            if (query === '') {
                this.dynamicCommands = [];
                this.isLoading = false;
                return;
            }
            
            this.isLoading = true;
            clearTimeout(this.debounceTimeout);
            
            this.debounceTimeout = setTimeout(() => {
                fetch(`{{ route('global.search') }}?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        this.dynamicCommands = data;
                        this.isLoading = false;
                        if (this.selectedIndex >= this.filteredCommands.length) {
                            this.selectedIndex = 0;
                        }
                    })
                    .catch(err => {
                        console.error('Search error:', err);
                        this.isLoading = false;
                    });
            }, 300);
        },
        openPalette() {
            this.open = true;
            this.search = '';
            this.selectedIndex = 0;
            setTimeout(() => this.$refs.searchInput.focus(), 50);
        },
        closePalette() {
            this.open = false;
        },
        handleKeydown(e) {
            if (!this.open) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    this.openPalette();
                }
                return;
            }

            if (e.key === 'Escape') {
                this.closePalette();
            } else if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (this.selectedIndex < this.filteredCommands.length - 1) {
                    this.selectedIndex++;
                    this.scrollToSelected();
                }
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (this.selectedIndex > 0) {
                    this.selectedIndex--;
                    this.scrollToSelected();
                }
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (this.filteredCommands.length > 0) {
                    window.location.href = this.filteredCommands[this.selectedIndex].route;
                }
            }
        },
        scrollToSelected() {
            this.$nextTick(() => {
                const activeEl = this.$refs.listbox.children[this.selectedIndex];
                if (activeEl) {
                    activeEl.scrollIntoView({ block: 'nearest' });
                }
            });
        }
    }"
    @keydown.window="handleKeydown"
    @open-command-palette.window="openPalette()"
    x-cloak
>
    <!-- Backdrop -->
    <div
        x-show="open"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[100] bg-slate-900/40 backdrop-blur-sm transition-opacity"
        @click="closePalette"
        style="display: none;"
    ></div>

    <!-- Palette Modal -->
    <div
        x-show="open"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95 -translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 -translate-y-4"
        class="fixed inset-0 z-[110] overflow-y-auto p-4 sm:p-6 md:p-20 flex items-start justify-center pointer-events-none"
        style="display: none;"
    >
        <div class="pointer-events-auto mx-auto w-full max-w-xl transform divide-y divide-slate-100 dark:divide-slate-700/60 overflow-hidden rounded-2xl bg-white dark:bg-slate-900 shadow-2xl dark:shadow-none border border-slate-200 dark:border-slate-700 ring-1 ring-black ring-opacity-5 transition-all">
            <!-- Search Input -->
            <div class="relative">
                <svg class="pointer-events-none absolute left-4 top-3.5 h-5 w-5 text-slate-400 dark:text-slate-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                </svg>
                <input
                    x-ref="searchInput"
                    x-model="search"
                    @input="selectedIndex = 0; fetchDynamicResults()"
                    type="text"
                    class="h-12 w-full border-0 bg-transparent pl-11 pr-4 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-0 sm:text-sm"
                    placeholder="Cari menu, nama produk, kode rak..."
                    role="combobox"
                    aria-expanded="false"
                    aria-controls="options"
                >
                <div class="absolute right-4 top-3 hidden sm:block text-xs font-semibold text-slate-400 dark:text-slate-500 border border-slate-200 dark:border-slate-700 rounded px-1.5 py-0.5">ESC</div>
            </div>

            <!-- Results List -->
            <ul x-ref="listbox" class="max-h-72 scroll-py-2 overflow-y-auto py-2 text-sm text-slate-800 dark:text-slate-200" id="options" role="listbox">
                <template x-if="isLoading">
                    <li class="px-4 py-8 text-center text-sm text-slate-500 dark:text-slate-400">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="h-4 w-4 animate-spin text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Mencari data di server...
                        </div>
                    </li>
                </template>
                <template x-if="!isLoading && filteredCommands.length === 0">
                    <li class="px-4 py-8 text-center text-sm text-slate-500 dark:text-slate-400">
                        <p>Tidak ada hasil yang ditemukan untuk "<span x-text="search" class="font-semibold text-slate-700 dark:text-slate-300"></span>".</p>
                    </li>
                </template>
                <template x-for="(command, index) in filteredCommands" :key="index">
                    <li
                        class="cursor-default select-none px-4 py-2.5 transition-colors"
                        :class="selectedIndex === index ? 'bg-indigo-600 text-white' : 'hover:bg-slate-50 dark:hover:bg-slate-800'"
                        @mouseenter="selectedIndex = index"
                        @click="window.location.href = command.route"
                        role="option"
                    >
                        <div class="flex items-center gap-3">
                            <svg class="h-5 w-5 flex-none" :class="selectedIndex === index ? 'text-indigo-200' : 'text-slate-400 dark:text-slate-500'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" :d="command.icon"></path>
                            </svg>
                            <div class="flex-auto min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="truncate font-medium" x-text="command.name"></span>
                                    <template x-if="command.type">
                                        <span class="inline-flex flex-none items-center rounded-md px-1.5 py-0.5 text-[10px] font-medium" :class="selectedIndex === index ? 'bg-indigo-500/50 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400'" x-text="command.type"></span>
                                    </template>
                                </div>
                                <template x-if="command.description">
                                    <span class="block truncate text-xs mt-0.5" :class="selectedIndex === index ? 'text-indigo-200' : 'text-slate-500 dark:text-slate-400'" x-text="command.description"></span>
                                </template>
                            </div>
                            <span x-show="selectedIndex === index" class="ml-3 flex-none text-xs font-semibold text-indigo-300">Enter</span>
                        </div>
                    </li>
                </template>
            </ul>
            
            <div class="flex items-center gap-2 border-t border-slate-100 dark:border-slate-700/60 bg-slate-50/50 dark:bg-slate-800/30 px-4 py-2.5 text-xs text-slate-500 dark:text-slate-400">
                <span>Gunakan panah <span class="font-bold border border-slate-200 dark:border-slate-600 rounded px-1">↑</span> <span class="font-bold border border-slate-200 dark:border-slate-600 rounded px-1">↓</span> untuk navigasi</span>
                <span class="ml-2">Tekan <span class="font-bold border border-slate-200 dark:border-slate-600 rounded px-1 text-slate-700 dark:text-slate-300">Enter</span> untuk memilih</span>
            </div>
        </div>
    </div>
</div>
