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

        // Generate Token untuk API React
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,  // 🚀 INI WAJIB ADA AGAR REACT BISA MENYIMPAN TOKEN
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

        // Hapus juga sesi web bawaan Laravel agar bersih 100%
        Auth::guard('web')->logout();

        return response()->json([
            'success' => true, // 🚀 INI JUGA WAJIB ADA
            'message' => 'Logout berhasil'
        ], 200);
    }
}
