<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminReply extends Model
{
    protected $fillable = [
        'chat_id',
        'admin_id',
        'message'
    ];

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
