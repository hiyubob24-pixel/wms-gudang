<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Rak;
use App\Models\Stock;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\User;
use Illuminate\Support\Carbon;

class WmsAiContextService
{
    public function buildSnapshot(?string $pageContext = null): array
    {
        $now = now();
        $today = $now->toDateString();
        $weekStart = $now->copy()->startOfWeek()->startOfDay();
        $sevenDaysAgo = $now->copy()->subDays(6)->startOfDay();
        $thirtyDaysAgo = $now->copy()->subDays(30)->startOfDay();
        $monthStart = $now->copy()->startOfMonth();
        $movementRelations = [
            'product:id,name,sku,deleted_at',
            'rak:id,name,nomor_rak,tingkat,bagian,capacity',
        ];

        $products = Product::withTrashed()
            ->withSum('stocks as current_stock_total', 'quantity')
            ->orderBy('name')
            ->get(['id', 'name', 'sku', 'deleted_at']);

        $stocks = Stock::with($movementRelations)
            ->where('quantity', '>', 0)
            ->orderByDesc('quantity')
            ->get();

        $raks = Rak::withSum('stocks as current_occupancy', 'quantity')
            ->orderBy('name')
            ->get(['id', 'name', 'nomor_rak', 'tingkat', 'bagian', 'capacity']);

        $recentIncoming = StockIn::with($movementRelations)
            ->orderByDesc('date_time')
            ->limit(8)
            ->get();

        $recentOutgoing = StockOut::with($movementRelations)
            ->orderByDesc('date_time')
            ->limit(8)
            ->get();

        $todayIncoming = StockIn::with($movementRelations)
            ->whereDate('date_time', $today)
            ->orderByDesc('date_time')
            ->limit(10)
            ->get();

        $todayOutgoing = StockOut::with($movementRelations)
            ->whereDate('date_time', $today)
            ->orderByDesc('date_time')
            ->limit(10)
            ->get();

        $productCatalog = $products
            ->map(function (Product $product) {
                $currentStock = (int) round($product->current_stock_total ?? 0);

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'current_stock' => $currentStock,
                    'archived' => $product->deleted_at !== null,
                ];
            })
            ->values();

        $stockPositions = $stocks
            ->map(function (Stock $stock) {
                return [
                    'product_id' => $stock->product_id,
                    'product_name' => $stock->product->name ?? 'Produk Arsip/Dihapus',
                    'sku' => $stock->product->sku ?? null,
                    'quantity' => (int) $stock->quantity,
                    'rack_id' => $stock->rak_id,
                    'rack_label' => $this->formatRackLabel($stock->rak),
                    'rack_name' => $stock->rak->name ?? null,
                    'zone' => $stock->rak->bagian ?? null,
                ];
            })
            ->values();

        $rackCatalog = $raks
            ->map(function (Rak $rak) {
                $capacity = (int) ($rak->capacity ?? 0);
                $occupancy = (int) round($rak->current_occupancy ?? 0);
                $utilization = $capacity > 0
                    ? round(($occupancy / max($capacity, 1)) * 100, 1)
                    : null;

                return [
                    'id' => $rak->id,
                    'label' => $this->formatRackLabel($rak),
                    'name' => $rak->name,
                    'capacity' => $capacity,
                    'occupancy' => $occupancy,
                    'available_space' => $capacity > 0 ? max($capacity - $occupancy, 0) : null,
                    'utilization_percent' => $utilization,
                ];
            })
            ->values();

        $recentMovements = $recentIncoming
            ->map(fn (StockIn $item) => $this->formatMovement($item, 'Barang Masuk', 'in'))
            ->merge($recentOutgoing->map(fn (StockOut $item) => $this->formatMovement($item, 'Barang Keluar', 'out')))
            ->sortByDesc('date_time')
            ->take(12)
            ->values()
            ->map(function (array $movement) {
                $movement['date_time'] = Carbon::parse($movement['date_time'])->toIso8601String();

                return $movement;
            });

        $recentIncomingMovements = $recentIncoming
            ->map(fn (StockIn $item) => $this->formatMovement($item, 'Barang Masuk', 'in'))
            ->values()
            ->map(function (array $movement) {
                $movement['date_time'] = Carbon::parse($movement['date_time'])->toIso8601String();

                return $movement;
            });

        $recentOutgoingMovements = $recentOutgoing
            ->map(fn (StockOut $item) => $this->formatMovement($item, 'Barang Keluar', 'out'))
            ->values()
            ->map(function (array $movement) {
                $movement['date_time'] = Carbon::parse($movement['date_time'])->toIso8601String();

                return $movement;
            });

