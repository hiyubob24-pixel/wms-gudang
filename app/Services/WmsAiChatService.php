<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Throwable;

class WmsAiChatService
{
    public function __construct(
        protected WmsAiContextService $contextService,
    ) {
    }

    public function respond(string $question, ?string $pageContext = null, ?string $previousResponseId = null): array
    {
        $snapshot = $this->contextService->buildSnapshot($pageContext);
        $suggestedQuestions = $this->buildSuggestedQuestions($snapshot);

        if (($provider = $this->resolveLiveAiProvider()) !== null) {
            try {
                $response = match ($provider) {
                    'gemini' => $this->respondWithGemini($question, $snapshot, $pageContext),
                    default => $this->respondWithOpenAi($question, $snapshot, $pageContext, $previousResponseId),
                };

                return [
                    'answer' => $response['answer'],
                    'citations' => $response['citations'],
                    'response_id' => $response['response_id'],
                    'meta' => [
                        'mode' => $provider,
                        'label' => 'AI live',
                    ],
                    'suggested_questions' => $suggestedQuestions,
                ];
            } catch (Throwable $exception) {
                Log::warning("WMS AI assistant falling back to local analytics mode after {$provider} failure.", [
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        return [
            'answer' => $this->respondLocally($question, $snapshot),
            'citations' => [],
            'response_id' => null,
            'meta' => [
                'mode' => 'local',
                'label' => 'Mode analitik',
            ],
            'suggested_questions' => $suggestedQuestions,
        ];
    }

    protected function respondWithOpenAi(string $question, array $snapshot, ?string $pageContext = null, ?string $previousResponseId = null): array
    {
        $tools = $this->buildTools('openai');
        $isAnalytical = $this->isAnalyticalQuestion($question);
        $payload = array_filter([
            'model' => config('services.openai.wms_model', 'gpt-5.4-mini'),
            'instructions' => $this->buildInstructions($snapshot, $pageContext, 'openai'),
            'input' => $this->buildUserInput($question, $pageContext),
            'reasoning' => ['effort' => $this->reasoningEffort($isAnalytical)],
            'tools' => $tools !== [] ? $tools : null,
            'tool_choice' => $tools !== [] ? 'auto' : null,
            'include' => $this->supportsWebSearch('openai') ? ['web_search_call.action.sources'] : null,
            'max_output_tokens' => $this->maxOutputTokens($isAnalytical),
            'previous_response_id' => filled($previousResponseId) ? $previousResponseId : null,
            'store' => true,
        ], fn ($value) => ! is_null($value) && $value !== []);

        $response = Http::withToken(config('services.openai.api_key'))
            ->acceptJson()
            ->asJson()
            ->timeout(45)
            ->baseUrl(rtrim(config('services.openai.base_url', 'https://api.openai.com/v1'), '/'))
            ->post('responses', $payload)
            ->throw()
            ->json();

        $messageContent = $this->extractMessageContent($response);
        $answer = trim((string) ($messageContent['text'] ?? $this->extractOutputText($response)));

        if (blank($answer)) {
            throw new \RuntimeException('OpenAI response did not return text output.');
        }

        return [
            'answer' => $answer,
            'citations' => $this->extractCitations($messageContent),
            'response_id' => data_get($response, 'id'),
        ];
    }

    protected function respondWithGemini(string $question, array $snapshot, ?string $pageContext = null): array
    {
        $isAnalytical = $this->isAnalyticalQuestion($question);
        $payload = [
            'model' => $isAnalytical
                ? config('services.gemini.wms_analysis_model', config('services.gemini.wms_model', 'gemini-2.5-pro'))
                : config('services.gemini.wms_model', 'gemini-2.5-flash'),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $this->buildInstructions($snapshot, $pageContext, 'gemini'),
                ],
                [
                    'role' => 'user',
                    'content' => $this->buildUserInput($question, $pageContext),
                ],
            ],
            'max_completion_tokens' => $this->maxOutputTokens($isAnalytical),
        ];

        $response = Http::withToken(config('services.gemini.api_key'))
            ->acceptJson()
            ->asJson()
            ->timeout(45)
            ->baseUrl(rtrim(config('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta/openai'), '/'))
            ->post('chat/completions', $payload)
            ->throw()
            ->json();

        $answer = trim($this->extractChatCompletionText($response));

        if (blank($answer)) {
            throw new \RuntimeException('Gemini response did not return text output.');
        }

        return [
            'answer' => $answer,
            'citations' => [],
            'response_id' => data_get($response, 'id'),
        ];
    }

    protected function buildInstructions(array $snapshot, ?string $pageContext = null, ?string $provider = null): string
    {
        $contextJson = json_encode(
            $snapshot,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        ) ?: '{}';
        $pageContextLabel = $pageContext ?: 'Panel admin umum';
        $provider = $provider ?? 'openai';

        $webSearchStatus = $this->supportsWebSearch($provider)
            ? 'aktif (bisa mencari info dari internet)'
            : 'tidak aktif (hanya mengandalkan pengetahuan bawaan)';

        return <<<PROMPT
Anda adalah **Atlas Gudang**, asisten AI cerdas, analitis, dan suportif khusus untuk admin WMS. Karakter Anda profesional namun luwes, empatik, dan proaktif.

GAYA KOMUNIKASI:
- Gunakan bahasa Indonesia yang natural, hangat, dan tidak kaku bak robot.
- **SANGAT PENTING**: JANGAN MONOTON! Variasikan kalimat pembuka Anda. Jangan selalu pakai "Berdasarkan data...", "Berikut adalah...", atau "Saat ini...". Gunakan variasi seperti "Baik, mari kita bedah datanya...", "Menarik, ini yang saya temukan...", "Tentu! Dari yang saya lihat...", atau langsung pada poinnya dengan gaya sapaan yang bersahabat.
- Sisipkan emoji secukupnya (🎯, 📦, ⚠️, 📊, dll) agar chat lebih hidup dan enak dibaca.
- Gunakan format markdown secara maksimal: **bold** untuk hal penting, bullet points untuk daftar, atau `code` untuk angka/SKU agar menonjol.

FOKUS DAN TUGAS UTAMA:
1. Menjawab kondisi gudang berdasarkan HANYA data JSON yang diberikan. Jangan mengarang data WMS.
2. Jika ditanya hal umum/luar WMS, jawab dengan pengetahuan Anda (status Web Search: {$webSearchStatus}).
3. Jika admin meminta analisis (hari ini, minggu ini, dsb):
   - Jangan sekadar memuntahkan angka mentah.
   - Buat KESIMPULAN singkat di awal.
   - Sorot HANYA angka/produk paling penting.
   - Selalu berikan 1-3 **saran operasional** (actionable insight) yang masuk akal.
4. Jika tidak ada masalah (misal stok aman), beri afirmasi positif bahwa operasional berjalan lancar.

ATURAN SISTEM:
- Stok menipis: current_stock <= 10.
- Rak hampir penuh: utilisasi >= 80%.
- Konteks halaman admin saat ini: {$pageContextLabel} (gunakan ini untuk menebak arah pertanyaan jika ambigu).

DATA WMS ANDA SAAT INI:
{$contextJson}
PROMPT;
    }

    protected function buildUserInput(string $question, ?string $pageContext = null): string
    {
        $contextLine = $pageContext
            ? "Halaman admin aktif: {$pageContext}."
            : 'Halaman admin aktif tidak disebutkan.';

        return <<<INPUT
{$contextLine}

Tentukan dulu apakah admin sedang menanyakan:
- pengetahuan umum,
- data/analisis WMS,
- atau gabungan keduanya.

Jika ini pertanyaan analisis WMS, jawab seperti analis operasional yang cerdas:
- simpulkan dulu,
- lalu tampilkan data pendukung yang paling relevan,
- lalu berikan insight dan tindakan.

Pertanyaan admin:
{$question}
INPUT;
    }

    protected function extractOutputText(array $response): string
    {
        $outputText = trim((string) data_get($response, 'output_text', ''));

        if ($outputText !== '') {
            return $outputText;
        }

        return collect(data_get($response, 'output', []))
            ->flatMap(function (array $item) {
                return collect($item['content'] ?? [])
                    ->filter(fn (array $content) => in_array($content['type'] ?? null, ['output_text', 'text'], true))
                    ->map(fn (array $content) => $content['text'] ?? '');
            })
            ->filter()
            ->implode("\n\n");
    }

    protected function respondLocally(string $question, array $snapshot): string
    {
        $normalizedQuestion = $this->normalize($question);

        if ($this->containsAny($normalizedQuestion, ['menipis', 'hampir habis', 'stok rendah', 'stok sedikit', 'low stock'])) {
            return $this->buildLowStockAnswer($snapshot);
        }

        if ($this->isMovementQuestion($normalizedQuestion)) {
            return $this->buildMovementAnswer($snapshot, $normalizedQuestion);
        }

        if ($this->containsAny($normalizedQuestion, ['rak penuh', 'kapasitas', 'rak hampir penuh', 'utilisasi', 'sisa rak'])) {
            return $this->buildRackCapacityAnswer($snapshot);
        }

        if ($this->containsAny($normalizedQuestion, ['saran', 'rekomendasi', 'prioritas', 'aksi'])) {
            return $this->buildRecommendationAnswer($snapshot);
        }

        if ($this->isProductQuestion($normalizedQuestion)) {
            $product = $this->findBestProduct($question, $snapshot['products']);

            if ($product) {
                return $this->buildProductAnswer($product, $snapshot);
            }
        }

        if ($this->isRackQuestion($normalizedQuestion)) {
            $rack = $this->findBestRack($question, $snapshot['racks']);

            if ($rack) {
                return $this->buildRackAnswer($rack, $snapshot);
            }
        }

        if (! $this->isLikelyWmsQuestion($normalizedQuestion)) {
            return $this->buildLocalCapabilityNotice();
        }

        return $this->buildSummaryAnswer($snapshot);
    }

    protected function buildSummaryAnswer(array $snapshot): string
    {
        $summary = $snapshot['summary'];
        $lowStock = collect($snapshot['alerts']['low_stock_products'])->pluck('name')->take(3)->implode(', ');
        $nearCapacity = collect($snapshot['alerts']['near_capacity_racks'])->pluck('label')->take(2)->implode(', ');

        $lines = [
            '📦 **Ringkasan Gudang Saat Ini**:',
            "- Total produk aktif: {$summary['total_products']}",
            "- Produk terarsip: {$summary['archived_products']}",
            "- Total stok fisik: {$summary['total_stock_quantity']} unit",
            "- Posisi stok aktif: {$summary['active_stock_positions']}",
            "- Total rak: {$summary['total_racks']} (terisi: {$summary['racks_with_stock']})",
            "- Transaksi hari ini: masuk {$summary['today_transactions_in']}, keluar {$summary['today_transactions_out']}",
            "- Pergerakan 7 hari: masuk {$summary['last_7_days_qty_in']} unit, keluar {$summary['last_7_days_qty_out']} unit",
        ];

        if ($summary['low_stock_count'] > 0) {
            $lines[] = "⚠️ **Alert stok menipis**: {$summary['low_stock_count']} produk" . ($lowStock ? " seperti {$lowStock}" : '');
        }

        if ($summary['near_capacity_rack_count'] > 0) {
            $lines[] = "⚠️ **Rak hampir penuh**: {$summary['near_capacity_rack_count']} rak" . ($nearCapacity ? " seperti {$nearCapacity}" : '');
        }

        $lines[] = '';
        $lines[] = '🎯 **Saran awal**:';
        $lines = array_merge($lines, $this->recommendationBullets($snapshot));

        return implode("\n", $lines);
    }

    protected function buildLowStockAnswer(array $snapshot): string
    {
        $products = collect($snapshot['alerts']['low_stock_products']);

        if ($products->isEmpty()) {
            return "✅ **Kabar baik!** Tidak ada produk dengan stok menipis berdasarkan heuristik sistem saat ini (stok <= 10).\n\n🎯 **Saran**: tetap pantau produk dengan arus keluar tinggi agar tidak telat restock.";
        }

        $lines = ["⚠️ **Perhatian**, ada {$products->count()} produk yang masuk kategori stok menipis saat ini:"];

        foreach ($products->take(10) as $product) {
            $lines[] = "- **{$product['name']}** (`{$this->displaySku($product['sku'])}`) tersisa **{$product['current_stock']}** unit";
        }

        $lines[] = '';
        $lines[] = '🎯 **Saran tindak lanjut**:';
        $lines[] = '- Prioritaskan restock untuk item dengan arus keluar tinggi atau item yang sering dipakai di operasional harian.';
        $lines[] = '- Cek lokasi rak produk terkait agar replenishment bisa diarahkan lebih cepat.';

        return implode("\n", $lines);
    }

    protected function buildRackCapacityAnswer(array $snapshot): string
    {
        $racks = collect($snapshot['alerts']['near_capacity_racks']);

        if ($racks->isEmpty()) {
            return "✅ **Aman!** Belum ada rak yang melewati ambang hampir penuh saat ini.\n\n🎯 **Saran**: kapasitas rak masih relatif aman, jadi fokus bisa diarahkan ke monitoring stok menipis dan arus keluar.";
        }

        $lines = ['⚠️ **Perhatian**, berikut adalah rak dengan utilisasi tinggi saat ini:'];

        foreach ($racks->take(10) as $rack) {
            $capacity = $rack['capacity'] > 0 ? $rack['capacity'] : 'tidak diatur';
            $availableSpace = $rack['available_space'] ?? '-';
            $lines[] = "- **Rak {$rack['label']}**: terisi {$rack['occupancy']}/{$capacity} unit (**{$rack['utilization_percent']}%**), sisa ruang: {$availableSpace}";
        }

        $lines[] = '';
        $lines[] = '🎯 **Saran tindak lanjut**:';
        $lines[] = '- Pertimbangkan redistribusi stok dari rak dengan utilisasi tinggi ke rak yang masih longgar.';
        $lines[] = '- Cek apakah ada rak dengan kapasitas 0 atau belum diatur agar perhitungan utilisasi lebih akurat.';

        return implode("\n", $lines);
    }

    protected function buildMovementAnswer(array $snapshot, string $normalizedQuestion): string
    {
        $direction = $this->detectMovementDirection($normalizedQuestion);
        $scope = $this->detectMovementScope($normalizedQuestion);

        return match ($scope) {
            'today' => $this->buildTodayMovementAnswer($snapshot, $direction),
            'week' => $this->buildWeeklyMovementAnswer($snapshot, $direction),
            'month' => $this->buildCurrentMonthMovementAnswer($snapshot, $direction),
            'recent' => $this->buildRecentMovementAnswer($snapshot, $direction),
            default => $this->buildSixMonthMovementAnswer($snapshot, $direction),
        };
    }

    protected function buildTodayMovementAnswer(array $snapshot, string $direction): string
    {
        $today = $snapshot['today'];

        if ($direction === 'in') {
            if (($today['incoming_transactions'] ?? 0) === 0) {
                return 'Belum ada barang masuk yang tercatat hari ini.';
            }

            $lines = [
                "Barang masuk hari ini tercatat {$today['incoming_transactions']} transaksi dengan total {$today['incoming_quantity']} unit.",
            ];

            $movements = collect($today['incoming_movements'] ?? [])->take(6);

            if ($movements->isNotEmpty()) {
                $lines[] = '';
                $lines[] = 'Rincian terbaru:';

                foreach ($movements as $movement) {
                    $lines[] = $this->formatMovementBullet($movement, true);
                }
            }

            return implode("\n", $lines);
        }

        if ($direction === 'out') {
            if (($today['outgoing_transactions'] ?? 0) === 0) {
                return 'Belum ada barang keluar yang tercatat hari ini.';
            }

            $lines = [
                "Barang keluar hari ini tercatat {$today['outgoing_transactions']} transaksi dengan total {$today['outgoing_quantity']} unit.",
            ];

            $movements = collect($today['outgoing_movements'] ?? [])->take(6);

            if ($movements->isNotEmpty()) {
                $lines[] = '';
                $lines[] = 'Rincian terbaru:';

                foreach ($movements as $movement) {
                    $lines[] = $this->formatMovementBullet($movement, true);
                }
            }

            return implode("\n", $lines);
        }

        if (($today['incoming_transactions'] ?? 0) === 0 && ($today['outgoing_transactions'] ?? 0) === 0) {
            return 'Belum ada transaksi barang masuk atau barang keluar yang tercatat hari ini.';
        }

        $lines = [
            'Laporan gudang hari ini:',
            "- Barang masuk: {$today['incoming_transactions']} transaksi, {$today['incoming_quantity']} unit",
            "- Barang keluar: {$today['outgoing_transactions']} transaksi, {$today['outgoing_quantity']} unit",
        ];

        if (($snapshot['summary']['low_stock_count'] ?? 0) > 0) {
            $lowStock = collect($snapshot['alerts']['low_stock_products'])->pluck('name')->take(3)->implode(', ');
            $lines[] = "- Alert stok menipis: {$snapshot['summary']['low_stock_count']} produk" . ($lowStock !== '' ? " seperti {$lowStock}" : '');
        }

        $movements = collect($snapshot['recent_movements'] ?? [])
            ->filter(function (array $movement) {
                return Carbon::parse($movement['date_time'])->isToday();
            })
            ->take(6);

        if ($movements->isNotEmpty()) {
            $lines[] = '';
            $lines[] = 'Aktivitas terbaru hari ini:';

            foreach ($movements as $movement) {
                $lines[] = $this->formatMovementBullet($movement, true);
            }
        }

        return implode("\n", $lines);
    }

    protected function buildWeeklyMovementAnswer(array $snapshot, string $direction): string
    {
        $dailyFlow = collect($snapshot['daily_flow_7_days'] ?? []);
        $week = $snapshot['current_week'] ?? [];

        if ($dailyFlow->every(fn (array $day) => ($day['incoming'] ?? 0) === 0 && ($day['outgoing'] ?? 0) === 0)) {
            return 'Belum ada pergerakan barang yang tercatat dalam 7 hari terakhir.';
        }

        $lines = [];

        if ($direction === 'in') {
            $lines[] = "Analisis barang masuk minggu ini ({$week['label']}):";
            $lines[] = "- Total barang masuk: {$week['incoming_transactions']} transaksi, {$week['incoming_quantity']} unit";

            $topProducts = collect($week['top_incoming_products'] ?? [])->take(3);

            if ($topProducts->isNotEmpty()) {
                $lines[] = '- Produk masuk dominan minggu ini:';

                foreach ($topProducts as $product) {
                    $lines[] = "- {$product['product_name']} ({$this->displaySku($product['sku'])}) {$product['quantity_this_week']} unit";
                }
            }

            $lines[] = '';
            $lines[] = 'Pola harian 7 hari terakhir:';
        } elseif ($direction === 'out') {
            $lines[] = "Analisis barang keluar minggu ini ({$week['label']}):";
            $lines[] = "- Total barang keluar: {$week['outgoing_transactions']} transaksi, {$week['outgoing_quantity']} unit";

            $topProducts = collect($week['top_outgoing_products'] ?? [])->take(3);

            if ($topProducts->isNotEmpty()) {
                $lines[] = '- Produk keluar dominan minggu ini:';

                foreach ($topProducts as $product) {
                    $lines[] = "- {$product['product_name']} ({$this->displaySku($product['sku'])}) {$product['quantity_this_week']} unit";
                }
            }

            $lines[] = '';
            $lines[] = 'Pola harian 7 hari terakhir:';
        } else {
            $netPrefix = ($week['net_flow'] ?? 0) > 0 ? '+' : '';
            $lines[] = "Ringkasan gudang minggu ini ({$week['label']}):";
            $lines[] = "- Barang masuk: {$week['incoming_transactions']} transaksi, {$week['incoming_quantity']} unit";
            $lines[] = "- Barang keluar: {$week['outgoing_transactions']} transaksi, {$week['outgoing_quantity']} unit";
            $lines[] = "- Net flow: {$netPrefix}{$week['net_flow']} unit";
            $lines[] = '';
            $lines[] = 'Pola harian 7 hari terakhir:';
        }

        foreach ($dailyFlow as $day) {
            if ($direction === 'in') {
                $lines[] = "- {$day['label']}: masuk {$day['incoming']} unit";
                continue;
            }

            if ($direction === 'out') {
                $lines[] = "- {$day['label']}: keluar {$day['outgoing']} unit";
                continue;
            }

            $netPrefix = $day['net_flow'] > 0 ? '+' : '';
            $lines[] = "- {$day['label']}: masuk {$day['incoming']}, keluar {$day['outgoing']}, net {$netPrefix}{$day['net_flow']}";
        }

        return implode("\n", $lines);
    }

    protected function buildCurrentMonthMovementAnswer(array $snapshot, string $direction): string
    {
        $month = $snapshot['current_month'];

        if ($direction === 'in') {
            if (($month['incoming_transactions'] ?? 0) === 0) {
                return "Belum ada barang masuk yang tercatat pada {$month['label']}.";
            }

            $lines = [
                "Sampai saat ini pada {$month['label']}, barang masuk tercatat {$month['incoming_transactions']} transaksi dengan total {$month['incoming_quantity']} unit.",
            ];

            $topProducts = collect($month['top_incoming_products'] ?? [])->take(5);

            if ($topProducts->isNotEmpty()) {
                $lines[] = '';
                $lines[] = 'Produk yang paling banyak masuk bulan ini:';

                foreach ($topProducts as $product) {
                    $lines[] = "- {$product['product_name']} ({$this->displaySku($product['sku'])}) {$product['quantity_this_month']} unit";
                }
            }

            return implode("\n", $lines);
        }

        if ($direction === 'out') {
            if (($month['outgoing_transactions'] ?? 0) === 0) {
                return "Belum ada barang keluar yang tercatat pada {$month['label']}.";
            }

            $lines = [
                "Sampai saat ini pada {$month['label']}, barang keluar tercatat {$month['outgoing_transactions']} transaksi dengan total {$month['outgoing_quantity']} unit.",
            ];

            $topProducts = collect($month['top_outgoing_products'] ?? [])->take(5);

            if ($topProducts->isNotEmpty()) {
                $lines[] = '';
                $lines[] = 'Produk dengan arus keluar tertinggi bulan ini:';

                foreach ($topProducts as $product) {
                    $lines[] = "- {$product['product_name']} ({$this->displaySku($product['sku'])}) {$product['quantity_this_month']} unit";
                }
            }

            return implode("\n", $lines);
        }

        if (($month['incoming_transactions'] ?? 0) === 0 && ($month['outgoing_transactions'] ?? 0) === 0) {
            return "Belum ada transaksi barang masuk atau barang keluar yang tercatat pada {$month['label']}.";
        }

        $netPrefix = ($month['net_flow'] ?? 0) > 0 ? '+' : '';
        $lines = [
            "Ringkasan pergerakan bulan ini ({$month['label']}):",
            "- Barang masuk: {$month['incoming_transactions']} transaksi, {$month['incoming_quantity']} unit",
            "- Barang keluar: {$month['outgoing_transactions']} transaksi, {$month['outgoing_quantity']} unit",
            "- Net flow: {$netPrefix}{$month['net_flow']} unit",
        ];

        $topIn = collect($month['top_incoming_products'] ?? [])->take(3)->pluck('product_name')->implode(', ');
        $topOut = collect($month['top_outgoing_products'] ?? [])->take(3)->pluck('product_name')->implode(', ');

        if ($topIn !== '') {
            $lines[] = "- Produk masuk dominan: {$topIn}";
        }

        if ($topOut !== '') {
            $lines[] = "- Produk keluar dominan: {$topOut}";
        }

        return implode("\n", $lines);
    }

    protected function buildRecentMovementAnswer(array $snapshot, string $direction): string
    {
        $movements = match ($direction) {
            'in' => collect($snapshot['recent_incoming'] ?? [])->take(6),
            'out' => collect($snapshot['recent_outgoing'] ?? [])->take(6),
            default => collect($snapshot['recent_movements'] ?? [])->take(6),
        };

        if ($movements->isEmpty()) {
            return 'Belum ada transaksi terbaru yang bisa diringkas dari data WMS saat ini.';
        }

        $lines = match ($direction) {
            'in' => ['Barang masuk terbaru yang tercatat:'],
            'out' => ['Barang keluar terbaru yang tercatat:'],
            default => ['Aktivitas gudang terbaru yang tercatat:'],
        };

        foreach ($movements as $movement) {
            $lines[] = $this->formatMovementBullet($movement, true);
        }

        return implode("\n", $lines);
    }

    protected function buildSixMonthMovementAnswer(array $snapshot, string $direction): string
    {
        $monthlyFlow = collect($snapshot['monthly_flow_6_months'] ?? []);

        if ($monthlyFlow->isEmpty()) {
            return 'Belum ada data tren yang bisa ditampilkan untuk 6 bulan terakhir.';
        }

        $lines = match ($direction) {
            'in' => ['Tren barang masuk 6 bulan terakhir:'],
            'out' => ['Tren barang keluar 6 bulan terakhir:'],
            default => ['Tren arus barang 6 bulan terakhir:'],
        };

        foreach ($monthlyFlow as $month) {
            if ($direction === 'in') {
                $lines[] = "- {$month['month']}: masuk {$month['incoming']} unit";
                continue;
            }

            if ($direction === 'out') {
                $lines[] = "- {$month['month']}: keluar {$month['outgoing']} unit";
                continue;
            }

            $netPrefix = $month['net_flow'] > 0 ? '+' : '';
            $lines[] = "- {$month['month']}: masuk {$month['incoming']}, keluar {$month['outgoing']}, net {$netPrefix}{$month['net_flow']}";
        }

        return implode("\n", $lines);
    }

    protected function buildRecommendationAnswer(array $snapshot): string
    {
        $lines = ['💡 **Prioritas operasional yang saya sarankan saat ini:**'];
        $lines = array_merge($lines, $this->recommendationBullets($snapshot));

        return implode("\n", $lines);
    }

    protected function buildProductAnswer(array $product, array $snapshot): string
    {
        $positions = collect($snapshot['stock_positions'])
            ->where('product_id', $product['id'])
            ->sortByDesc('quantity')
            ->values();

        $topOutbound = collect($snapshot['top_outbound_products_30_days'])
            ->firstWhere('product_id', $product['id']);

        $lines = [
            "📦 **Status Produk: {$product['name']}**",
            "- SKU: `{$this->displaySku($product['sku'])}`",
            "- Total stok saat ini: **{$product['current_stock']} unit**",
            '- Status data: ' . ($product['archived'] ? '*Produk terarsip*' : '*Produk aktif*'),
        ];

        if ($positions->isEmpty()) {
            $lines[] = '- Lokasi stok: belum ada stok aktif di rak mana pun';
        } else {
            $lines[] = '- **Lokasi stok**:';

            foreach ($positions->take(8) as $position) {
                $lines[] = "  - Rak **{$position['rack_label']}** = {$position['quantity']} unit";
            }
        }

        if ($topOutbound) {
            $lines[] = "- Arus keluar 30 hari terakhir: **{$topOutbound['quantity_30_days']} unit**";
        }

        $lines[] = '';
        $lines[] = '🎯 **Saran**:';

        if ($product['current_stock'] <= 0) {
            $lines[] = '- Produk ini sudah kosong, jadi perlu diputuskan apakah segera restock atau dihentikan sementara.';
        } elseif ($product['current_stock'] <= 10) {
            $lines[] = '- Stok sudah menipis, jadikan prioritas pengadaan (replenishment) jika demand-nya tinggi.';
        } else {
            $lines[] = '- Stok masih tersedia dengan aman. Fokus berikutnya adalah menjaga distribusi antar rak tetap rapi dan mudah dijangkau.';
        }

        return implode("\n", $lines);
    }

    protected function buildRackAnswer(array $rack, array $snapshot): string
    {
        $positions = collect($snapshot['stock_positions'])
            ->where('rack_id', $rack['id'])
            ->sortByDesc('quantity')
            ->values();

        $lines = [
            "🏬 **Status Rak: {$rack['label']}**",
            '- Kapasitas: ' . ($rack['capacity'] > 0 ? "**{$rack['capacity']} unit**" : 'belum diatur'),
            "- Terisi: **{$rack['occupancy']} unit**",
            '- Sisa ruang: ' . ($rack['available_space'] !== null ? "**{$rack['available_space']} unit**" : 'belum bisa dihitung'),
            '- Utilisasi: ' . ($rack['utilization_percent'] !== null ? "**{$rack['utilization_percent']}%**" : 'belum tersedia'),
        ];

        if ($positions->isEmpty()) {
            $lines[] = '- Isi rak: belum ada stok aktif';
        } else {
            $lines[] = '- **Isi rak saat ini**:';

            foreach ($positions->take(8) as $position) {
                $lines[] = "  - **{$position['product_name']}** = {$position['quantity']} unit";
            }
        }

        return implode("\n", $lines);
    }

    protected function recommendationBullets(array $snapshot): array
    {
        $bullets = [];
        $lowStock = collect($snapshot['alerts']['low_stock_products'])->pluck('name')->take(3)->implode(', ');
        $nearCapacity = collect($snapshot['alerts']['near_capacity_racks'])->pluck('label')->take(2)->implode(', ');
        $idleProducts = collect($snapshot['alerts']['idle_products_30_days'])->pluck('name')->take(3)->implode(', ');
        $topOutbound = collect($snapshot['top_outbound_products_30_days'])->pluck('product_name')->take(3)->implode(', ');

        if ($lowStock !== '') {
            $bullets[] = "- Restock atau review safety stock untuk {$lowStock}.";
        }

        if ($nearCapacity !== '') {
            $bullets[] = "- Redistribusikan isi rak dengan utilisasi tinggi seperti {$nearCapacity}.";
        }

        if ($topOutbound !== '') {
            $bullets[] = "- Pantau produk dengan arus keluar tinggi: {$topOutbound}.";
        }

        if ($idleProducts !== '') {
            $bullets[] = "- Evaluasi produk yang tidak bergerak 30 hari terakhir tetapi masih punya stok, misalnya {$idleProducts}.";
        }

        if ($bullets === []) {
            $bullets[] = '- Kondisi gudang relatif stabil. Pertahankan monitoring stok, kapasitas rak, dan transaksi harian secara rutin.';
        }

        return $bullets;
    }

    protected function buildSuggestedQuestions(array $snapshot): array
    {
        $questions = [
            'Analisis barang masuk minggu ini',
            'Laporan gudang hari ini bagaimana?',
            'Barang apa saja yang menipis?',
            'Bagaimana tren barang bulan ini?',
            'Apa saran prioritas operasional hari ini?',
        ];

        return array_values(array_unique(array_filter($questions)));
    }

    protected function buildLocalCapabilityNotice(): string
    {
        return "Panel AI saat ini masih berjalan di Mode analitik lokal, jadi saya hanya bisa menjawab berdasarkan data WMS internal.\n\nUntuk pertanyaan umum, penjelasan yang lebih natural, atau info terbaru dari luar sistem, aktifkan provider AI live seperti Gemini atau OpenAI agar mode AI live dipakai.";
    }

    protected function isProductQuestion(string $normalizedQuestion): bool
    {
        return $this->containsAny($normalizedQuestion, ['produk', 'barang', 'sku', 'stok']);
    }

    protected function isRackQuestion(string $normalizedQuestion): bool
    {
        return $this->containsAny($normalizedQuestion, ['rak', 'zona', 'lokasi', 'kapasitas']);
    }

    protected function isMovementQuestion(string $normalizedQuestion): bool
    {
        return $this->containsAny($normalizedQuestion, [
            'laporan',
            'rekap',
            'ringkas',
            'ringkasan',
            'summary',
            'tren',
            'trend',
            'grafik',
            'pergerakan',
            'transaksi',
            'aktivitas',
            'barang masuk',
            'barang keluar',
            'stok masuk',
            'stok keluar',
            'penerimaan',
            'pengeluaran',
            'harian',
            'mingguan',
            'bulanan',
            'hari ini',
            'bulan ini',
            'masuk',
            'keluar',
        ]);
    }

    protected function findBestProduct(string $question, array $products): ?array
    {
        $normalizedQuestion = $this->normalize($question);
        $bestMatch = null;
        $bestScore = 0.0;

        foreach ($products as $product) {
            foreach (array_filter([$product['sku'] ?? null, $product['name'] ?? null]) as $candidate) {
                $normalizedCandidate = $this->normalize((string) $candidate);

                if ($normalizedCandidate === '') {
                    continue;
                }

                if (str_contains($normalizedQuestion, $normalizedCandidate)) {
                    return $product;
                }

                $score = $this->scoreCandidateMatch($normalizedQuestion, $normalizedCandidate);

                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestMatch = $product;
                }
            }
        }

        return $bestScore >= 0.5 ? $bestMatch : null;
    }

    protected function findBestRack(string $question, array $racks): ?array
    {
        $normalizedQuestion = $this->normalize($question);
        $bestMatch = null;
        $bestScore = 0.0;

        foreach ($racks as $rack) {
            foreach (array_filter([$rack['label'] ?? null, $rack['name'] ?? null]) as $candidate) {
                $normalizedCandidate = $this->normalize((string) $candidate);

                if ($normalizedCandidate === '') {
                    continue;
                }

                if (str_contains($normalizedQuestion, $normalizedCandidate)) {
                    return $rack;
                }

                $score = $this->scoreCandidateMatch($normalizedQuestion, $normalizedCandidate);

                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestMatch = $rack;
                }
            }
        }

        return $bestScore >= 0.5 ? $bestMatch : null;
    }

