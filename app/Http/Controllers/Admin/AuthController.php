<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminLoginRequest;
use App\Models\LogAktivitas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('admin.auth.login');
    }

    public function login(AdminLoginRequest $request): RedirectResponse
    {
        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            return back()->withInput($request->only('email'))->withErrors([
                'email' => 'Email atau password salah.',
            ]);
        }

        if ($request->user()->role !== 'admin') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withInput($request->only('email'))->withErrors([
                'email' => 'Akun tidak memiliki akses admin.',
            ]);
        }

        $request->session()->regenerate();

        LogAktivitas::create([
            'user_id' => $request->user()->id,
            'aktivitas' => 'Admin Login',
            'deskripsi' => 'Admin berhasil masuk ke sistem.',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
