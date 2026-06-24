<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, CanResetPassword;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Override: arahkan link reset password ke URL frontend React,
     * bukan ke URL backend Laravel.
     */
    public function sendPasswordResetNotification($token): void
    {
        $frontendUrl = env('FRONTEND_URL', 'http://127.0.0.1:5173');
        $url = $frontendUrl . '/reset-password/' . $token . '?email=' . urlencode($this->email);

        $this->notify(new ResetPasswordNotification($token, function ($notifiable) use ($url) {
            return $url;
        }));
    }
}

