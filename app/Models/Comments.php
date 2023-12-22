<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comments extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'owner_id',
        'body',
        'rate',
    ];

    // relations

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
