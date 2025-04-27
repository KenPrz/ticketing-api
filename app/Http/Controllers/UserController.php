<?php

namespace App\Http\Controllers;

use App\Enums\UserTypes;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    /**
     * The index screen for the user when finding a user.
     * 
     * @param \Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function usersHome(Request $request)
    {
        $user = $request->user();

        // Get Random users
        $users = User::where('id', '!=', $user->id)
            ->whereIn('user_type', UserTypes::exposedUserTypes())
            ->inRandomOrder()
            ->limit(20)
            ->get();

        // Pass the friendship status to the resource
        $userData = UserResource::collection($users)
            ->response()
            ->getData(true);

        return response()->json($userData, 200);
    }
    
    /**
     * Display the user's profile.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $userId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserProfile(Request $request, $userId)
    {
        // First find the user by ID
        $user = User::where('id', $userId)
            ->whereIn('user_type', UserTypes::exposedUserTypes())
            ->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Pass the friendship status to the resource
        $userData = UserResource::make($user)
            ->response()
            ->getData(true);
        
        return response()->json($userData, 200);
    }

    /**
     * Search for users by name or email.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchUsers(Request $request)
    {
        $searchQuery = $request->input('query');

        $users = User::where(function ($query) use ($searchQuery) {
                $query->where('name', 'LIKE', "%{$searchQuery}%")
                    ->orWhere('email', 'LIKE', "%{$searchQuery}%");
            })
            ->whereIn('user_type', UserTypes::exposedUserTypes())
            ->limit(20)
            ->get();
    
        // Transform results using UserResource collection
        $usersResource = UserResource::collection($users)
            ->response()
            ->getData(true);

        return response()->json($usersResource, 200);
    }
}
