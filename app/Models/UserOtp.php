<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserOtp extends Model
{
    /**
     * The fields that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'code',
        'expires_at',
        'verified_at',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * Get the user that owns the otp.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the OTP is expired.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return now()->gte($this->expires_at);
    }

    /**
     * Check if the OTP is verified.
     *
     * @return bool
     */
    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }

    /**
     * Verify the OTP.
     *
     * @return void
     */
    public function verify(): void
    {
        $this->verified_at = now();
        $this->save();
    }
}
