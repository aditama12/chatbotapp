<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\AdminChatController;
use App\Http\Controllers\PasswordResetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);
Route::post('/password/reset-link', [PasswordResetController::class, 'sendResetLinkEmail']);
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword']);

Route::post('/chatbot/send', [ChatbotController::class, 'send']);
Route::post('/chatbot/escalated/{chatId}/follow-up', [AdminChatController::class, 'addFollowUpMessage']);

// 👇 INI DIA RUTE YANG DITAMBAHKAN BUB 👇
// Rute ini wajib ada buat ngasih data profil (nama, email) ke React
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Admin routes
Route::prefix('mimin')->group(function () {
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);

    // WAJIB BUNGKUS PAKAI auth:sanctum BIAR AUTH::USER() NGGAK NULL
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/chats/escalated', [AdminChatController::class, 'getEscalatedChats']);
        Route::get('/chats/{chatId}', [AdminChatController::class, 'getChatDetail']);
        Route::post('/chats/{chatId}/reply', [AdminChatController::class, 'sendAdminReply']);
        Route::post('/chats/{chatId}/resolve', [AdminChatController::class, 'resolveChat']);
        Route::get('/user/{userId}/chats', [AdminChatController::class, 'getUserChats']);
        Route::get('/dashboard', [AdminChatController::class, 'getDashboardStats']);
        Route::get('/history', [AdminChatController::class, 'getHistory']);
    });
});