<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Rak;
use App\Models\Stock;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AdminAiChatTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_get_grounded_local_ai_answer_from_wms_data(): void
    {
        config([
            'services.wms_ai.provider' => 'auto',
            'services.openai.api_key' => null,
            'services.gemini.api_key' => null,
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $rak = Rak::create([
            'name' => 'Rak Operasional A',
            'nomor_rak' => 'R-01',
            'tingkat' => '1',
            'bagian' => 'Fast Moving',
            'capacity' => 100,
        ]);

        $product = Product::create([
            'name' => 'Gula Pasir',
            'sku' => 'SKU-GULA-01',
        ]);

        Stock::create([
            'product_id' => $product->id,
            'rak_id' => $rak->id,
            'quantity' => 12,
        ]);

        $response = $this
            ->actingAs($admin)
            ->postJson(route('admin-ai.chat'), [
                'message' => 'Di rak mana Gula Pasir disimpan sekarang?',
                'page_context' => 'Monitoring / Posisi Stok',
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('meta.mode', 'local')
            ->assertJsonPath('response_id', null)
            ->assertJsonStructure([
                'answer',
                'meta' => ['mode', 'label'],
                'suggested_questions',
            ]);

        $this->assertStringContainsString('Gula Pasir', $response->json('answer'));
        $this->assertStringContainsString('R-01 - 1 - Fast Moving', $response->json('answer'));
    }

    public function test_staff_cannot_access_admin_ai_chat_endpoint(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        $this
            ->actingAs($staff)
            ->postJson(route('admin-ai.chat'), [
                'message' => 'Ringkas kondisi gudang saat ini',
            ])
            ->assertForbidden();
    }

    public function test_local_ai_answers_daily_incoming_question_with_today_summary(): void
    {
        config([
            'services.wms_ai.provider' => 'auto',
            'services.openai.api_key' => null,
            'services.gemini.api_key' => null,
        ]);
        Carbon::setTestNow('2026-04-19 10:30:00');

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $rak = Rak::create([
            'name' => 'Rak Harian',
            'nomor_rak' => 'A-01',
            'tingkat' => '1',
            'bagian' => 'Inbound',
            'capacity' => 100,
        ]);

        $tepung = Product::create([
            'name' => 'Tepung',
            'sku' => 'SKU-TEPUNG-01',
        ]);

        $gula = Product::create([
            'name' => 'Gula',
            'sku' => 'SKU-GULA-02',
        ]);

        StockIn::create([
            'product_id' => $tepung->id,
            'rak_id' => $rak->id,
            'quantity' => 3,
            'date_time' => now()->copy()->setTime(9, 15),
            'created_by' => $admin->id,
        ]);

        StockIn::create([
            'product_id' => $gula->id,
            'rak_id' => $rak->id,
            'quantity' => 1,
            'date_time' => now()->copy()->setTime(8, 40),
            'created_by' => $admin->id,
        ]);

        StockIn::create([
            'product_id' => $tepung->id,
            'rak_id' => $rak->id,
            'quantity' => 9,
            'date_time' => now()->copy()->subDay()->setTime(15, 0),
            'created_by' => $admin->id,
        ]);

        $response = $this
            ->actingAs($admin)
            ->postJson(route('admin-ai.chat'), [
                'message' => 'barang masuk harian',
                'page_context' => 'Operasional / Barang Masuk',
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('meta.mode', 'local');

        $this->assertStringContainsString('Barang masuk hari ini tercatat 2 transaksi dengan total 4 unit.', $response->json('answer'));
        $this->assertStringContainsString('Tepung', $response->json('answer'));
        $this->assertStringContainsString('Gula', $response->json('answer'));
        $this->assertStringNotContainsString('6 bulan terakhir', $response->json('answer'));

        Carbon::setTestNow();
    }

    public function test_local_ai_answers_monthly_trend_question_with_current_month_summary(): void
    {
        config([
            'services.wms_ai.provider' => 'auto',
            'services.openai.api_key' => null,
            'services.gemini.api_key' => null,
        ]);
        Carbon::setTestNow('2026-04-19 10:30:00');

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $rak = Rak::create([
            'name' => 'Rak Bulanan',
            'nomor_rak' => 'B-02',
            'tingkat' => '2',
            'bagian' => 'Fast Moving',
            'capacity' => 120,
        ]);

        $tepung = Product::create([
            'name' => 'Tepung Terigu',
            'sku' => 'SKU-TEP-11',
        ]);

        $beras = Product::create([
            'name' => 'Beras',
            'sku' => 'SKU-BRS-12',
        ]);

        StockIn::create([
            'product_id' => $tepung->id,
            'rak_id' => $rak->id,
            'quantity' => 12,
            'date_time' => now()->copy()->startOfMonth()->addDays(2)->setTime(9, 0),
            'created_by' => $admin->id,
        ]);

        StockIn::create([
            'product_id' => $beras->id,
            'rak_id' => $rak->id,
            'quantity' => 8,
            'date_time' => now()->copy()->startOfMonth()->addDays(5)->setTime(14, 0),
            'created_by' => $admin->id,
        ]);

        StockOut::create([
            'product_id' => $tepung->id,
            'rak_id' => $rak->id,
            'quantity' => 4,
            'date_time' => now()->copy()->startOfMonth()->addDays(7)->setTime(11, 0),
            'created_by' => $admin->id,
        ]);

        StockIn::create([
            'product_id' => $tepung->id,
            'rak_id' => $rak->id,
            'quantity' => 50,
            'date_time' => now()->copy()->subMonth()->startOfMonth()->addDays(3)->setTime(10, 0),
            'created_by' => $admin->id,
        ]);

        $response = $this
            ->actingAs($admin)
            ->postJson(route('admin-ai.chat'), [
                'message' => 'trend barang bulan ini',
                'page_context' => 'Monitoring / Laporan & Grafik',
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('meta.mode', 'local');

        $this->assertStringContainsString('Ringkasan pergerakan bulan ini (Apr 2026):', $response->json('answer'));
        $this->assertStringContainsString('Barang masuk: 2 transaksi, 20 unit', $response->json('answer'));
        $this->assertStringContainsString('Barang keluar: 1 transaksi, 4 unit', $response->json('answer'));
        $this->assertStringNotContainsString('6 bulan terakhir', $response->json('answer'));

        Carbon::setTestNow();
    }

    public function test_admin_chat_uses_openai_for_hybrid_questions_when_api_is_configured(): void
    {
        config([
            'services.wms_ai.provider' => 'openai',
            'services.openai.api_key' => 'test-openai-key',
            'services.openai.base_url' => 'https://api.openai.com/v1',
            'services.openai.wms_model' => 'gpt-5.4-mini',
            'services.openai.wms_reasoning_effort' => 'low',
            'services.openai.wms_enable_web_search' => true,
            'services.gemini.api_key' => null,
        ]);

        Http::fake([
            'https://api.openai.com/v1/responses' => Http::response([
                'id' => 'resp_test_123',
                'output' => [
                    [
                        'type' => 'message',
                        'status' => 'completed',
                        'role' => 'assistant',
                        'content' => [
                            [
                                'type' => 'output_text',
                                'text' => 'FIFO adalah metode pengeluaran stok berdasarkan barang yang masuk lebih dulu.',
                                'annotations' => [
                                    [
                                        'type' => 'url_citation',
                                        'start_index' => 0,
                                        'end_index' => 4,
                                        'url' => 'https://example.com/fifo',
                                        'title' => 'Panduan FIFO',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this
            ->actingAs($admin)
            ->postJson(route('admin-ai.chat'), [
                'message' => 'Jelaskan FIFO lalu hubungkan dengan kondisi stok di sistem ini.',
                'page_context' => 'Dashboard Admin',
                'previous_response_id' => 'resp_prev_001',
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('meta.mode', 'openai')
            ->assertJsonPath('response_id', 'resp_test_123')
            ->assertJsonPath('citations.0.url', 'https://example.com/fifo')
            ->assertJsonPath('citations.0.title', 'Panduan FIFO');

        Http::assertSent(function (HttpRequest $request) {
            $data = $request->data();

            return $request->url() === 'https://api.openai.com/v1/responses'
                && $request->hasHeader('Authorization', 'Bearer test-openai-key')
                && data_get($data, 'model') === 'gpt-5.4-mini'
                && data_get($data, 'reasoning.effort') === 'low'
                && data_get($data, 'tools.0.type') === 'web_search'
                && data_get($data, 'tool_choice') === 'auto'
                && data_get($data, 'previous_response_id') === 'resp_prev_001'
                && str_contains((string) data_get($data, 'instructions'), 'asisten AI hybrid')
                && str_contains((string) data_get($data, 'instructions'), 'Gunakan HANYA data WMS')
                && str_contains((string) data_get($data, 'input'), 'gabungan keduanya');
        });
    }

    public function test_analytical_admin_question_uses_deeper_openai_reasoning(): void
    {
        config([
            'services.wms_ai.provider' => 'openai',
            'services.openai.api_key' => 'test-openai-key',
            'services.openai.base_url' => 'https://api.openai.com/v1',
            'services.openai.wms_model' => 'gpt-5.4-mini',
            'services.openai.wms_reasoning_effort' => 'low',
            'services.openai.wms_analysis_reasoning_effort' => 'medium',
            'services.openai.wms_enable_web_search' => true,
            'services.gemini.api_key' => null,
        ]);

        Http::fake([
            'https://api.openai.com/v1/responses' => Http::response([
                'id' => 'resp_analysis_456',
                'output_text' => 'Barang masuk minggu ini didominasi dua SKU utama dan menunjukkan pola replenishment yang sehat.',
            ], 200),
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this
            ->actingAs($admin)
            ->postJson(route('admin-ai.chat'), [
                'message' => 'Analisis barang masuk minggu ini',
                'page_context' => 'Operasional / Barang Masuk',
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('meta.mode', 'openai')
            ->assertJsonPath('response_id', 'resp_analysis_456');

        Http::assertSent(function (HttpRequest $request) {
            $data = $request->data();

            return data_get($data, 'reasoning.effort') === 'medium'
                && data_get($data, 'max_output_tokens') === 1400
                && data_get($data, 'store') === true
                && str_contains((string) data_get($data, 'instructions'), 'seperti AI nyata')
                && str_contains((string) data_get($data, 'input'), 'analis operasional yang cerdas');
        });
    }

    public function test_admin_chat_uses_gemini_when_provider_is_gemini(): void
    {
        config([
            'services.wms_ai.provider' => 'gemini',
            'services.gemini.api_key' => 'test-gemini-key',
            'services.gemini.base_url' => 'https://generativelanguage.googleapis.com/v1beta/openai',
            'services.gemini.wms_model' => 'gemini-2.5-flash',
            'services.openai.api_key' => null,
        ]);

        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/openai/chat/completions' => Http::response([
                'id' => 'gemini_resp_123',
                'choices' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'content' => 'FIFO berarti barang yang masuk lebih dulu diprioritaskan keluar lebih dulu, dan prinsip ini bisa dipakai untuk menjaga perputaran stok di gudang.',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this
            ->actingAs($admin)
            ->postJson(route('admin-ai.chat'), [
                'message' => 'Jelaskan FIFO lalu hubungkan dengan kondisi stok di sistem ini.',
                'page_context' => 'Dashboard Admin',
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('meta.mode', 'gemini')
            ->assertJsonPath('response_id', 'gemini_resp_123');

        Http::assertSent(function (HttpRequest $request) {
            $data = $request->data();

            return $request->url() === 'https://generativelanguage.googleapis.com/v1beta/openai/chat/completions'
                && $request->hasHeader('Authorization', 'Bearer test-gemini-key')
                && data_get($data, 'model') === 'gemini-2.5-flash'
                && data_get($data, 'messages.0.role') === 'system'
                && str_contains((string) data_get($data, 'messages.0.content'), 'asisten AI hybrid')
                && str_contains((string) data_get($data, 'messages.0.content'), 'Status web search saat ini: tidak aktif')
                && data_get($data, 'messages.1.role') === 'user'
                && str_contains((string) data_get($data, 'messages.1.content'), 'gabungan keduanya');
        });
    }

    public function test_analytical_admin_question_uses_gemini_analysis_model(): void
    {
        config([
            'services.wms_ai.provider' => 'gemini',
            'services.gemini.api_key' => 'test-gemini-key',
            'services.gemini.base_url' => 'https://generativelanguage.googleapis.com/v1beta/openai',
            'services.gemini.wms_model' => 'gemini-2.5-flash',
            'services.gemini.wms_analysis_model' => 'gemini-2.5-pro',
            'services.openai.api_key' => null,
        ]);

        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/openai/chat/completions' => Http::response([
                'id' => 'gemini_analysis_456',
                'choices' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'content' => [
                                ['type' => 'text', 'text' => 'Barang masuk minggu ini cukup sehat dan terlihat terkonsentrasi pada beberapa SKU utama.'],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this
            ->actingAs($admin)
            ->postJson(route('admin-ai.chat'), [
                'message' => 'Analisis barang masuk minggu ini',
                'page_context' => 'Operasional / Barang Masuk',
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('meta.mode', 'gemini')
            ->assertJsonPath('response_id', 'gemini_analysis_456');

        Http::assertSent(function (HttpRequest $request) {
            $data = $request->data();

            return data_get($data, 'model') === 'gemini-2.5-pro'
                && data_get($data, 'max_completion_tokens') === 1400
                && str_contains((string) data_get($data, 'messages.0.content'), 'seperti AI nyata')
                && str_contains((string) data_get($data, 'messages.1.content'), 'analis operasional yang cerdas');
        });
    }

    public function test_admin_stock_page_renders_ai_assistant(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $rak = Rak::create([
            'name' => 'Rak Monitoring',
            'nomor_rak' => 'R-02',
            'tingkat' => '2',
            'bagian' => 'Reserve',
            'capacity' => 150,
        ]);

        $product = Product::create([
            'name' => 'Minyak Goreng',
            'sku' => 'SKU-MINYAK-01',
        ]);

        Stock::create([
            'product_id' => $product->id,
            'rak_id' => $rak->id,
            'quantity' => 40,
        ]);

        $this
            ->actingAs($admin)
            ->get(route('stocks.index'))
            ->assertOk()
            ->assertSee('Atlas Gudang')
            ->assertSee('Tanya stok & saran gudang', false);
    }
}
