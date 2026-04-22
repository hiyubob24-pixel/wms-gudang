<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Rak;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $q = $request->query('q', '');
        $results = [];

        if (blank($q)) {
            return response()->json([]);
        }

        $user = $request->user();
        $isAdmin = $user && $user->role === 'admin';

        if ($isAdmin) {
            // Cari Produk
            $products = Product::where('name', 'like', "%{$q}%")
                ->orWhere('sku', 'like', "%{$q}%")
                ->take(5)
                ->get();
                
            foreach ($products as $p) {
                $results[] = [
                    'name' => $p->name,
                    'description' => "SKU: {$p->sku}",
                    'route' => route('products.index', ['search' => $p->name]),
                    'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                    'type' => 'Produk'
                ];
            }

            // Cari Rak
            $raks = Rak::where('name', 'like', "%{$q}%")
                ->orWhere('nomor_rak', 'like', "%{$q}%")
                ->take(4)
                ->get();
                
            foreach ($raks as $r) {
                $results[] = [
                    'name' => $r->name,
                    'description' => "Rak {$r->nomor_rak} • Tingkat {$r->tingkat} • Bagian {$r->bagian} (Sisa {$r->available_space})",
                    'route' => route('raks.index', ['search' => $r->name]),
                    'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z',
                    'type' => 'Rak'
                ];
            }
        }

        return response()->json($results);
    }
}
