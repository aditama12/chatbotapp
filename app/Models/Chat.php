<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = [
        'user_id',
        'message',
        'reply',
        'admin_status',
        'admin_id',
        'escalated_at',
        'resolved_at',
        'escalation_reason',
        'user_follow_up'
    ];

    protected $casts = [
        'escalated_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    // relasi ke user
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    // relasi ke admin yang merespon
    public function admin()
    {
        return $this->belongsTo(\App\Models\User::class, 'admin_id');
    }

    // relasi ke admin replies
    public function adminReplies()
    {
        return $this->hasMany(\App\Models\AdminReply::class, 'chat_id');
    }

    // scope untuk chat yang sudah di-escalate
    public function scopeEscalated($query)
    {
        return $query->whereNotNull('escalated_at');
    }

    // scope untuk chat pending admin
    public function scopePendingAdmin($query)
    {
        return $query->where('admin_status', 'pending');
    }
}