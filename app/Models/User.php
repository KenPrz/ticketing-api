<?php

namespace App\Models;

use App\Enums\{
    FriendStatus,
    UserTypes,
};
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
        'mobile',
        'recent_longitude',
        'recent_latitude',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'user_type' => UserTypes::class,
        ];
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'avatar_url',
    ];

    /**
     * Get the avatar URL attribute.
     *
     * @return string|null
     */
    public function getAvatarUrlAttribute()
    {
        // If it's null, return null
        if (empty($this->attributes['avatar'])) {
            return null;
        }

        // If it's already a URL, return as is
        if (filter_var($this->attributes['avatar'], FILTER_VALIDATE_URL) || str_starts_with($this->attributes['avatar'], 'http')) {
            return $this->attributes['avatar'];
        }

        // Otherwise, generate URL from the stored path
        return Storage::disk('public')->url($this->attributes['avatar']);
    }

    /**
     * Get the user's bookmarks.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function eventBookmarks()
    {
        return $this->belongsToMany(
            Event::class,
            'event_bookmarks',
            'user_id',
            'event_id',
        );
    }

    /**
     * Get all purchases made by the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'purchased_by');
    }

    /**
     * Get all tickets owned by the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'owner_id');
    }

    /**
     * Get all seats assigned to the user through tickets.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function seats()
    {
        return $this->hasManyThrough(
            Seat::class,
            Ticket::class,
            'owner_id',
            'ticket_id',
            'id',
            'id',
        );
    }

    /**
     * Check if the user is friends with another user
     *
     * @param \App\Models\User $user
     * @return bool
     */
    public function isFriendsWith(User $user): bool
    {
        // Don't check friendship with self
        if ($this->id === $user->id) {
            return false;
        }
    
        // Explicitly check for ACCEPTED status in both directions
        $forwardFriendship = UserFriend::where('user_id', $this->id)
            ->where('friend_id', $user->id)
            ->where('status', FriendStatus::ACCEPTED)
            ->exists();
            
        $reverseFriendship = UserFriend::where('user_id', $user->id)
            ->where('friend_id', $this->id)
            ->where('status', FriendStatus::ACCEPTED)
            ->exists();
            
        return $forwardFriendship || $reverseFriendship;
    }

    /**
     * Check if the user has a pending friend request from another user
     *
     * @param \App\Models\User $user
     * @return bool
     */
    public function hasPendingRequestFrom(User $user): bool
    {
        return UserFriend::where([
            'user_id' => $user->id,
            'friend_id' => $this->id,
            'status' => FriendStatus::PENDING
        ])->exists();
    }

    /**
     * Check if the user has sent a pending friend request to another user
     *
     * @param \App\Models\User $user
     * @return bool
     */
    public function hasPendingRequestTo(User $user): bool
    {
        return UserFriend::where([
            'user_id' => $this->id,
            'friend_id' => $user->id,
            'status' => FriendStatus::PENDING
        ])->exists();
    }

    /**
     * Get the friendship status with another user
     *
     * @param \App\Models\User $user
     * @return string
     */
    public function getFriendshipStatusWith(User $user): string
    {
        // First check for pending requests FROM the other user
        if ($this->hasPendingRequestFrom($user)) {
            return 'REQUEST_RECEIVED';
        }
        
        // Then check if users are friends
        if ($this->isFriendsWith($user)) {
            return 'FRIENDS';
        }
        
        // Then check if current user sent a request TO the other user
        if ($this->hasPendingRequestTo($user)) {
            return 'REQUEST_SENT';
        }
        
        // Default state
        return 'NOT_FRIENDS';
    }

    /**
     * Check if the user is a client.
     *
     * @return bool
     */
    public function isClient(): bool
    {
        return $this->user_type === UserTypes::CLIENT;
    }

    /**
     * Check if the user is an organizer.
     *
     * @return bool
     */
    public function isOrganizer(): bool
    {
        return $this->user_type === UserTypes::ORGANIZER;
    }

    /**
     * Check if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->user_type === UserTypes::ADMIN;
    }

    /**
     * Define the relationship with the OTPs.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function otp()
    {
        return $this->hasMany(UserOtp::class);
    }

    /**
     * Get the latest OTP for the user.
     * 
     * @return UserOtp | null
     */
    public function latestOtp()
    {
        return $this->otp()
            ->latest()
            ->first();
    }

    /**
     * Check if user is mobile verified.
     * 
     * @return bool
     */
    public function isPhoneVerified(): bool
    {
        return $this->otp()
            ->whereNotNull('verified_at')
            ->exists();
    }

    /**
     * Check if the user has a verified OTP.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function hasVerifiedOtp(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->otp()->whereNotNull('verified_at')->exists()
        );
    }

    /**
     * Get the events organized by the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function events()
    {
        return $this->hasMany(Event::class, 'organizer_id');
    }

    /**
     * Get all accepted friends of the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function friends()
    {
        return $this->hasMany(UserFriend::class, 'user_id')
            ->where('status', FriendStatus::ACCEPTED)
            ->with('friend');
    }

    /**
     * Get all pending friend requests sent by the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sentFriendRequests()
    {
        return $this->hasMany(UserFriend::class, 'user_id')
            ->where('status', FriendStatus::PENDING)
            ->with('friend');
    }

    /**
     * Get all pending friend requests received by the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function receivedFriendRequests()
    {
        return $this->hasMany(UserFriend::class, 'friend_id')
            ->where('status', FriendStatus::PENDING)
            ->with('user');
    }
}