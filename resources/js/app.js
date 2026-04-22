import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

window.appToastStack = (initialToasts = []) => ({
    toasts: [],
    timers: {},

    init() {
        this.toasts = initialToasts.map((toast, index) => ({
            ...toast,
            id: toast.id ?? `toast-${Date.now()}-${index}`,
            visible: false,
            remaining: toast.duration ?? 4800,
            startedAt: null,
            paused: false,
        }));

        requestAnimationFrame(() => {
            this.toasts.forEach((toast, index) => {
                window.setTimeout(() => {
                    toast.visible = true;
                    this.arm(toast.id);
                }, index * 110);
            });
        });
    },

    find(id) {
        return this.toasts.find((toast) => toast.id === id);
    },

    arm(id) {
        const toast = this.find(id);

        if (!toast || toast.persistent) {
            return;
        }

        this.clear(id);
        toast.startedAt = Date.now();
        this.timers[id] = window.setTimeout(() => this.dismiss(id), toast.remaining);
    },

    clear(id) {
        if (!this.timers[id]) {
            return;
        }

        window.clearTimeout(this.timers[id]);
        delete this.timers[id];
    },

    pause(id) {
        const toast = this.find(id);

        if (!toast || toast.persistent || toast.paused || !toast.startedAt) {
            return;
        }

        const elapsed = Date.now() - toast.startedAt;
        toast.remaining = Math.max(0, toast.remaining - elapsed);
        toast.paused = true;
        this.clear(id);
    },

    resume(id) {
        const toast = this.find(id);

        if (!toast || toast.persistent || !toast.paused) {
            return;
        }

        toast.paused = false;
        this.arm(id);
    },

    dismiss(id) {
        const toast = this.find(id);

        if (!toast) {
            return;
        }

        toast.visible = false;
        this.clear(id);

        window.setTimeout(() => {
            this.toasts = this.toasts.filter((item) => item.id !== id);
        }, 220);
    },

    progressStyle(toast) {
        return `animation-duration: ${toast.remaining}ms; animation-play-state: ${toast.paused ? 'paused' : 'running'};`;
    },
});

