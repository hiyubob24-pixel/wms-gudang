<?php

namespace App\Http\Controllers;

use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\Stock;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role === 'admin') {
            // Data eksisting
            $totalProducts = Product::count();
            $totalStock = Stock::sum('quantity');
            $recentStockIns = StockIn::with('product', 'rak')->orderBy('date_time', 'desc')->limit(5)->get();
            $recentStockOuts = StockOut::with('product', 'rak')->orderBy('date_time', 'desc')->limit(5)->get();

            // Logika Persiapan Data Grafik (6 Bulan Terakhir)
            $sixMonthsAgo = now()->subMonths(5)->startOfMonth();

            $rawStockIns = StockIn::where('date_time', '>=', $sixMonthsAgo)
                ->selectRaw('DATE_FORMAT(date_time, "%Y-%m") as monthKey, SUM(quantity) as total')
                ->groupBy('monthKey')
                ->pluck('total', 'monthKey');

            $rawStockOuts = StockOut::where('date_time', '>=', $sixMonthsAgo)
                ->selectRaw('DATE_FORMAT(date_time, "%Y-%m") as monthKey, SUM(quantity) as total')
                ->groupBy('monthKey')
                ->pluck('total', 'monthKey');

            $chartLabels = [];
            $chartDataIn = [];
            $chartDataOut = [];

            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $key = $date->format('Y-m');
                
                $chartLabels[] = $date->format('M Y');
                $chartDataIn[] = $rawStockIns[$key] ?? 0;
                $chartDataOut[] = $rawStockOuts[$key] ?? 0;
            }

            return view('admin.dashboard', compact(
                'totalProducts', 
                'totalStock', 
                'recentStockIns', 
                'recentStockOuts',
                'chartLabels',
                'chartDataIn',
                'chartDataOut'
            ));
        } else {
            return view('staff.dashboard');
        }
    }
}