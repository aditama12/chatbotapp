<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    public function store(LoginRequest $request): JsonResponse
    {
        $request->authenticate();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 🚀 VALIDASI SUPER KETAT: Jika ini request dari halaman Admin, pastikan akunnya benar-benar Admin!
        if ($request->has('role') && $request->role === 'admin') {
            if ($user->role !== 'admin') {
                Auth::guard('web')->logout(); // Batalkan sesi
                return response()->json([
                    'success' => false,
                    'message' => 'Akses Ditolak! Akun email ini terdaftar sebagai User biasa, bukan Admin.'
                ], 403);
            }
        }

        // Generate Token API
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true, // 👈 INI WAJIB TRUE AGAR REACT MENGENALI LOGIN BERHASIL
            'message' => 'Login berhasil',
            'user' => $user,
            'token' => $token
        ], 200);
    }

    public function destroy(Request $request): JsonResponse
    {
        /** @var \App\Models\User|null $user */
        $user = $request->user();

        if ($user && $user->currentAccessToken()) {
            $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
        }

        Auth::guard('web')->logout();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ], 200);
    }
}
