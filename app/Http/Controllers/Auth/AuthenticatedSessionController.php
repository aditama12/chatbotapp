<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // 👈 Jangan lupa import model User

class AuthenticatedSessionController extends Controller
{
    public function store(LoginRequest $request): JsonResponse
    {
        // 1. Validasi password & email
        $request->authenticate();

        // 2. Deklarasikan secara eksplisit kalau $user ini adalah model App\Models\User
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 3. Generate token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'user' => $user,
            'token' => $token
        ], 200);
    }

    public function destroy(Request $request): JsonResponse
    {
        /** @var \App\Models\User|null $user */
        $user = $request->user();

        // Cek apakah user ada dan sedang punya token aktif
        if ($user && $user->currentAccessToken()) {
            // Hapus token berdasarkan ID token tersebut menggunakan relasi tokens()
            $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
        }

        return response()->json([
            'message' => 'Logout berhasil'
        ], 200);
    }
}
