<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Rak;
use App\Models\Stock;
use App\Models\StockIn;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_archive_product_with_transaction_history(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $product = Product::create([
            'name' => 'Produk Arsip',
            'sku' => 'SKU-ARSIP-01',
        ]);

        $rak = Rak::create([
            'name' => 'Rak Transaksi',
            'nomor_rak' => 'R-01',
            'tingkat' => '1',
            'bagian' => 'Fast Moving',
            'capacity' => 1000,
        ]);

        $stockIn = StockIn::create([
            'product_id' => $product->id,
            'rak_id' => $rak->id,
            'quantity' => 10,
            'date_time' => now(),
            'created_by' => $admin->id,
        ]);

        $response = $this
            ->actingAs($admin)
            ->delete(route('products.destroy', $product));

        $response
            ->assertRedirect(route('products.index'))
            ->assertSessionHas('success', fn (string $message) => str_contains($message, 'Riwayat transaksi tetap tersimpan'));

        $this->assertSoftDeleted('products', [
            'id' => $product->id,
        ]);

        $this->assertTrue($stockIn->fresh()->product->trashed());
        $this->assertSame('Produk Arsip', $stockIn->fresh()->product->name);
    }

    public function test_admin_cannot_delete_product_that_still_has_active_stock(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $product = Product::create([
            'name' => 'Produk Aktif',
            'sku' => 'SKU-AKTIF-01',
        ]);

        $rak = Rak::create([
            'name' => 'Rak Aktif',
            'nomor_rak' => 'R-02',
            'tingkat' => '2',
            'bagian' => 'Slow Moving',
            'capacity' => 1000,
        ]);

        Stock::create([
            'product_id' => $product->id,
            'rak_id' => $rak->id,
            'quantity' => 25,
        ]);

        $response = $this
            ->actingAs($admin)
            ->delete(route('products.destroy', $product));

        $response
            ->assertRedirect(route('products.index'))
            ->assertSessionHas('error', fn (string $message) => str_contains($message, 'stok aktif'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'deleted_at' => null,
        ]);
    }

    public function test_admin_can_reuse_name_and_sku_after_product_is_archived(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $product = Product::create([
            'name' => 'Produk Ulang',
            'sku' => 'SKU-ULANG-01',
        ]);

        $this
            ->actingAs($admin)
            ->delete(route('products.destroy', $product))
            ->assertRedirect(route('products.index'));

        $response = $this
            ->actingAs($admin)
            ->post(route('products.store'), [
                'name' => 'Produk Ulang',
                'sku' => 'SKU-ULANG-01',
            ]);

        $response
            ->assertRedirect(route('products.index'))
            ->assertSessionHas('success', 'Produk berhasil ditambahkan.');

        $this->assertSame(2, Product::withTrashed()->where('sku', 'SKU-ULANG-01')->count());
        $this->assertDatabaseHas('products', [
            'name' => 'Produk Ulang',
            'sku' => 'SKU-ULANG-01',
            'deleted_at' => null,
        ]);
    }
}
