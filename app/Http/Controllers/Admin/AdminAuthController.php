<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserTypes; // Add the UserTypes enum import

class AdminAuthController extends Controller
{
    /**
     * Handle the login request for the admin.
     *
     * @param Request $request
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->has('remember'))) {
            // Check if the authenticated user is an admin
            if (Auth::user()->user_type !== UserTypes::ADMIN) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return back()->with('error', 'You do not have permission to access the admin portal.');
            }
            
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return back()->with('error', 'The provided credentials do not match our records.');
    }

    /**
     * Handle the logout request for the admin.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login')->with('success', 'You have been logged out successfully');
    }
}