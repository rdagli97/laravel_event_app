<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillabe = [
        'conversation_id',
        'sender_id',
        'receiver_id',
        'body',
    ];

    // relations

    public function covnersation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }
}
