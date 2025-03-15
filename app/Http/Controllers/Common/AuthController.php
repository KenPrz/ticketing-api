<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Http\Requests\Common\{
    LoginRequest,
    RegistrationRequest,
};
use App\Services\AuthService;
use Illuminate\Http\{
    JsonResponse,
    Request,
};

class AuthController extends Controller
{

    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Login the user.
     * 
     * @param LoginRequest $request
     *
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $request->validated();

            $credentials = $request->only('email', 'password');

            $token = $this->authService->attemptLogin($credentials);

            return response()->json(['token' => $token], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }

    /**
     * Register a new user.
     * 
     * @param RegistrationRequest $request
     * 
     * @return JsonResponse
     */
    public function register(RegistrationRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $token = $this->authService->registerClient($data);

            return response()->json(['token' => $token], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }

    /**
     * Logout the user.
     * 
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json(['message' => 'Logged out successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }

    /**
     * Get the authenticated user.
     * 
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json(
            $request->user()->load([])->append('has_verified_otp'), 200,
        );
    }    

    public function verificationStatus(Request $request): JsonResponse
    {
        return response()->json(
            $request->user()->load([])->append('has_verified_otp'), 200,
        );
    }
}