        $todayIncomingMovements = $todayIncoming
            ->map(fn (StockIn $item) => $this->formatMovement($item, 'Barang Masuk', 'in'))
            ->values()
            ->map(function (array $movement) {
                $movement['date_time'] = Carbon::parse($movement['date_time'])->toIso8601String();

                return $movement;
            });

        $todayOutgoingMovements = $todayOutgoing
            ->map(fn (StockOut $item) => $this->formatMovement($item, 'Barang Keluar', 'out'))
            ->values()
            ->map(function (array $movement) {
                $movement['date_time'] = Carbon::parse($movement['date_time'])->toIso8601String();

                return $movement;
            });

        $movementProductIdsLast30Days = StockIn::where('date_time', '>=', $thirtyDaysAgo)
            ->pluck('product_id')
            ->merge(
                StockOut::where('date_time', '>=', $thirtyDaysAgo)
                    ->pluck('product_id')
            )
            ->unique()
            ->values();

        $topOutboundProducts = StockOut::with('product:id,name,sku,deleted_at')
            ->where('date_time', '>=', $thirtyDaysAgo)
            ->selectRaw('product_id, SUM(quantity) as total_outbound')
            ->groupBy('product_id')
            ->orderByDesc('total_outbound')
            ->limit(10)
            ->get()
            ->map(function (StockOut $stockOut) {
                return [
                    'product_id' => $stockOut->product_id,
                    'product_name' => $stockOut->product->name ?? 'Produk Arsip/Dihapus',
                    'sku' => $stockOut->product->sku ?? null,
                    'quantity_30_days' => (int) round($stockOut->total_outbound ?? 0),
                ];
            })
            ->values();

        $topInboundProductsCurrentMonth = StockIn::with('product:id,name,sku,deleted_at')
            ->where('date_time', '>=', $monthStart)
            ->selectRaw('product_id, SUM(quantity) as total_inbound')
            ->groupBy('product_id')
            ->orderByDesc('total_inbound')
            ->limit(8)
            ->get()
            ->map(function (StockIn $stockIn) {
                return [
                    'product_id' => $stockIn->product_id,
                    'product_name' => $stockIn->product->name ?? 'Produk Arsip/Dihapus',
                    'sku' => $stockIn->product->sku ?? null,
                    'quantity_this_month' => (int) round($stockIn->total_inbound ?? 0),
                ];
            })
            ->values();

        $topInboundProductsCurrentWeek = StockIn::with('product:id,name,sku,deleted_at')
            ->where('date_time', '>=', $weekStart)
            ->selectRaw('product_id, SUM(quantity) as total_inbound')
            ->groupBy('product_id')
            ->orderByDesc('total_inbound')
            ->limit(8)
            ->get()
            ->map(function (StockIn $stockIn) {
                return [
                    'product_id' => $stockIn->product_id,
                    'product_name' => $stockIn->product->name ?? 'Produk Arsip/Dihapus',
                    'sku' => $stockIn->product->sku ?? null,
                    'quantity_this_week' => (int) round($stockIn->total_inbound ?? 0),
                ];
            })
            ->values();

        $topOutboundProductsCurrentMonth = StockOut::with('product:id,name,sku,deleted_at')
            ->where('date_time', '>=', $monthStart)
            ->selectRaw('product_id, SUM(quantity) as total_outbound')
            ->groupBy('product_id')
            ->orderByDesc('total_outbound')
            ->limit(8)
            ->get()
            ->map(function (StockOut $stockOut) {
                return [
                    'product_id' => $stockOut->product_id,
                    'product_name' => $stockOut->product->name ?? 'Produk Arsip/Dihapus',
                    'sku' => $stockOut->product->sku ?? null,
                    'quantity_this_month' => (int) round($stockOut->total_outbound ?? 0),
                ];
            })
            ->values();

        $topOutboundProductsCurrentWeek = StockOut::with('product:id,name,sku,deleted_at')
            ->where('date_time', '>=', $weekStart)
            ->selectRaw('product_id, SUM(quantity) as total_outbound')
            ->groupBy('product_id')
            ->orderByDesc('total_outbound')
            ->limit(8)
            ->get()
            ->map(function (StockOut $stockOut) {
                return [
                    'product_id' => $stockOut->product_id,
                    'product_name' => $stockOut->product->name ?? 'Produk Arsip/Dihapus',
                    'sku' => $stockOut->product->sku ?? null,
                    'quantity_this_week' => (int) round($stockOut->total_outbound ?? 0),
                ];
            })
            ->values();

