<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Rak;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_dashboard_shows_personal_activity_summary(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        $otherStaff = User::factory()->create([
            'role' => 'staff',
        ]);

        $rak = Rak::create([
            'name' => 'Rak Operasional',
            'nomor_rak' => 'R-01',
            'tingkat' => '1',
            'bagian' => 'Fast Moving',
            'capacity' => 1000,
        ]);

        $incomingProduct = Product::create([
            'name' => 'Gula Pasir',
            'sku' => 'SKU-GULA-01',
        ]);

        $outgoingProduct = Product::create([
            'name' => 'Tepung Terigu',
            'sku' => 'SKU-TERIGU-01',
        ]);

        $hiddenProduct = Product::create([
            'name' => 'Produk User Lain',
            'sku' => 'SKU-LAIN-01',
        ]);

        StockIn::create([
            'product_id' => $incomingProduct->id,
            'rak_id' => $rak->id,
            'quantity' => 12,
            'date_time' => now()->subDay(),
            'created_by' => $staff->id,
        ]);

        StockOut::create([
            'product_id' => $outgoingProduct->id,
            'rak_id' => $rak->id,
            'quantity' => 5,
            'date_time' => now(),
            'created_by' => $staff->id,
        ]);

        StockIn::create([
            'product_id' => $hiddenProduct->id,
            'rak_id' => $rak->id,
            'quantity' => 20,
            'date_time' => now(),
            'created_by' => $otherStaff->id,
        ]);

        $response = $this
            ->actingAs($staff)
            ->get(route('dashboard'));

        $response
            ->assertOk()
            ->assertSee('Grafik Aktivitas 7 Hari Terakhir')
            ->assertSee('Aktivitas Terakhir Anda')
            ->assertSee('Input Barang Masuk')
            ->assertSee('Input Barang Keluar')
            ->assertSee('Gula Pasir')
            ->assertSee('Tepung Terigu')
            ->assertDontSee('Produk User Lain');
    }
}
