<?php

namespace App\Http\Controllers;

use App\Models\Rak;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class RakController extends Controller
{
    public function index()
    {
        $raks = Rak::withCount(['stocks', 'stockIns', 'stockOuts'])->get();
        return view('admin.raks.index', compact('raks'));
    }

    public function create()
    {
        return view('admin.raks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_rak' => 'required|string',
            'tingkat'   => 'required|string',
            'bagian'    => 'required|string',
            'name'      => 'nullable|string',
            'capacity'  => 'required|integer|min:1'
        ]);

        Rak::create([
            'nomor_rak' => $request->nomor_rak,
            'tingkat'   => $request->tingkat,
            'bagian'    => $request->bagian,
            'name'      => $request->name ?? $request->nomor_rak, 
            'capacity'  => $request->capacity,
        ]);

        return redirect()->route('raks.index')->with('success', 'Rak berhasil ditambahkan.');
    }

    public function edit(Rak $rak)
    {
        return view('admin.raks.edit', compact('rak'));
    }

    public function update(Request $request, Rak $rak)
    {
        $request->validate([
            'nomor_rak' => 'required|string',
            'tingkat'   => 'required|string',
            'bagian'    => 'required|string',
            'name'      => 'nullable|string',
            'capacity'  => 'required|integer|min:1'
        ]);

        $rak->update([
            'nomor_rak' => $request->nomor_rak,
            'tingkat'   => $request->tingkat,
            'bagian'    => $request->bagian,
            'name'      => $request->name ?? $request->nomor_rak,
            'capacity'  => $request->capacity,
        ]);

        return redirect()->route('raks.index')->with('success', 'Rak berhasil diupdate.');
    }

    public function destroy(Rak $rak)
    {
        $rak->loadCount(['stocks', 'stockIns', 'stockOuts']);

        $dependencies = collect([
            $rak->stocks_count ? "{$rak->stocks_count} stok aktif" : null,
            $rak->stock_ins_count ? "{$rak->stock_ins_count} transaksi barang masuk" : null,
            $rak->stock_outs_count ? "{$rak->stock_outs_count} transaksi barang keluar" : null,
        ])->filter();

        if ($dependencies->isNotEmpty()) {
            return redirect()
                ->route('raks.index')
                ->with('error', 'Rak tidak bisa dihapus karena masih digunakan pada '.$dependencies->join(', ', ' dan ').'.');
        }

        try {
            $rak->delete();
        } catch (QueryException $exception) {
            return redirect()
                ->route('raks.index')
                ->with('error', 'Rak tidak bisa dihapus karena masih terhubung dengan data lain.');
        }

        return redirect()->route('raks.index')->with('success', 'Rak dihapus.');
    }
}