        $lowStockProducts = $productCatalog
            ->filter(fn (array $product) => ! $product['archived'] && $product['current_stock'] > 0 && $product['current_stock'] <= 10)
            ->sortBy('current_stock')
            ->take(12)
            ->values();

        $outOfStockProducts = $productCatalog
            ->filter(fn (array $product) => ! $product['archived'] && $product['current_stock'] <= 0)
            ->take(12)
            ->values();

        $idleProducts = $productCatalog
            ->filter(function (array $product) use ($movementProductIdsLast30Days) {
                return ! $product['archived']
                    && $product['current_stock'] > 0
                    && ! $movementProductIdsLast30Days->contains($product['id']);
            })
            ->take(12)
            ->values();

        $nearCapacityRaks = $rackCatalog
            ->filter(fn (array $rak) => $rak['utilization_percent'] !== null && $rak['utilization_percent'] >= 80)
            ->sortByDesc('utilization_percent')
            ->take(12)
            ->values();

        $monthlyFlow = collect(range(5, 0, -1))
            ->map(function (int $monthsAgo) use ($now) {
                $start = $now->copy()->subMonths($monthsAgo)->startOfMonth();
                $end = $start->copy()->endOfMonth();

                return [
                    'month' => $start->format('M Y'),
                    'incoming' => (int) StockIn::whereBetween('date_time', [$start, $end])->sum('quantity'),
                    'outgoing' => (int) StockOut::whereBetween('date_time', [$start, $end])->sum('quantity'),
                ];
            })
            ->values()
            ->map(function (array $month) {
                $month['net_flow'] = $month['incoming'] - $month['outgoing'];

                return $month;
            });

        $dailyIncomingLast7Days = StockIn::where('date_time', '>=', $sevenDaysAgo)
            ->selectRaw('DATE(date_time) as day_key, SUM(quantity) as total')
            ->groupBy('day_key')
            ->pluck('total', 'day_key');

        $dailyOutgoingLast7Days = StockOut::where('date_time', '>=', $sevenDaysAgo)
            ->selectRaw('DATE(date_time) as day_key, SUM(quantity) as total')
            ->groupBy('day_key')
            ->pluck('total', 'day_key');

        $dailyFlow7Days = collect(range(6, 0, -1))
            ->map(function (int $daysAgo) use ($now, $dailyIncomingLast7Days, $dailyOutgoingLast7Days) {
                $date = $now->copy()->subDays($daysAgo);
                $key = $date->toDateString();
                $incoming = (int) ($dailyIncomingLast7Days[$key] ?? 0);
                $outgoing = (int) ($dailyOutgoingLast7Days[$key] ?? 0);

                return [
                    'date' => $key,
                    'label' => $date->format('d M'),
                    'incoming' => $incoming,
                    'outgoing' => $outgoing,
                    'net_flow' => $incoming - $outgoing,
                ];
            })
            ->values();

        $todaySummary = [
            'date' => $today,
            'incoming_transactions' => $todayIncoming->count(),
            'incoming_quantity' => (int) $todayIncoming->sum('quantity'),
            'outgoing_transactions' => $todayOutgoing->count(),
            'outgoing_quantity' => (int) $todayOutgoing->sum('quantity'),
            'incoming_movements' => $todayIncomingMovements->all(),
            'outgoing_movements' => $todayOutgoingMovements->all(),
        ];

        $currentWeekSummary = [
            'label' => $weekStart->format('d M') . ' - ' . $now->format('d M Y'),
            'incoming_transactions' => StockIn::where('date_time', '>=', $weekStart)->count(),
            'incoming_quantity' => (int) StockIn::where('date_time', '>=', $weekStart)->sum('quantity'),
            'outgoing_transactions' => StockOut::where('date_time', '>=', $weekStart)->count(),
            'outgoing_quantity' => (int) StockOut::where('date_time', '>=', $weekStart)->sum('quantity'),
            'top_incoming_products' => $topInboundProductsCurrentWeek->all(),
            'top_outgoing_products' => $topOutboundProductsCurrentWeek->all(),
        ];
        $currentWeekSummary['net_flow'] = $currentWeekSummary['incoming_quantity'] - $currentWeekSummary['outgoing_quantity'];

