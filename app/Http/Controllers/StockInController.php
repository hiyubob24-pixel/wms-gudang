<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Rak;
use App\Models\Stock;
use App\Models\StockIn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockInController extends Controller
{
    public function index() {
        // Penambahan 'user' pada eager loading
        $stockIns = StockIn::with(['product', 'rak', 'user'])->orderBy('date_time', 'desc')->get();
        return view('stock_in.index', compact('stockIns'));
    }

    public function create() {
        $products = Product::all();
        
        $raks = Rak::with('stocks')->get()->filter(function ($rak) {
            return $rak->available_space > 0;
        }); 
        
        return view('stock_in.create', compact('products', 'raks'));
    }

    public function store(Request $request) {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rak_id'     => 'required|exists:raks,id',
            'quantity'   => 'required|integer|min:1',
            'date_time'  => 'required|date',
        ]);

        $rak = Rak::with('stocks')->findOrFail($request->rak_id);

        if (($rak->current_occupancy + $request->quantity) > $rak->capacity) {
            return redirect()->back()
                ->with('error', "Rak Penuh! Kapasitas: {$rak->capacity}, Terisi: {$rak->current_occupancy}, Sisa ruang: {$rak->available_space}.")
                ->withInput();
        }

        DB::transaction(function () use ($request) {
            StockIn::create([
                'product_id' => $request->product_id,
                'rak_id'     => $request->rak_id,
                'quantity'   => $request->quantity,
                'date_time'  => $request->date_time,
                'created_by' => Auth::id(),
            ]);

            $stock = Stock::where('product_id', $request->product_id)
                          ->where('rak_id', $request->rak_id)
                          ->first();
                          
            if ($stock) {
                $stock->quantity += $request->quantity;
                $stock->save();
            } else {
                Stock::create([
                    'product_id' => $request->product_id,
                    'rak_id'     => $request->rak_id,
                    'quantity'   => $request->quantity,
                ]);
            }
        });

        return redirect()->route('stock-in.index')->with('success', 'Barang masuk berhasil dicatat.');
    }

    public function edit($id) {
        $stockIn = StockIn::findOrFail($id);
        $products = Product::all();
        
        $raks = Rak::with('stocks')->get()->filter(function ($rak) use ($stockIn) {
            return $rak->available_space > 0 || $rak->id == $stockIn->rak_id;
        });
        
        return view('stock_in.edit', compact('stockIn', 'products', 'raks'));
    }

    public function update(Request $request, $id) {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rak_id'     => 'required|exists:raks,id',
            'quantity'   => 'required|integer|min:1',
            'date_time'  => 'required|date',
        ]);

        $stockIn = StockIn::findOrFail($id);
        $rak = Rak::with('stocks')->findOrFail($request->rak_id);

        $virtualOccupancy = ($rak->current_occupancy - $stockIn->quantity) + $request->quantity;

        if ($virtualOccupancy > $rak->capacity) {
            return redirect()->back()->with('error', "Kapasitas rak tidak mencukupi untuk perubahan ini.")->withInput();
        }

        DB::transaction(function () use ($request, $stockIn) {
            $oldStock = Stock::where('product_id', $stockIn->product_id)->where('rak_id', $stockIn->rak_id)->first();
            if ($oldStock) {
                $oldStock->quantity -= $stockIn->quantity;
                $oldStock->save();
            }

            $stockIn->update([
                'product_id' => $request->product_id,
                'rak_id'     => $request->rak_id,
                'quantity'   => $request->quantity,
                'date_time'  => $request->date_time,
            ]);

            $newStock = Stock::where('product_id', $request->product_id)->where('rak_id', $request->rak_id)->first();
            if ($newStock) {
                $newStock->quantity += $request->quantity;
                $newStock->save();
            } else {
                Stock::create([
                    'product_id' => $request->product_id,
                    'rak_id'     => $request->rak_id,
                    'quantity'   => $request->quantity,
                ]);
            }
        });

        return redirect()->route('stock-in.index')->with('success', 'Data berhasil dikalibrasi.');
    }
}