<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount(['stockIns', 'stockOuts'])->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,staff',
        ]);
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);
        return redirect()->route('users.index')->with('success', 'User berhasil dibuat.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,staff',
        ]);
        $user->update($request->only('name', 'email', 'role'));
        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }
        return redirect()->route('users.index')->with('success', 'User berhasil diupdate.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'User yang sedang login tidak bisa dihapus.');
        }

        $user->loadCount(['stockIns', 'stockOuts']);

        $dependencies = collect([
            $user->stock_ins_count ? "{$user->stock_ins_count} transaksi barang masuk" : null,
            $user->stock_outs_count ? "{$user->stock_outs_count} transaksi barang keluar" : null,
        ])->filter();

        if ($dependencies->isNotEmpty()) {
            return redirect()
                ->route('users.index')
                ->with('error', 'User tidak bisa dihapus karena masih tercatat pada '.$dependencies->join(', ', ' dan ').'.');
        }

        try {
            $user->delete();
        } catch (QueryException $exception) {
            return redirect()
                ->route('users.index')
                ->with('error', 'User tidak bisa dihapus karena masih terhubung dengan data lain.');
        }

        return redirect()->route('users.index')->with('success', 'User dihapus.');
    }
}
