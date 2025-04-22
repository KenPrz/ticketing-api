@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-100">
        <!-- Navigation Bar (Same as dashboard) -->
        @include('layouts.navbar', ['active' => 'users'])
        
        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <!-- Success Message -->
            @if(session('success'))
                <div class="px-4 sm:px-0 mb-6">
                    <div class="bg-success bg-opacity-10 border border-success text-success px-4 py-3 rounded-lg">
                        {{ session('success') }}
                    </div>
                </div>
            @endif
            
            <!-- Page Header and Navigation -->
            <div class="px-4 py-6 sm:px-0">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">User Details</h1>
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
                                    <span class="font-medium text-gray-700">{{ $user->name }}</span>
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="{{ route('admin.users') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to Users
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- User Details -->
            <div class="px-4 sm:px-0">
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900">User Information</h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">Personal details and account information.</p>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.user.edit', $user->id) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="-ml-0.5 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                Edit
                            </a>
                            @if($user->user_type != 'ADMIN')
                                <form action="{{ route('admin.user.delete', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent shadow-sm text-sm leading-4 font-medium rounded-md text-white bg-danger hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-danger">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="-ml-0.5 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Delete
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    <div class="border-t border-gray-200">
                        <div class="flex">
                            <!-- User Profile Image -->
                            <div class="w-1/3 border-r border-gray-200 p-6 flex flex-col items-center">
                                <div class="h-40 w-40 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 overflow-hidden">
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
                                        <span class="text-6xl font-medium">{{ substr($user->name, 0, 1) }}</span>
                                    @endif
                                </div>
                                <div class="mt-4 text-center">
                                    <h4 class="text-lg font-medium text-gray-900">{{ $user->name }}</h4>
                                    <div class="mt-1">
                                        @if($user->user_type == 'CLIENT')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Client
                                            </span>
                                        @elseif($user->user_type == 'ORGANIZER')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                Organizer
                                            </span>
                                        @elseif($user->user_type == 'ADMIN')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Admin
                                            </span>
                                        @endif
                                    </div>
                                    <p class="mt-2 text-sm text-gray-500">Member since {{ $user->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                            
                            <!-- User Details -->
                            <div class="w-2/3">
                                <dl>
                                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-500">Full name</dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $user->name }}</dd>
                                    </div>
                                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-500">Email address</dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $user->email }}</dd>
                                    </div>
                                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-500">Mobile</dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $user->mobile ?? 'Not provided' }}</dd>
                                    </div>
                                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-500">User type</dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $user->user_type }}</dd>
                                    </div>
                                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-500">User ID</dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $user->id }}</dd>
                                    </div>
                                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-500">Last location</dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                            @if($user->recent_latitude && $user->recent_longitude)
                                                <span>Lat: {{ $user->recent_latitude }}, Long: {{ $user->recent_longitude }}</span>
                                                <a href="https://www.google.com/maps/search/?api=1&query={{ $user->recent_latitude }},{{ $user->recent_longitude }}" target="_blank" class="text-primary hover:text-secondary ml-2">
                                                    (View on map)
                                                </a>
                                            @else
                                                Not available
                                            @endif
                                        </dd>
                                    </div>
                                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-500">Registered on</dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $user->created_at->format('F d, Y \a\t h:i A') }}</dd>
                                    </div>
                                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-500">Last updated</dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $user->updated_at->format('F d, Y \a\t h:i A') }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
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