window.adminAiAssistant = ({ endpoint, pageLabel, initialMode = 'local', initialQuickPrompts = [] }) => ({
    endpoint,
    pageLabel,
    mode: initialMode,
    quickPrompts: initialQuickPrompts,
    open: false,
    loading: false,
    draft: '',
    messages: [],
    previousResponseId: null,
    storageKey: 'wms-admin-ai-assistant',

    init() {
        this.restoreState();

        if (!this.messages.length) {
            this.resetConversation();
        }

        window.addEventListener('admin-ai:open', () => {
            this.open = true;
            this.persistState();
            this.scrollMessages();
        });

        if (this.open) {
            this.scrollMessages();
        }
    },

    makeId() {
        return `assistant-${Date.now()}-${Math.random().toString(36).slice(2, 9)}`;
    },

    modeLabel() {
        return this.mode === 'local' ? 'Mode analitik' : 'AI live';
    },

    hasConversation() {
        return this.messages.some((message) => message.role === 'user');
    },

    shouldShowQuickPrompts() {
        return !this.loading && !this.hasConversation();
    },

    introMessage() {
        return {
            id: this.makeId(),
            role: 'assistant',
            text: `Saya siap membantu admin membaca kondisi gudang dari data WMS dan menjawab pertanyaan umum saat AI live aktif.\n\nKonteks saat ini: ${this.pageLabel}.\nAnda bisa tanya ringkasan stok, lokasi produk, kapasitas rak, tren barang masuk/keluar, minta saran operasional, atau pertanyaan umum seperti konsep FIFO dan FEFO.`,
            citations: [],
        };
    },

    messageCitations(message) {
        const citations = Array.isArray(message?.citations) ? message.citations : [];
        const uniqueCitations = [];
        const seen = new Set();

        citations.forEach((citation) => {
            if (!citation?.url) {
                return;
            }

            const key = `${citation.url}::${citation.title ?? ''}`;

            if (seen.has(key)) {
                return;
            }

            seen.add(key);
            uniqueCitations.push(citation);
        });

        return uniqueCitations;
    },

    citationKey(citation, index) {
        return `${citation?.url ?? 'citation'}-${index}`;
    },

    citationLabel(citation, index) {
        const title = citation?.title?.trim();

        return title ? `[${index + 1}] ${title}` : `[${index + 1}] ${citation?.url ?? 'Sumber web'}`;
    },

    formatMarkdown(text) {
        if (!text) return '';
        return text
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/\*\*(.*?)\*\*/g, '<strong class="font-bold text-slate-800">$1</strong>')
            .replace(/\*(.*?)\*/g, '<em class="italic text-slate-700">$1</em>')
            .replace(/`(.*?)`/g, '<code class="bg-slate-200/60 border border-slate-200/80 text-sky-700 px-1.5 py-0.5 rounded-md text-[0.85em] font-mono">$1</code>');
    },

    restoreState() {
        const rawState = window.sessionStorage.getItem(this.storageKey);

        if (!rawState) {
            return;
        }

        try {
            const state = JSON.parse(rawState);

            this.open = Boolean(state.open);
            this.mode = state.mode ?? this.mode;
            this.messages = Array.isArray(state.messages) ? state.messages : [];
            this.previousResponseId = state.previousResponseId ?? null;

            if (Array.isArray(state.quickPrompts) && state.quickPrompts.length) {
                this.quickPrompts = state.quickPrompts.slice(0, 4);
            }
        } catch (error) {
            window.sessionStorage.removeItem(this.storageKey);
        }
    },

    persistState() {
        const pinnedIntro = this.messages[0]?.role === 'assistant' ? [this.messages[0]] : [];
        const recentMessages = this.messages.slice(-16);
        const mergedMessages = [];
        const seenIds = new Set();

        [...pinnedIntro, ...recentMessages].forEach((message) => {
            if (!message?.id || seenIds.has(message.id)) {
                return;
            }

            seenIds.add(message.id);
            mergedMessages.push(message);
        });

        this.messages = mergedMessages;

        window.sessionStorage.setItem(
            this.storageKey,
            JSON.stringify({
                open: this.open,
                mode: this.mode,
                messages: this.messages,
                previousResponseId: this.previousResponseId,
                quickPrompts: this.quickPrompts.slice(0, 4),
            }),
        );
    },

    toggle() {
        this.open = !this.open;
        this.persistState();

        if (this.open) {
            this.scrollMessages();
        }
    },

    closePanel() {
        this.open = false;
        this.persistState();
    },

    resetConversation() {
        this.loading = false;
        this.previousResponseId = null;
        this.messages = [this.introMessage()];
        this.persistState();
        this.scrollMessages();
    },

    sendQuickPrompt(prompt) {
        if (this.loading) {
            return;
        }

        this.draft = prompt;
        this.submit();
    },

    submitFromKeyboard(event) {
        if (event.shiftKey) {
            this.draft = `${this.draft}\n`;
            return;
        }

        this.submit();
    },

    async submit() {
        const message = this.draft.trim();

        if (!message || this.loading) {
            return;
        }

        this.messages.push({
            id: this.makeId(),
            role: 'user',
            text: message,
        });

        this.draft = '';
        this.loading = true;
        this.open = true;
        this.persistState();
        this.scrollMessages();

        try {
            const { data } = await window.axios.post(this.endpoint, {
                message,
                page_context: this.pageLabel,
                previous_response_id: this.previousResponseId,
            });

            this.mode = data?.meta?.mode ?? this.mode;
            this.previousResponseId = data?.response_id ?? null;

            if (Array.isArray(data?.suggested_questions) && data.suggested_questions.length) {
                this.quickPrompts = data.suggested_questions.slice(0, 4);
            }

            this.messages.push({
                id: this.makeId(),
                role: 'assistant',
                text: data?.answer ?? 'Belum ada jawaban yang bisa ditampilkan.',
                citations: Array.isArray(data?.citations) ? data.citations : [],
            });
        } catch (error) {
            const responseMessage = error?.response?.data?.message;
            const fallbackMessage = 'Asisten gudang sedang mengalami kendala. Coba kirim ulang pertanyaan Anda dalam beberapa saat.';

            this.messages.push({
                id: this.makeId(),
                role: 'assistant',
                text: responseMessage || fallbackMessage,
                citations: [],
            });
        } finally {
            this.loading = false;
            this.persistState();
            this.scrollMessages();
        }
    },

    scrollMessages() {
        this.$nextTick(() => {
            this.$refs.messages?.scrollTo({
                top: this.$refs.messages.scrollHeight,
                behavior: 'smooth',
            });
        });
    },
});

Alpine.start();
