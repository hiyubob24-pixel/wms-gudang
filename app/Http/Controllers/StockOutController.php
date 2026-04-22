<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Rak;
use App\Models\Stock;
use App\Models\StockOut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockOutController extends Controller
{
    public function index()
    {
        // Penambahan 'user' pada eager loading
        $stockOuts = StockOut::with(['product', 'rak', 'user'])->orderBy('date_time', 'desc')->get();
        return view('stock_out.index', compact('stockOuts'));
    }

    public function create()
    {
        $products = Product::orderBy('name')->get();
        return view('stock_out.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rak_id' => 'required|exists:raks,id',
            'quantity' => 'required|integer|min:1',
            'date_time' => 'required|date',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $stock = Stock::where('product_id', $request->product_id)
                              ->where('rak_id', $request->rak_id)
                              ->lockForUpdate()
                              ->first();
                              
                if (!$stock || $stock->quantity < $request->quantity) {
                    $tersedia = $stock ? $stock->quantity : 0;
                    throw new \Exception("Stok tidak mencukupi di rak ini. Tersedia: {$tersedia} unit.");
                }

                StockOut::create([
                    'product_id' => $request->product_id,
                    'rak_id' => $request->rak_id,
                    'quantity' => $request->quantity,
                    'date_time' => $request->date_time,
                    'created_by' => Auth::id(),
                ]);

                $stock->quantity -= $request->quantity;
                $stock->save();
            });

            return redirect()->route('stock-out.index')->with('success', 'Barang keluar berhasil dicatat.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error_stok', $e->getMessage())->withInput();
        }
    }

    public function getRaksByProduct($productId)
    {
        $stocks = Stock::with('rak')
                    ->where('product_id', $productId)
                    ->where('quantity', '>', 0)
                    ->get()
                    ->map(function($stock) {
                        return [
                            'id' => $stock->rak_id,
                            'display_name' => "{$stock->rak->nomor_rak} - {$stock->rak->tingkat} - {$stock->rak->bagian} (Stok: {$stock->quantity})"
                        ];
                    });

        return response()->json($stocks);
    }

    public function edit($id)
    {
        $stockOut = StockOut::findOrFail($id);
        $products = Product::orderBy('name')->get();

        $currentProduct = Product::withTrashed()->find($stockOut->product_id);
        if ($currentProduct && !$products->contains('id', $currentProduct->id)) {
            $products->prepend($currentProduct);
        }

        $raks = Rak::all()->map(function($rak) {
            $rak->display_name = "{$rak->nomor_rak} - {$rak->tingkat} - {$rak->bagian}";
            return $rak;
        });
        return view('stock_out.edit', compact('stockOut', 'products', 'raks'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rak_id' => 'required|exists:raks,id',
            'quantity' => 'required|integer|min:1',
            'date_time' => 'required|date',
        ]);

        $stockOut = StockOut::findOrFail($id);

        try {
            DB::transaction(function () use ($request, $stockOut) {
                $oldStock = Stock::where('product_id', $stockOut->product_id)->where('rak_id', $stockOut->rak_id)->first();
                if ($oldStock) {
                    $oldStock->quantity += $stockOut->quantity;
                    $oldStock->save();
                }

                $newStock = Stock::where('product_id', $request->product_id)->where('rak_id', $request->rak_id)->first();
                if (!$newStock || $newStock->quantity < $request->quantity) {
                    throw new \Exception('Gagal Edit: Stok di rak tujuan tidak mencukupi.');
                }

                $stockOut->update([
                    'product_id' => $request->product_id,
                    'rak_id' => $request->rak_id,
                    'quantity' => $request->quantity,
                    'date_time' => $request->date_time,
                ]);

                $newStock->quantity -= $request->quantity;
                $newStock->save();
            });
            return redirect()->route('stock-out.index')->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error_stok', $e->getMessage())->withInput();
        }
    }
}
