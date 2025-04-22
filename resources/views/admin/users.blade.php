@extends('layouts.app')
@use(App\Enums\UserTypes)

@section('content')
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navbar', ['active' => 'users'])
        
        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <!-- Page Heading -->
            <div class="px-4 py-6 sm:px-0">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-semibold text-gray-900">Users</h1>
                </div>
                <p class="mt-2 text-sm text-gray-600">Manage registered users of the platform.</p>
            </div>
            
            <!-- Filter and Search -->
            <div class="px-4 sm:px-0 mb-6">
                <div class="bg-white shadow rounded-lg p-4 sm:p-6">
                    <form action="{{ route('admin.users') }}" method="GET" class="space-y-4 sm:space-y-0 sm:flex sm:items-end sm:space-x-4">
                        <div class="flex-1">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text" name="search" id="search" value="{{ request('search') }}" class="focus:ring-primary focus:border-primary block w-full pl-10 sm:text-sm border-gray-300 rounded-md py-2" placeholder="Search by name or email">
                            </div>
                        </div>
                        
                        <div class="w-full sm:w-48">
                            <label for="user_type" class="block text-sm font-medium text-gray-700 mb-1">User Type</label>
                            <select id="user_type" name="user_type" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md">
                                <option value="">All Types</option>
                                <option value="client" {{ request('user_type') == 'client' ? 'selected' : '' }}>Client</option>
                                <option value="organizer" {{ request('user_type') == 'organizer' ? 'selected' : '' }}>Organizer</option>
                            </select>
                        </div>
                        
                        <div class="w-full sm:w-48">
                            <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                            <select id="sort" name="sort" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md">
                                <option value="desc" {{ request('sort') == 'desc' || !request('sort') ? 'selected' : '' }}>Newest First</option>
                                <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Oldest First</option>
                            </select>
                        </div>
                        
                        <div>
                            <button type="submit" class="bg-primary hover:bg-opacity-90 text-white px-4 py-2 rounded-md shadow-sm text-sm font-medium">
                                Filter
                            </button>
                            @if(request('search') || request('user_type') || request('sort'))
                                <a href="{{ route('admin.users') }}" class="ml-2 text-gray-600 hover:text-gray-900 text-sm font-medium">
                                    Clear
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Users Table -->
            <div class="px-4 sm:px-0">
                <div class="bg-white shadow overflow-hidden rounded-lg">
                    @if($users->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            ID
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Name
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Email
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            User Type
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Registered On
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($users as $user)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $user->id }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 overflow-hidden">
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
                                                            <span>{{ substr($user->name, 0, 1) }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $user->name }}
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            {{ $user->mobile }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $user->email }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($user->user_type == 'client')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                        Client
                                                    </span>
                                                @elseif($user->user_type == 'organizer')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                        Organizer
                                                    </span>
                                                @elseif($user->user_type == 'admin')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        Admin
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                        {{ $user->user_type }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $user->created_at->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('admin.user.details', ['user' => $user->id]) }}" class="text-primary hover:text-secondary mr-3">View</a>
                                                <a href="{{ route('admin.user.edit', ['user' => $user->id]) }}" class="text-gray-600 hover:text-gray-900 mr-3">Edit</a>
                                                @if($user->user_type != UserTypes::ADMIN->value)
                                                    <form action="{{ route('admin.user.delete', ['user' => $user->id]) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-danger hover:text-opacity-90" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                            {{ $users->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="p-6 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No users found</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                @if(request('search') || request('user_type'))
                                    No users match your filter criteria.
                                @endif
                            </p>
                            <div class="mt-6">
                                @if(request('search') || request('user_type'))
                                    <a href="{{ route('admin.users') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                        Clear filters
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </main>
        
        @include('layouts.footer')
    </div>
@endsection