<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Http\Requests\Common\OtpRequest;
use Illuminate\Http\Request;
use App\Services\OtpService;

class OtpController extends Controller
{
    /**
     * The OTP service instance.
     */
    protected $otpService;

    /**
     * Construct the otp service instance.
     *
     * @param  OtpService  $otpService  The OTP service instance
     *
     * @return void
     */
    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Generate an OTP and send it to the user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOtp(Request $request)
    {
        $user = $request->user();

        $otp = $this->otpService->generateOtp();

        $this->otpService->createOtp($user, $otp);

        return response()->json([
            'message' => 'The OTP has been sent to your number.',
        ]);
    }

    /**
     * Verify the OTP.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyOtp(OtpRequest $request)
    {
        $data = $request->validated();
        // Verify the OTP.
        if($this->otpService->verifyOtp(
            $request->user(),
            $data['otp'])
        ) {
            return response()->json([
                'message' => 'The OTP is correct.',
                'data' => [
                    'token' => $request->user()->createToken('auth_token')->plainTextToken,
                    'user' => $request->user(),
                ]
            ]);
        }
        // Return an error message if the OTP is incorrect.
        return response()->json([
            'message' => 'The OTP is incorrect.',
        ], 400);
    }

    /**
     * Resend the OTP.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendOtp(Request $request)
    {
        $user = $request->user();

        $otp = $this->otpService->generateOtp();

        $this->otpService->createOtp($user, $otp);

        return response()->json([
            'message' => 'The OTP has been resent to your number.',
        ]);
    }
}
