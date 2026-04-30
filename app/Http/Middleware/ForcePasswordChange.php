<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Ambil data user yang sedang login
        $user = Auth::user();

        // 2. Cek apakah user sudah login dan apakah password_changed_at masih kosong (NULL)
        // Ini mendeteksi login pertama kali bagi karyawan
        if ($user && is_null($user->password_changed_at)) {
            
            /**
             * 3. Pengecualian Penting: 
             * Izinkan request jika user sedang memproses update password pertama,
             * kirim ulang OTP, atau sedang melakukan logout.
             */
            if ($request->is('logout') || 
                $request->routeIs('password.update.first') || 
                $request->routeIs('otp.resend')) {
                return $next($request);
            }

            /**
             * 4. Tindakan Keamanan:
             * Jika user mencoba akses halaman dashboard/pdm tanpa ganti password,
             * kita paksa logout dan bersihkan session agar token CSRF tidak mismatch.
             */
            Auth::logout();
            
            // Hancurkan session agar benar-benar bersih dari error "Sesi Kadaluwarsa"
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // 5. Lempar kembali ke login dengan pesan peringatan
            return redirect()->route('login')->with('error', 'Anda harus memverifikasi OTP dan mengganti password pada login pertama.');
        }

        return $next($request);
    }
}