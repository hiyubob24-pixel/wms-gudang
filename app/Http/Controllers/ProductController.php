<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::withCount([
            'stocks as active_stocks_count' => fn ($query) => $query->where('quantity', '>', 0),
            'stockIns',
            'stockOuts',
        ])
            ->orderBy('name')
            ->get();

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                Rule::unique('products')->whereNull('deleted_at'),
            ],
            'sku' => [
                'nullable',
                Rule::unique('products')->whereNull('deleted_at'),
            ],
        ]);

        Product::create($request->only('name', 'sku'));

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => [
                'required',
                Rule::unique('products')->ignore($product->id)->whereNull('deleted_at'),
            ],
            'sku' => [
                'nullable',
                Rule::unique('products')->ignore($product->id)->whereNull('deleted_at'),
            ],
        ]);

        $product->update($request->only('name', 'sku'));

        return redirect()->route('products.index')->with('success', 'Produk berhasil diupdate.');
    }

    public function destroy(Product $product)
    {
        $product->loadCount([
            'stocks as active_stocks_count' => fn ($query) => $query->where('quantity', '>', 0),
            'stockIns',
            'stockOuts',
        ]);

        $dependencies = collect([
            $product->active_stocks_count ? "{$product->active_stocks_count} stok aktif" : null,
        ])->filter();

        if ($dependencies->isNotEmpty()) {
            return redirect()
                ->route('products.index')
                ->with('error', 'Produk tidak bisa dihapus karena masih memiliki '.$dependencies->join(', ', ' dan ').'. Kosongkan stoknya terlebih dahulu.');
        }

        try {
            $product->delete();
        } catch (QueryException $exception) {
            return redirect()
                ->route('products.index')
                ->with('error', 'Produk tidak bisa dihapus karena masih terhubung dengan data lain.');
        }

        $message = 'Produk dihapus dari master data.';

        if ($product->stock_ins_count || $product->stock_outs_count) {
            $message .= ' Riwayat transaksi tetap tersimpan.';
        }

        return redirect()->route('products.index')->with('success', $message);
    }
}
