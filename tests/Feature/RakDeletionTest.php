<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Rak;
use App\Models\StockIn;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RakDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cannot_delete_rak_that_is_still_used_by_transactions(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $product = Product::create([
            'name' => 'Tepung Terigu',
            'sku' => 'SKU-TEPUNG-01',
        ]);

        $rak = Rak::create([
            'name' => 'Rak Bahan Tepung',
            'nomor_rak' => 'R-01',
            'tingkat' => '1',
            'bagian' => 'Fast Moving',
            'capacity' => 5000,
        ]);

        StockIn::create([
            'product_id' => $product->id,
            'rak_id' => $rak->id,
            'quantity' => 100,
            'date_time' => now(),
            'created_by' => $admin->id,
        ]);

        $response = $this
            ->actingAs($admin)
            ->delete(route('raks.destroy', $rak));

        $response
            ->assertRedirect(route('raks.index'))
            ->assertSessionHas('error', fn (string $message) => str_contains($message, 'transaksi barang masuk'));

        $this->assertDatabaseHas('raks', [
            'id' => $rak->id,
        ]);
    }

    public function test_admin_can_delete_rak_that_has_no_dependencies(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $rak = Rak::create([
            'name' => 'Rak Aman Dihapus',
            'nomor_rak' => 'R-02',
            'tingkat' => '2',
            'bagian' => 'Slow Moving',
            'capacity' => 3000,
        ]);

        $response = $this
            ->actingAs($admin)
            ->delete(route('raks.destroy', $rak));

        $response
            ->assertRedirect(route('raks.index'))
            ->assertSessionHas('success', 'Rak dihapus.');

        $this->assertDatabaseMissing('raks', [
            'id' => $rak->id,
        ]);
    }
}
