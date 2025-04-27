<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    /**
     * Get the authenticated user's profile.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Return the user profile data
        return response()->json([
            $request->user()->with('posts'),
        ]);
    }

    /**
     * Update the authenticated user's profile.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'mobile' => 'nullable|string|max:15|unique:users,mobile,' . $request->user()->id,
        ]);

        // Fetch the authenticated user
        $user = $request->user();

        // Update user details
        $user->update($request->only('name', 'email', 'mobile'));

        // Return the updated user profile data
        return response()->json([
            'user' => $user,
        ]);
    }

    public function updateAvatar(Request $request)
    {
        // Validate the request data
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        // Fetch the authenticated user
        $user = $request->user();

        // Store the avatar image
        $path = $request->file('avatar')->store('images/avatars', 'public');

        // Update the user's avatar path
        $user->update(['avatar' => $path]);

        // Return the updated user profile data
        return response()->json([
            'user' => $user,
        ]);
    }

    /**
     * Update the authenticated user's password.
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function updatePassword(Request $request)
    {
        // Validate the request data
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // Fetch the authenticated user
        $user = $request->user();

        // Check if the current password is correct
        if (!password_verify($request->input('current_password'), $user->password)) {
            return response()->json(['message' => 'Current password is incorrect.'], 401);
        }

        // Update the user's password
        $user->update(['password' => bcrypt($request->input('new_password'))]);

        // Return a success message
        return response()->json(['message' => 'Password updated successfully.']);
    }
}
