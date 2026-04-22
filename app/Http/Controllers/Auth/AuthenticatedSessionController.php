<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $firstName = explode(' ', trim((string) $request->user()->name))[0] ?: 'Tim';

        return redirect()
            ->intended(route('dashboard', absolute: false))
            ->with('success', 'Selamat datang kembali, '.$firstName.'.');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $firstName = explode(' ', trim((string) optional($request->user())->name))[0] ?: null;

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('info', $firstName ? 'Sampai jumpa, '.$firstName.'. Anda berhasil logout.' : 'Anda berhasil logout.');
    }
}
