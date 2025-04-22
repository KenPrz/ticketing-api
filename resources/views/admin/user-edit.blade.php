@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-100">
        <!-- Navigation Bar -->
        @include('layouts.navbar', ['active' => 'users'])
        
        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <!-- Page Header and Navigation -->
            <div class="px-4 py-6 sm:px-0">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">Edit User</h1>
                        <nav class="flex mt-2" aria-label="Breadcrumb">
                            <ol class="flex items-center space-x-1 text-sm text-gray-500">
                                <li>
                                    <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-700">Dashboard</a>
                                </li>
                                <li>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </li>
                                <li>
                                    <a href="{{ route('admin.users') }}" class="hover:text-gray-700">Users</a>
                                </li>
                                <li>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </li>
                                <li>
                                    <a href="{{ route('admin.user.details', $user->id) }}" class="hover:text-gray-700">{{ $user->name }}</a>
                                </li>
                                <li>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </li>
                                <li>
                                    <span class="font-medium text-gray-700">Edit</span>
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="{{ route('admin.user.details', $user->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to User Details
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Edit User Form -->
            <div class="px-4 sm:px-0">
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Edit User Information</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">Update user details and preferences.</p>
                    </div>
                    
                    <div class="border-t border-gray-200 p-6">
                        <form action="{{ route('admin.user.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <!-- Left Column -->
                                <div class="space-y-6">
                                    <!-- Name -->
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" 
                                            class="block w-full h-10 px-3 border-gray-300 rounded-md shadow-sm 
                                            focus:ring-primary focus:border-primary 
                                            @error('name') border-danger @enderror">
                                        @error('name')
                                            <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <!-- Email -->
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" 
                                            class="block w-full h-10 px-3 border-gray-300 rounded-md shadow-sm 
                                            focus:ring-primary focus:border-primary 
                                            @error('email') border-danger @enderror">
                                        @error('email')
                                            <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <!-- Mobile -->
                                    <div>
                                        <label for="mobile" class="block text-sm font-medium text-gray-700 mb-1">Mobile Number</label>
                                        <input type="text" name="mobile" id="mobile" value="{{ old('mobile', $user->mobile) }}" 
                                            class="block w-full h-10 px-3 border-gray-300 rounded-md shadow-sm 
                                            focus:ring-primary focus:border-primary 
                                            @error('mobile') border-danger @enderror">
                                        @error('mobile')
                                            <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <!-- User Type -->
                                    <div>
                                        <label for="user_type" class="block text-sm font-medium text-gray-700 mb-1">User Type</label>
                                        <select id="user_type" name="user_type" 
                                            class="block w-full h-10 px-3 border-gray-300 rounded-md shadow-sm 
                                            focus:ring-primary focus:border-primary 
                                            @error('user_type') border-danger @enderror">
                                            <option value="CLIENT" {{ (old('user_type', $user->user_type) == 'CLIENT') ? 'selected' : '' }}>Client</option>
                                            <option value="ORGANIZER" {{ (old('user_type', $user->user_type) == 'ORGANIZER') ? 'selected' : '' }}>Organizer</option>
                                            <option value="ADMIN" {{ (old('user_type', $user->user_type) == 'ADMIN') ? 'selected' : '' }}>Admin</option>
                                        </select>
                                        @error('user_type')
                                            <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                
                                <!-- Right Column - Avatar Display (no edit) -->
                                <div class="space-y-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Profile Photo</label>
                                        <div class="mt-4 flex flex-col items-center">
                                            <div class="mb-4 w-32 h-32 rounded-full overflow-hidden bg-gray-200">
                                                @if($user->avatar)
                                                    @php
                                                        // Handle potential localhost duplication in the URL
                                                        $avatarPath = $user->avatar;
                                                        
                                                        // Remove duplicate localhost if present
                                                        $avatarPath = preg_replace('#^/?localhost/#', '', $avatarPath);
                                                        
                                                        // Generate the proper URL
                                                        $avatarUrl = filter_var($avatarPath, FILTER_VALIDATE_URL) ? 
                                                            $avatarPath : 
                                                            asset('storage/' . $avatarPath);
                                                    @endphp
                                                    <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                                                @else
                                                    <div class="h-full w-full flex items-center justify-center text-gray-600">
                                                        <span class="text-5xl font-medium">{{ substr($user->name, 0, 1) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <p class="text-sm text-gray-500">Current profile photo</p>
                                        </div>
                                    </div>

                                    <!-- ID and Other Info -->
                                    <div class="mt-6 space-y-4">
                                        <div class="p-4 bg-gray-50 rounded-md">
                                            <h4 class="text-sm font-medium text-gray-500">User ID</h4>
                                            <p class="mt-1 text-sm text-gray-900">{{ $user->id }}</p>
                                        </div>
                                        <div class="p-4 bg-gray-50 rounded-md">
                                            <h4 class="text-sm font-medium text-gray-500">Member Since</h4>
                                            <p class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('F d, Y') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Form Footer -->
                            <div class="mt-8 pt-5 border-t border-gray-200 flex justify-end space-x-3">
                                <a href="{{ route('admin.user.details', $user->id) }}" 
                                   class="inline-flex items-center justify-center h-10 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                    Cancel
                                </a>
                                <button type="submit" 
                                        class="inline-flex items-center justify-center h-10 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
        
        <!-- Footer -->
        <footer class="bg-white shadow-inner mt-8 py-4">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <p class="text-center text-sm text-gray-500">
                    &copy; {{ date('Y') }} Q-Phoria. All rights reserved.
                </p>
            </div>
        </footer>
    </div>
@endsection