    protected function scoreCandidateMatch(string $normalizedQuestion, string $normalizedCandidate): float
    {
        $tokens = collect(explode(' ', $normalizedCandidate))
            ->filter(fn (string $token) => strlen($token) >= 3)
            ->unique()
            ->values();

        if ($tokens->isEmpty()) {
            return 0.0;
        }

        $matchedTokens = $tokens->filter(fn (string $token) => str_contains($normalizedQuestion, $token))->count();

        if ($matchedTokens > 0) {
            return $matchedTokens / $tokens->count();
        }

        similar_text($normalizedQuestion, $normalizedCandidate, $percentage);

        return round($percentage / 100, 3);
    }

    protected function containsAny(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($haystack, $this->normalize($needle))) {
                return true;
            }
        }

        return false;
    }

    protected function displaySku(?string $sku): string
    {
        return filled($sku) ? $sku : 'tanpa SKU';
    }

    protected function normalize(string $value): string
    {
        return Str::of($value)
            ->lower()
            ->ascii()
            ->replaceMatches('/[^a-z0-9\s-]/', ' ')
            ->replaceMatches('/\s+/', ' ')
            ->trim()
            ->value();
    }

    protected function detectMovementDirection(string $normalizedQuestion): string
    {
        $asksIncoming = $this->containsAny($normalizedQuestion, ['barang masuk', 'stok masuk', 'penerimaan', 'inbound', 'masuk']);
        $asksOutgoing = $this->containsAny($normalizedQuestion, ['barang keluar', 'stok keluar', 'pengeluaran', 'outbound', 'keluar']);

        if ($asksIncoming && ! $asksOutgoing) {
            return 'in';
        }

        if ($asksOutgoing && ! $asksIncoming) {
            return 'out';
        }

        return 'both';
    }

    protected function detectMovementScope(string $normalizedQuestion): string
    {
        if ($this->containsAny($normalizedQuestion, ['hari ini', 'today'])) {
            return 'today';
        }

        if ($this->containsAny($normalizedQuestion, ['harian'])) {
            return $this->containsAny($normalizedQuestion, ['tren', 'trend', 'grafik'])
                ? 'week'
                : 'today';
        }

        if ($this->containsAny($normalizedQuestion, ['7 hari', 'tujuh hari', 'minggu ini', 'mingguan', 'pekan ini'])) {
            return 'week';
        }

        if ($this->containsAny($normalizedQuestion, ['bulan ini', 'bulan sekarang', 'month to date', 'mtd', 'bulanan'])) {
            return 'month';
        }

        if ($this->containsAny($normalizedQuestion, ['terbaru', 'terakhir', 'recent'])) {
            return 'recent';
        }

        if ($this->containsAny($normalizedQuestion, ['tren', 'trend', 'grafik', '6 bulan', 'enam bulan'])) {
            return 'six_months';
        }

        if ($this->containsAny($normalizedQuestion, ['laporan', 'rekap', 'ringkas', 'ringkasan', 'summary'])) {
            return 'today';
        }

        return $this->detectMovementDirection($normalizedQuestion) === 'both'
            ? 'six_months'
            : 'recent';
    }

    protected function formatMovementBullet(array $movement, bool $includeTime = false): string
    {
        $prefix = ($movement['type'] ?? 'in') === 'in' ? '+' : '-';
        $time = $includeTime
            ? Carbon::parse($movement['date_time'])->format('H:i') . ' '
            : '';

        return "- {$time}{$movement['product_name']} {$prefix}{$movement['quantity']} di {$movement['rack_label']}";
    }

    protected function buildTools(string $provider): array
    {
        if (! $this->supportsWebSearch($provider)) {
            return [];
        }

        return [
            ['type' => 'web_search'],
        ];
    }

    protected function reasoningEffort(bool $isAnalytical = false): string
    {
        $configKey = $isAnalytical
            ? 'services.openai.wms_analysis_reasoning_effort'
            : 'services.openai.wms_reasoning_effort';
        $default = $isAnalytical ? 'medium' : 'low';
        $value = (string) config($configKey, $default);

        if (in_array($value, ['none', 'low', 'medium', 'high', 'xhigh'], true)) {
            return $value;
        }

        return $default;
    }

    protected function maxOutputTokens(bool $isAnalytical = false): int
    {
        return $isAnalytical ? 1400 : 900;
    }

    protected function webSearchEnabled(): bool
    {
        return (bool) config('services.openai.wms_enable_web_search', true);
    }

    protected function supportsWebSearch(string $provider): bool
    {
        return $provider === 'openai' && $this->webSearchEnabled();
    }

    protected function extractChatCompletionText(array $response): string
    {
        $content = data_get($response, 'choices.0.message.content');

        if (is_string($content)) {
            return $content;
        }

        if (is_array($content)) {
            return collect($content)
                ->map(function ($part) {
                    if (is_string($part)) {
                        return $part;
                    }

                    if (is_array($part) && is_string($part['text'] ?? null)) {
                        return $part['text'];
                    }

                    return '';
                })
                ->filter()
                ->implode("\n\n");
        }

        return '';
    }

    protected function extractMessageContent(array $response): array
    {
        $content = collect(data_get($response, 'output', []))
            ->filter(fn (array $item) => ($item['type'] ?? null) === 'message')
            ->flatMap(fn (array $item) => $item['content'] ?? [])
            ->first(fn (array $item) => in_array($item['type'] ?? null, ['output_text', 'text'], true));

        if (! is_array($content)) {
            return [];
        }

        return $content;
    }

    protected function extractCitations(array $messageContent): array
    {
        return collect($messageContent['annotations'] ?? [])
            ->map(function (array $annotation) {
                if (($annotation['type'] ?? null) !== 'url_citation') {
                    return null;
                }

                $citation = is_array($annotation['url_citation'] ?? null)
                    ? $annotation['url_citation']
                    : $annotation;

                $url = $citation['url'] ?? null;

                if (blank($url)) {
                    return null;
                }

                return [
                    'title' => $citation['title'] ?? parse_url($url, PHP_URL_HOST) ?? $url,
                    'url' => $url,
                    'start_index' => max((int) ($citation['start_index'] ?? 0), 0),
                    'end_index' => max((int) ($citation['end_index'] ?? 0), 0),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    protected function isLikelyWmsQuestion(string $normalizedQuestion): bool
    {
        return $this->containsAny($normalizedQuestion, [
            'wms',
            'gudang',
            'stok',
            'barang',
            'produk',
            'sku',
            'rak',
            'lokasi',
            'kapasitas',
            'laporan',
            'transaksi',
            'harian',
            'mingguan',
            'bulanan',
            'masuk',
            'keluar',
            'outbound',
            'inbound',
            'restock',
            'rekomendasi',
            'saran',
            'prioritas',
            'aksi',
            'operasional',
            'sistem',
            'user',
            'admin',
        ]);
    }

    protected function isAnalyticalQuestion(string $question): bool
    {
        $normalizedQuestion = $this->normalize($question);

        return $this->containsAny($normalizedQuestion, [
            'analisis',
            'analisa',
            'insight',
            'evaluasi',
            'kesimpulan',
            'laporan',
            'rekap',
            'ringkas',
            'ringkasan',
            'summary',
            'tren',
            'trend',
            'pola',
            'kenapa',
            'mengapa',
            'minggu ini',
            'bulan ini',
            'hari ini',
            'prioritas',
            'rekomendasi',
            'saran',
        ]);
    }

    protected function hasOpenAiConfiguration(): bool
    {
        return filled(config('services.openai.api_key'));
    }

    protected function hasGeminiConfiguration(): bool
    {
        return filled(config('services.gemini.api_key'));
    }

    protected function resolveLiveAiProvider(): ?string
    {
        $configuredProvider = strtolower((string) config('services.wms_ai.provider', 'auto'));

        return match ($configuredProvider) {
            'gemini' => $this->hasGeminiConfiguration() ? 'gemini' : null,
            'openai' => $this->hasOpenAiConfiguration() ? 'openai' : null,
            default => $this->hasGeminiConfiguration()
                ? 'gemini'
                : ($this->hasOpenAiConfiguration() ? 'openai' : null),
        };
    }
}
