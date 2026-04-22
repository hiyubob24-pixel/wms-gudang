@php
    $pageLabel = match (true) {
        request()->routeIs('dashboard') => 'Dashboard Admin',
        request()->routeIs('stock-in.*') => 'Operasional / Barang Masuk',
        request()->routeIs('stock-out.*') => 'Operasional / Barang Keluar',
        request()->routeIs('products.*') => 'Master Data / Produk',
        request()->routeIs('raks.*') => 'Master Data / Rak',
        request()->routeIs('users.*') => 'Master Data / User',
        request()->routeIs('stocks.*') => 'Monitoring / Posisi Stok',
        request()->routeIs('reports.*') => 'Monitoring / Laporan & Grafik',
        default => 'Panel Admin',
    };

    $defaultQuickPrompts = match (true) {
        request()->routeIs('dashboard') => [
            'Ringkas kondisi gudang saat ini',
            'Apa saran prioritas operasional hari ini?',
            'Produk mana yang stoknya menipis?',
            'Rak mana yang hampir penuh?',
        ],
        request()->routeIs('stock-in.*') => [
            'Ringkas barang masuk terbaru',
            'Produk mana yang stoknya paling bertambah?',
            'Apa saran penempatan rak hari ini?',
            'Ada stok menipis yang perlu segera masuk?',
        ],
        request()->routeIs('stock-out.*') => [
            'Ringkas barang keluar terbaru',
            'Produk mana yang arus keluarnya paling tinggi?',
            'Produk mana yang stoknya menipis?',
            'Apa saran prioritas outbound hari ini?',
        ],
        request()->routeIs('products.*') => [
            'Produk mana yang stoknya menipis?',
            'Produk mana yang stoknya paling besar saat ini?',
            'Produk mana yang tidak bergerak 30 hari terakhir?',
            'Apa saran pengelolaan master produk?',
        ],
        request()->routeIs('raks.*') => [
            'Rak mana yang hampir penuh?',
            'Bagaimana kondisi kapasitas rak saat ini?',
            'Rak mana yang masih longgar?',
            'Apa saran redistribusi stok?',
        ],
        request()->routeIs('stocks.*') => [
            'Ringkas posisi stok saat ini',
            'Di rak mana stok paling padat?',
            'Produk mana yang stoknya menipis?',
            'Apa saran perapihan stok?',
        ],
        request()->routeIs('reports.*') => [
            'Jelaskan tren 6 bulan terakhir',
            'Bulan mana arus keluar paling tinggi?',
            'Apa insight utama dari laporan ini?',
            'Apa saran tindak lanjut dari tren sekarang?',
        ],
        default => [
            'Ringkas kondisi gudang saat ini',
            'Produk mana yang stoknya menipis?',
            'Rak mana yang hampir penuh?',
            'Apa saran prioritas operasional hari ini?',
        ],
    };

    $hasGeminiAi = filled(config('services.gemini.api_key'));
    $hasOpenAi = filled(config('services.openai.api_key'));
    $configuredAiProvider = strtolower((string) config('services.wms_ai.provider', 'auto'));
    $initialAiMode = match ($configuredAiProvider) {
        'gemini' => $hasGeminiAi ? 'gemini' : 'local',
        'openai' => $hasOpenAi ? 'openai' : 'local',
        default => $hasGeminiAi ? 'gemini' : ($hasOpenAi ? 'openai' : 'local'),
    };
@endphp

<div
    x-data="window.adminAiAssistant({
        endpoint: @js(route('admin-ai.chat')),
        pageLabel: @js($pageLabel),
        initialMode: @js($initialAiMode),
        initialQuickPrompts: @js($defaultQuickPrompts),
    })"
    x-init="init()"
    @keydown.window.escape="closePanel()"
    class="app-ai-shell"
    x-cloak
