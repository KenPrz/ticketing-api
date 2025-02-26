<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\UserTypes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

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
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'user_type' => UserTypes::class,
    ];

    /**
     * Get the attributes that should be cast.
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
    public function otps()
    {
        return $this->hasMany(UserOtp::class);
    }

    /**
     * Get the latest OTP for the user.
     */
    public function latestOtp()
    {
        return $this->hasOne(UserOtp::class)
            ->latest();
    }

    /**
     * Check if user is phone verified.
     * 
     * @return bool
     */
    public function isPhoneVerified(): bool
    {
        return $this->otps()
            ->whereNotNull('verified_at')
            ->exists();
    }

    /**
     * Check if the user has a verified OTP.
     *
     * @return bool
     */
    public function hasVerifiedOtp(): bool
    {
        return $this->otps()
            ->whereNotNull('verified_at')
            ->exists();
    }
}
