<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword; 
use Illuminate\Notifications\Messages\MailMessage; 

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Kita ubah variabelnya jadi $user biar lebih akrab sama Laravel-nya
        ResetPassword::toMailUsing(function ($user, string $token) {
            
            // 👉 PAKE CARA PALING AMAN BUAT NGAMBIL EMAIL
            $email = $user->getEmailForPasswordReset();
            
            $frontendUrl = env('FRONTEND_URL', 'http://127.0.0.1:5173');
            $url = $frontendUrl . '/reset-password/' . $token . '?email=' . urlencode($email);

            return (new MailMessage)
                ->subject('Reset Password Akun SAKTI')
                // 👉 Sapaan diubah biar aman dari error 'name not found'
                ->greeting('Halo, Sahabat SAKTI!') 
                ->line('Kami menerima permintaan untuk mereset password akun SAKTI kamu nih.')
                ->action('Reset Password Sekarang', $url)
                ->line('Tautan reset password ini hanya berlaku selama 60 menit.')
                ->line('Kalau kamu ngga merasa pernah meminta reset password, abaikan saja email ini ya.')
                ->salutation('Salam hangat, Tim SAKTI');
        });
    }
}