>
    <div
        x-show="open"
        x-transition.opacity
        class="fixed inset-0 z-[60] bg-slate-950/20 backdrop-blur-[2px] sm:hidden"
        @click="closePanel()"
        style="display: none;"
    ></div>

    <section
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="translate-y-4 opacity-0 sm:translate-y-3 sm:translate-x-3"
        x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="translate-y-3 opacity-0"
        class="app-ai-panel fixed inset-x-2 bottom-2 top-2 z-[80] flex min-h-0 flex-col overflow-hidden rounded-[1.5rem] border border-slate-200 dark:border-slate-700/60 bg-white dark:bg-slate-900 shadow-2xl shadow-slate-900/15 dark:shadow-black/40 backdrop-blur sm:inset-x-auto sm:bottom-6 sm:right-6 sm:top-16 sm:w-[28rem] sm:rounded-[2rem]"
        style="display: none;"
    >
        <div
            class="app-ai-panel__header px-4 pb-3 pt-4 text-white transition-[padding] duration-200 sm:px-5 sm:pb-3 sm:pt-4"
            :class="{ 'app-ai-panel__header--compact': hasConversation() }"
        >
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-[linear-gradient(135deg,rgba(255,255,255,0.25),rgba(255,255,255,0.05))] border border-white/20 text-lg font-bold shadow-[inset_0_1px_1px_rgba(255,255,255,0.5)]">AI</span>
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-cyan-100/90">Atlas Gudang</p>
                            <p class="text-sm font-semibold sm:text-base">Asisten Admin WMS</p>
                        </div>
                    </div>
                    <p x-show="!hasConversation()" class="app-ai-panel__lead mt-2 text-sm leading-6 text-cyan-50/85 sm:hidden" style="display: none;">Tanya data gudang atau pertanyaan umum saat AI live aktif.</p>
                    <p x-show="!hasConversation()" class="app-ai-panel__lead mt-2 hidden text-sm leading-relaxed text-cyan-50/85 sm:block" style="display: none;">Tanya stok, lokasi rak, tren gudang, konsep operasional, sampai pertanyaan umum yang masih relevan untuk admin.</p>
                </div>

                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-[11px] font-semibold text-white/90">
                        <span class="app-ai-status-dot"></span>
                        <span x-text="modeLabel()"></span>
                    </span>
                    <button
                        type="button"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-white/10 text-white transition hover:bg-white/20"
                        @click="closePanel()"
                    >
                        <span class="sr-only">Tutup panel AI</span>
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.22 4.22a.75.75 0 011.06 0L10 8.94l4.72-4.72a.75.75 0 111.06 1.06L11.06 10l4.72 4.72a.75.75 0 11-1.06 1.06L10 11.06l-4.72 4.72a.75.75 0 11-1.06-1.06L8.94 10 4.22 5.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>

            <div
                class="app-ai-context-card mt-3 flex items-center justify-between gap-3 rounded-2xl border border-white/10 bg-white/10 px-4 py-2.5 text-xs text-cyan-50/85 transition-all duration-200"
                :class="{ 'app-ai-context-card--compact': hasConversation() }"
            >
                <div>
                    <p class="font-semibold text-white/95">Konteks aktif</p>
                    <p x-text="pageLabel"></p>
                </div>
                <button
                    type="button"
                    class="rounded-xl border border-white/15 px-3 py-2 text-[11px] font-semibold uppercase tracking-[0.16em] text-white transition hover:bg-white/10"
                    @click="resetConversation()"
                >
                    Reset
                </button>
            </div>
        </div>

        <div class="app-ai-panel__body flex min-h-0 flex-1 flex-col overflow-hidden bg-slate-50/80 dark:bg-slate-900/80">
            <div x-show="!hasConversation()" class="hidden border-b border-slate-200/80 dark:border-slate-700/50 px-4 py-2.5 text-xs text-slate-500 dark:text-slate-400 sm:block" style="display: none;">
                Data WMS dibaca secara real-time saat pertanyaan Anda dikirim.
            </div>

            <div x-ref="messages" class="app-ai-messages flex min-h-0 flex-1 flex-col gap-3 overflow-y-auto px-4 py-4">
                <template x-for="message in messages" :key="message.id">
                    <div class="flex" :class="message.role === 'user' ? 'justify-end' : 'justify-start'">
                        <article :class="message.role === 'user' ? 'app-ai-bubble app-ai-bubble--user' : 'app-ai-bubble app-ai-bubble--assistant'">
                            <div class="whitespace-pre-line text-[15px] leading-relaxed sm:text-[15.5px] sm:leading-[1.75]" x-html="formatMarkdown(message.text)"></div>

                            <template x-if="message.role === 'assistant' && messageCitations(message).length">
                                <div class="mt-3 border-t border-slate-200/60 dark:border-slate-600/40 pt-3 text-xs text-slate-500 dark:text-slate-400">
                                    <p class="font-semibold uppercase tracking-[0.16em] text-slate-400 dark:text-slate-500">Sumber web</p>

                                    <div class="mt-2 flex flex-col gap-2">
                                        <template x-for="(citation, index) in messageCitations(message)" :key="citationKey(citation, index)">
                                            <a
                                                class="app-ai-citation rounded-xl border border-slate-200/80 dark:border-slate-600/40 bg-white dark:bg-slate-800/60 px-3 py-2 text-slate-600 dark:text-slate-300 transition hover:border-indigo-300 dark:hover:border-indigo-500/40 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 hover:text-slate-900 dark:hover:text-slate-100"
                                                :href="citation.url"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                            >
                                                <span x-text="citationLabel(citation, index)"></span>
                                            </a>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </article>
                    </div>
                </template>

                <div x-show="loading" class="flex justify-start">
                    <article class="app-ai-bubble app-ai-bubble--assistant">
                        <div class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-300">
                            <div class="flex items-center gap-1">
                                <span class="app-ai-dot"></span>
                                <span class="app-ai-dot" style="animation-delay: .18s;"></span>
                                <span class="app-ai-dot" style="animation-delay: .36s;"></span>
                            </div>
                            <span>Sedang menganalisis pertanyaan dan konteks...</span>
                        </div>
                    </article>
                </div>
            </div>

            <div class="app-ai-composer border-t border-slate-200/80 dark:border-slate-700/50 bg-white dark:bg-slate-900 px-4 py-2.5 backdrop-blur">
                <div x-show="shouldShowQuickPrompts()" class="app-ai-quick-prompts mb-3 hidden gap-2 overflow-x-auto pb-1 sm:flex" style="display: none;">
                    <template x-for="prompt in quickPrompts" :key="prompt">
                        <button
                            type="button"
                            class="shrink-0 whitespace-nowrap rounded-full border border-slate-200/80 dark:border-slate-600/40 bg-white dark:bg-slate-800/60 px-3 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 transition hover:border-indigo-300 dark:hover:border-indigo-500/40 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 hover:text-slate-900 dark:hover:text-slate-100"
                            @click="sendQuickPrompt(prompt)"
                        >
                            <span x-text="prompt"></span>
                        </button>
                    </template>
                </div>

                <div class="rounded-[1.4rem] border border-slate-200/80 dark:border-slate-600/40 bg-white dark:bg-slate-800/50 p-2 shadow-[0_20px_36px_-32px_rgba(15,23,42,0.3)] dark:shadow-[0_20px_36px_-32px_rgba(0,0,0,0.5)] transition-shadow duration-200 focus-within:shadow-[0_20px_36px_-24px_rgba(99,102,241,0.2)] dark:focus-within:shadow-[0_20px_36px_-24px_rgba(99,102,241,0.15)]">
                    <label for="admin-ai-message" class="sr-only">Tulis pertanyaan untuk AI gudang</label>
                    <textarea
                        id="admin-ai-message"
                        x-model="draft"
                        rows="1"
                        class="min-h-[2.8rem] max-h-24 w-full resize-none overflow-y-auto border-0 bg-transparent px-3 py-2 text-[15px] text-slate-700 dark:text-slate-200 shadow-none focus:ring-0 sm:min-h-[3.2rem] sm:max-h-28"
                        placeholder="Tanya soal stok menipis, saran rak, dll..."
                        @keydown.enter.prevent="submitFromKeyboard($event)"
                    ></textarea>

                    <div class="flex items-center justify-between gap-3 border-t border-slate-100 dark:border-slate-700/40 px-2 pt-2.5">
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            <span class="font-semibold text-slate-700 dark:text-slate-200" x-text="modeLabel()"></span>
                            <span class="ml-1">aktif untuk konteks admin.</span>
                        </p>

                        <button
                            type="button"
                            class="app-ai-send-btn inline-flex items-center gap-2 rounded-2xl px-4 py-2 text-sm font-semibold text-white transition disabled:cursor-not-allowed disabled:opacity-60"
                            :disabled="loading || ! draft.trim()"
                            @click="submit()"
                        >
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M3.105 3.105a.75.75 0 01.824-.164l12 5a.75.75 0 010 1.386l-12 5A.75.75 0 012.75 13.75V11.2a.75.75 0 01.58-.73l4.69-1.042L3.33 8.386a.75.75 0 01-.58-.73V3.75a.75.75 0 01.355-.645z" />
                            </svg>
                            Kirim
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <button
        x-show="!open"
        x-transition:enter="transition ease-out duration-180"
        x-transition:enter-start="translate-y-2 opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transition ease-in duration-120"
        x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="translate-y-2 opacity-0"
        type="button"
        class="app-ai-fab fixed bottom-4 right-4 z-[70] inline-flex items-center gap-3 rounded-[1.45rem] border border-white/35 dark:border-slate-700/60 px-4 py-3 shadow-xl shadow-sky-200/60 dark:shadow-none transition sm:bottom-6 sm:right-6"
        @click="toggle()"
        style="display: none;"
    >
        <span class="inline-flex h-[3.25rem] w-[3.25rem] items-center justify-center rounded-[1.3rem] bg-[linear-gradient(135deg,#4338ca_0%,#7c3aed_54%,#0f172a_100%)] text-[1.05rem] font-bold text-white shadow-[0_22px_36px_-24px_rgba(2,132,199,0.6)] border border-white/10">AI</span>
        <span class="hidden text-left sm:block">
            <span class="block text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">WMS Assistant</span>
            <span class="block text-[15px] font-semibold text-slate-900 dark:text-slate-100">Tanya data & saran gudang</span>
        </span>
    </button>
</div>
