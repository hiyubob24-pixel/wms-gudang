<?php

namespace App\Http\Controllers;

use App\Models\Stock;

class StockController extends Controller
{
    public function index()
    {
        $stocks = Stock::with('product', 'rak')->get();
        return view('admin.stocks.index', compact('stocks'));
    }
}