<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // 🚀 Ambil data user langsung dari Token API, bukan dari Cookie
        $user = $request->user();

        // Cek apakah tokennya valid DAN role-nya adalah admin
        if ($user && $user->role === 'admin') {
            return $next($request);
        }

        // Jika gagal, tolak dengan JSON yang bersih agar terbaca oleh React
        return response()->json([
            'success' => false,
            'message' => 'Akses ditolak. Anda bukan admin.'
        ], 403);
    }
}
