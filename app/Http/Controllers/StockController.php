<?php

namespace App\Http\Controllers;

use App\Models\Stock;

class StockController extends Controller
{
    public function index()
    {
        $stocks = Stock::with('product', 'rak')
            ->where('quantity', '>', 0)
            ->get();

        return view('admin.stocks.index', compact('stocks'));
    }
}
