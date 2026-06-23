<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
public function store(LoginRequest $request)
    {
        $request->authenticate();

        // Ambil data user yang berhasil login
        $user = $request->user();

        // Buat Bearer Token baru
        $token = $user->createToken('admin-token')->plainTextToken;

        // Kembalikan token ke React dalam bentuk JSON
        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'token' => $token, // 👈 Ini yang ditangkap oleh response.data.token
            'user' => $user
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
