<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'surname',
        'username',
        'email',
        'image',
        'phone',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // relations

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'sender_id', 'id');
    }

    public function sentFriendRequest(): HasMany
    {
        return $this->hasMany(FriendRequest::class, 'sender_id', 'id');
    }

    public function receivedFriendRequest(): HasMany
    {
        return $this->hasMany(FriendRequest::class, 'receiver_id', 'id');
    }

    public function friends(): HasMany
    {
        return $this->hasMany(Friend::class, 'user_id', 'id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comments::class);
    }

    public function sentEventRequests(): HasMany
    {
        return $this->hasMany(EventRequest::class, 'sender_id', 'id');
    }

    public function receivedEventRequests(): HasMany
    {
        return $this->hasMany(EventRequest::class, 'receiver_id', 'id');
    }

    public function getAverageRating()
    {
        $comments = $this->comments;

        if ($comments->isEmpty() || $comments->whereNull('rate')->count === $comments->count()) {
            return null;
        }

        $ratings = $comments->pluck('rate')->filter();

        $averageRating = $ratings->average();

        return $averageRating;
    }

    public function joinedEvents(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'members', 'user_id', 'event_id');
    }
}