        $currentMonthSummary = [
            'label' => $now->format('M Y'),
            'incoming_transactions' => StockIn::where('date_time', '>=', $monthStart)->count(),
            'incoming_quantity' => (int) StockIn::where('date_time', '>=', $monthStart)->sum('quantity'),
            'outgoing_transactions' => StockOut::where('date_time', '>=', $monthStart)->count(),
            'outgoing_quantity' => (int) StockOut::where('date_time', '>=', $monthStart)->sum('quantity'),
            'top_incoming_products' => $topInboundProductsCurrentMonth->all(),
            'top_outgoing_products' => $topOutboundProductsCurrentMonth->all(),
        ];
        $currentMonthSummary['net_flow'] = $currentMonthSummary['incoming_quantity'] - $currentMonthSummary['outgoing_quantity'];

        $summary = [
            'page_context' => $pageContext,
            'generated_at' => $now->toIso8601String(),
            'total_products' => $productCatalog->where('archived', false)->count(),
            'archived_products' => $productCatalog->where('archived', true)->count(),
            'total_stock_quantity' => (int) $stocks->sum('quantity'),
            'active_stock_positions' => $stockPositions->count(),
            'total_racks' => $rackCatalog->count(),
            'racks_with_stock' => $rackCatalog->filter(fn (array $rak) => $rak['occupancy'] > 0)->count(),
            'total_admins' => User::where('role', 'admin')->count(),
            'total_staff' => User::where('role', 'staff')->count(),
            'today_transactions_in' => StockIn::whereDate('date_time', $today)->count(),
            'today_transactions_out' => StockOut::whereDate('date_time', $today)->count(),
            'today_qty_in' => $todaySummary['incoming_quantity'],
            'today_qty_out' => $todaySummary['outgoing_quantity'],
            'this_week_qty_in' => $currentWeekSummary['incoming_quantity'],
            'this_week_qty_out' => $currentWeekSummary['outgoing_quantity'],
            'last_7_days_qty_in' => (int) StockIn::where('date_time', '>=', $sevenDaysAgo)->sum('quantity'),
            'last_7_days_qty_out' => (int) StockOut::where('date_time', '>=', $sevenDaysAgo)->sum('quantity'),
            'this_month_qty_in' => $currentMonthSummary['incoming_quantity'],
            'this_month_qty_out' => $currentMonthSummary['outgoing_quantity'],
            'low_stock_count' => $lowStockProducts->count(),
            'out_of_stock_count' => $outOfStockProducts->count(),
            'near_capacity_rack_count' => $nearCapacityRaks->count(),
        ];

        return [
            'summary' => $summary,
            'alerts' => [
                'low_stock_products' => $lowStockProducts->all(),
                'out_of_stock_products' => $outOfStockProducts->all(),
                'near_capacity_racks' => $nearCapacityRaks->all(),
                'idle_products_30_days' => $idleProducts->all(),
            ],
            'products' => $productCatalog->all(),
            'stock_positions' => $stockPositions->all(),
            'racks' => $rackCatalog->all(),
            'today' => $todaySummary,
            'current_week' => $currentWeekSummary,
            'current_month' => $currentMonthSummary,
            'daily_flow_7_days' => $dailyFlow7Days->all(),
            'recent_movements' => $recentMovements->all(),
            'recent_incoming' => $recentIncomingMovements->all(),
            'recent_outgoing' => $recentOutgoingMovements->all(),
            'top_outbound_products_30_days' => $topOutboundProducts->all(),
            'monthly_flow_6_months' => $monthlyFlow->all(),
        ];
    }

    public function buildPromptContext(?string $pageContext = null): string
    {
        return json_encode(
            $this->buildSnapshot($pageContext),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        ) ?: '{}';
    }

    protected function formatMovement(StockIn|StockOut $movement, string $label, string $direction): array
    {
        return [
            'type' => $direction,
            'label' => $label,
            'product_name' => $movement->product->name ?? 'Produk Arsip/Dihapus',
            'sku' => $movement->product->sku ?? null,
            'quantity' => (int) $movement->quantity,
            'rack_label' => $this->formatRackLabel($movement->rak),
            'date_time' => $movement->date_time,
        ];
    }

    protected function formatRackLabel(?Rak $rak): string
    {
        if (! $rak) {
            return 'Rak tidak tersedia';
        }

        $identity = collect([$rak->nomor_rak, $rak->tingkat, $rak->bagian])
            ->filter(fn (?string $value) => filled($value))
            ->implode(' - ');

        if (blank($identity)) {
            return $rak->name ?: 'Rak tanpa nama';
        }

        return filled($rak->name)
            ? "{$identity} ({$rak->name})"
            : $identity;
    }
}
