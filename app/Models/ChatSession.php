<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatSession extends Model
{
    use HasFactory;

    protected $table = 'chat_sessions';

    protected $fillable = [
        'session_token',
        'user_id',
        'status',
        'is_bot_enabled',
    ];

    /**
     * Get the messages for this chat session.
     */
    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'chat_session_id')->orderBy('created_at', 'asc');
    }

    /**
     * Get the user who owns this chat session (if logged in).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
