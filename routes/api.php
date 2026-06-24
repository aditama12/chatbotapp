<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\AdminChatController;
use App\Http\Controllers\PasswordResetController;
use Illuminate\Http\Request;

// Rute Publik
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/password/reset-link', [PasswordResetController::class, 'sendResetLinkEmail']);
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword']);
// Polling balasan admin — publik, chatId cukup sebagai identifier
Route::get('/chatbot/escalated/{chatId}/status', [AdminChatController::class, 'getStatusForUser']);

// DEBUG: Test kirim email (hapus setelah masalah email teratasi)
Route::get('/test-mail', function () {
    try {
        \Illuminate\Support\Facades\Mail::raw('Test email dari Railway - ' . now(), function ($msg) {
            $msg->to(config('mail.from.address'))
                ->subject('Test SMTP Railway ' . now()->format('H:i:s'));
        });
        return response()->json([
            'success' => true,
            'message' => 'Email berhasil dikirim!',
            'mail_config' => [
                'mailer'     => config('mail.default'),
                'host'       => config('mail.mailers.smtp.host'),
                'port'       => config('mail.mailers.smtp.port'),
                'username'   => config('mail.mailers.smtp.username'),
                'encryption' => config('mail.mailers.smtp.encryption'),
                'from'       => config('mail.from.address'),
                'queue'      => config('queue.default'),
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error'   => $e->getMessage(),
            'mail_config' => [
                'mailer'     => config('mail.default'),
                'host'       => config('mail.mailers.smtp.host'),
                'port'       => config('mail.mailers.smtp.port'),
                'username'   => config('mail.mailers.smtp.username'),
                'encryption' => config('mail.mailers.smtp.encryption'),
                'from'       => config('mail.from.address'),
                'queue'      => config('queue.default'),
            ]
        ], 500);
    }
});


// Rute User yang Diamankan (Wajib bawa token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // 🚀 Rute Chat User yang Benar
    Route::post('/chatbot/send', [ChatbotController::class, 'send']);
    Route::post('/chatbot/escalated/{chatId}/follow-up', [AdminChatController::class, 'addFollowUpMessage']);
});

// Admin routes
Route::prefix('mimin')->group(function () {
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    // 🚀 Rute Admin yang Benar
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
