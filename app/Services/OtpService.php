<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserOtp;

class OtpService {
    /**
     * Generate a random OTP.
     *
     * @return string
     */
    public function generateOtp(): string
    {
        // return strval(random_int(1000, 9999));

        return '1234';
    }

    /**
     * Create a new OTP.
     *
     * @param  User $user  The user instance
     * @param  string  $otp  The OTP
     *
     * @return UserOtp
     */
    public function createOtp(
        User $user,
        string $otp
    ): UserOtp {
        return $user->otp()->create([
            'code' => $otp,
            'expires_at' => now()
                ->addMinutes(
                    config('constants.otp_expires')
                ),
        ]);
    }

    /**
     * Check if the OTP is correct and not expired.
     *
     * @param  User  $user  The user instance
     * @param  string  $otp  The OTP
     *
     * @return bool | void 
     */
    public function verifyOtp(
        User $user,
        string $otp
    ): bool {
        $latestOtp = $user->latestOtp();

        if(is_null($latestOtp)) {
            return false;
        }

        if($latestOtp->code !== $otp && $latestOtp->expires_at->isPast()) {
            return false;
        }

        $latestOtp->update([
            'verified_at' => now(),
        ]);

        return true;
    }
}