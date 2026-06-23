<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Cek apakah token ada dan role adalah admin
        if ($user && $user->role === 'admin') {
            return $next($request);
        }

        // Jika gagal, beritahu React apa role aslinya agar mudah dilacak
        $roleSaatIni = $user ? $user->role : 'Token Tidak Valid';

        return response()->json([
            'success' => false,
            'message' => 'Akses ditolak oleh sistem. Role akun Anda saat ini adalah: ' . $roleSaatIni
        ], 403);
    }
}
