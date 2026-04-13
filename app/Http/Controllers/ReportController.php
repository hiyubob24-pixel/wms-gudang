<?php

namespace App\Http\Controllers;

use App\Models\StockIn;
use App\Models\StockOut;

class ReportController extends Controller
{
    public function index()
    {
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
        $tableReports = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $key = $date->format('Y-m');
            $label = $date->format('M Y');
            
            $in = $rawStockIns[$key] ?? 0;
            $out = $rawStockOuts[$key] ?? 0;
            
            $chartLabels[] = $label;
            $chartDataIn[] = $in;
            $chartDataOut[] = $out;

            // Membungkus data untuk ditampilkan ke dalam bentuk tabel HTML di view reports.blade.php
            $tableReports[] = [
                'month' => $label,
                'in' => $in,
                'out' => $out,
                'net' => $in - $out
            ];
        }

        return view('admin.reports', compact('chartLabels', 'chartDataIn', 'chartDataOut', 'tableReports'));
    }
}