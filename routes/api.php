<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\AdminChatController;
use App\Http\Controllers\PasswordResetController;
use Illuminate\Http\Request;


// Rute Publik (Tidak butuh token)
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/password/reset-link', [PasswordResetController::class, 'sendResetLinkEmail']);
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword']);

// Rute Chatbot Publik
Route::middleware(['api'])->group(function () {
    Route::post('/chatbot/send', [ChatbotController::class, 'send']);
    Route::post('/chatbot/escalated/{chatId}/follow-up', [AdminChatController::class, 'addFollowUpMessage']);
});

// Rute User yang Diamankan (Wajib bawa token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    // ❌ Rute chatbot duplikat Dihapus dari sini
});

// Admin routes
Route::prefix('mimin')->group(function () {
    // Login admin (Publik)
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    // 🚀 KEMBALIKAN ADMIN MIDDLEWARE DI SINI AGAR DATA BISA DIBACA!
    Route::middleware(['auth:sanctum', \App\Http\Middleware\AdminMiddleware::class])->group(function () {
        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);
        Route::get('/chats/escalated', [AdminChatController::class, 'getEscalatedChats']);
        Route::get('/chats/{chatId}', [AdminChatController::class, 'getChatDetail']);
        Route::post('/chats/{chatId}/reply', [AdminChatController::class, 'sendAdminReply']);
        Route::post('/chats/{chatId}/resolve', [AdminChatController::class, 'resolveChat']);
        Route::get('/user/{userId}/chats', [AdminChatController::class, 'getUserChats']);
        Route::get('/dashboard', [AdminChatController::class, 'getDashboardStats']);
        Route::get('/history', [AdminChatController::class, 'getHistory']);
    });
});
