@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gradient-to-b from-nav-from via-nav-via to-nav-to flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <!-- Login Card -->
            <div class="bg-white shadow-xl rounded-xl p-8 border border-gray-200">
                <div class="text-center mb-8">
                    <!-- Brand Logo -->
                    <div class="mx-auto mb-4 flex justify-center">
                        <img src="{{ asset('logo.png') }}" alt="Q-Phoria Logo" class="h-24 w-auto">
                    </div>
                    <h2 class="text-2xl font-bold text-dark">Admin Portal</h2>
                    <p class="text-gray-600 mt-1">Please sign in to your account</p>
                </div>
                
                @if(session('error'))
                    <div class="bg-danger bg-opacity-10 border border-danger text-danger px-4 py-3 rounded-lg mb-4">
                        {{ session('error') }}
                    </div>
                @endif
                
                <form method="POST" action="{{ route('admin.login') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary @error('email') border-danger @enderror">
                        @error('email')
                            <span class="text-sm text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input id="password" type="password" name="password" required 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary @error('password') border-danger @enderror">
                        @error('password')
                            <span class="text-sm text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember" type="checkbox" name="remember" 
                                class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                            <label for="remember" class="ml-2 block text-sm text-gray-700">
                                Remember me
                            </label>
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="w-full bg-gradient-to-r from-primary-gradient-from to-primary-gradient-to hover:from-secondary hover:to-tertiary text-white font-medium py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all duration-300 transform hover:scale-[1.02]">
                            Sign In
                        </button>
                    </div>
                </form>
                
                <!-- Success check icon (hidden by default) -->
                <div class="hidden mt-4 flex justify-center">
                    <div class="w-12 h-12 rounded-full bg-success flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 text-center">
                <p class="text-sm text-gray-200">
                    &copy; {{ date('Y') }} Q-Phoria. All rights reserved.
                </p>
            </div>
        </div>
    </div>
@endsection