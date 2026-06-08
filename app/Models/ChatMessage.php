<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $table = 'chat_messages';

    protected $fillable = [
        'chat_session_id',
        'sender_type',
        'message',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    /**
     * Get the session that owns the message.
     */
    public function session()
    {
        return $this->belongsTo(ChatSession::class, 'chat_session_id');
    }
}
