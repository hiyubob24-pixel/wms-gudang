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
            $totalRacks = \App\Models\Rak::count();
            
            $lowStockCount = \Illuminate\Support\Facades\DB::table('stocks')
                ->selectRaw('product_id, SUM(quantity) as total')
                ->groupBy('product_id')
                ->having('total', '<', 10)
                ->get()
                ->count();

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
                'totalRacks',
                'lowStockCount', 
                'recentStockIns', 
                'recentStockOuts',
                'chartLabels',
                'chartDataIn',
                'chartDataOut'
            ));
        } else {
            $today = now();
            $todayDate = $today->toDateString();
            $weekStart = $today->copy()->startOfWeek();
            $sevenDaysAgo = $today->copy()->subDays(6)->startOfDay();

            $todayStockInCount = StockIn::where('created_by', $user->id)
                ->whereDate('date_time', $todayDate)
                ->count();

            $todayStockOutCount = StockOut::where('created_by', $user->id)
                ->whereDate('date_time', $todayDate)
                ->count();

            $weekQtyIn = StockIn::where('created_by', $user->id)
                ->where('date_time', '>=', $weekStart)
                ->sum('quantity');

            $weekQtyOut = StockOut::where('created_by', $user->id)
                ->where('date_time', '>=', $weekStart)
                ->sum('quantity');

            $productsHandledThisWeek = StockIn::where('created_by', $user->id)
                ->where('date_time', '>=', $weekStart)
                ->pluck('product_id')
                ->merge(
                    StockOut::where('created_by', $user->id)
                        ->where('date_time', '>=', $weekStart)
                        ->pluck('product_id')
                )
                ->unique()
                ->count();

            $activeRaksToday = StockIn::where('created_by', $user->id)
                ->whereDate('date_time', $todayDate)
                ->pluck('rak_id')
                ->merge(
                    StockOut::where('created_by', $user->id)
                        ->whereDate('date_time', $todayDate)
                        ->pluck('rak_id')
                )
                ->unique()
                ->count();

            $rawStaffStockIns = StockIn::where('created_by', $user->id)
                ->where('date_time', '>=', $sevenDaysAgo)
                ->selectRaw('DATE(date_time) as day_key, SUM(quantity) as total')
                ->groupBy('day_key')
                ->pluck('total', 'day_key');

            $rawStaffStockOuts = StockOut::where('created_by', $user->id)
                ->where('date_time', '>=', $sevenDaysAgo)
                ->selectRaw('DATE(date_time) as day_key, SUM(quantity) as total')
                ->groupBy('day_key')
                ->pluck('total', 'day_key');

            $staffChartLabels = [];
            $staffChartDataIn = [];
            $staffChartDataOut = [];

            for ($i = 6; $i >= 0; $i--) {
                $date = $today->copy()->subDays($i);
                $key = $date->toDateString();

                $staffChartLabels[] = $date->format('d M');
                $staffChartDataIn[] = $rawStaffStockIns[$key] ?? 0;
                $staffChartDataOut[] = $rawStaffStockOuts[$key] ?? 0;
            }

            $recentStaffIns = StockIn::with(['product', 'rak'])
                ->where('created_by', $user->id)
                ->orderBy('date_time', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    return [
                        'type' => 'in',
                        'label' => 'Barang Masuk',
                        'product_name' => $item->product->name ?? 'Produk Arsip/Dihapus',
                        'rak_name' => trim(($item->rak->nomor_rak ?? '-') . ' - ' . ($item->rak->tingkat ?? '-') . ' (' . ($item->rak->bagian ?? '-') . ')'),
                        'date_time' => $item->date_time,
                        'quantity' => $item->quantity,
                    ];
                });

            $recentStaffOuts = StockOut::with(['product', 'rak'])
                ->where('created_by', $user->id)
                ->orderBy('date_time', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    return [
                        'type' => 'out',
                        'label' => 'Barang Keluar',
                        'product_name' => $item->product->name ?? 'Produk Arsip/Dihapus',
                        'rak_name' => trim(($item->rak->nomor_rak ?? '-') . ' - ' . ($item->rak->tingkat ?? '-') . ' (' . ($item->rak->bagian ?? '-') . ')'),
                        'date_time' => $item->date_time,
                        'quantity' => $item->quantity,
                    ];
                });

            $recentActivities = $recentStaffIns
                ->merge($recentStaffOuts)
                ->sortByDesc('date_time')
                ->take(6)
                ->values();

            return view('staff.dashboard', compact(
                'todayStockInCount',
                'todayStockOutCount',
                'weekQtyIn',
                'weekQtyOut',
                'productsHandledThisWeek',
                'activeRaksToday',
                'staffChartLabels',
                'staffChartDataIn',
                'staffChartDataOut',
                'recentActivities'
            ));
        }
    }
}
