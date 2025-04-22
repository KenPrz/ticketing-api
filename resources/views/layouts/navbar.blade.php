<!-- Navigation Bar -->
<nav class="bg-white shadow-sm border-b border-gray-200">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo and Brand -->
            <div class="flex items-center">
                <div class="flex-shrink-0 flex items-center">
                    <img class="h-10 w-auto" src="{{ asset('logo.png') }}" alt="Q-Phoria Logo">
                    <span class="ml-2 text-xl font-semibold text-gray-800">Q-Phoria Admin</span>
                </div>
            </div>
            
            <!-- Navigation Links - Center -->
            <div class="hidden md:flex md:items-center md:space-x-4">
                <a href="{{ route('admin.dashboard') }}" 
                    class="px-3 py-2 rounded-md text-sm font-medium {{ $active === 'dashboard' ? 'text-gray-900 bg-gray-100' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">
                    Dashboard
                </a>
                <a href="{{ route('admin.users') }}" 
                    class="px-3 py-2 rounded-md text-sm font-medium {{ $active === 'users' ? 'text-gray-900 bg-gray-100' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">
                    Users
                </a>
            </div>
            
            <!-- Right Navigation -->
            <div class="flex items-center">
                <!-- User Dropdown (Fixed with proper hover behavior) -->
                <div class="ml-3 relative group">
                    <div class="flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary cursor-pointer">
                        <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center text-gray-700">
                            <span class="font-medium">A</span>
                        </div>
                        <span class="ml-2 mt-1 text-gray-700">{{ auth()->user()->name }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1 mt-1.5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    
                    <!-- Dropdown Menu (Fixed with proper hover behavior) -->
                    <div class="hidden group-hover:block absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10">
                        <!-- Add this invisible overlay to maintain hover state -->
                        <div class="fixed inset-0 z-0 hidden group-hover:block" aria-hidden="true"></div>
                        <div class="relative z-10">
                            <a href="{{ route('admin.user.details', ['user' => auth()->user()->id]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Your Profile</a>
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sign out</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Mobile menu - CSS-only toggle with checkbox hack -->
    <div class="md:hidden">
        <input type="checkbox" id="mobile-menu-toggle" class="hidden peer">
        <label for="mobile-menu-toggle" class="block px-2 py-2 text-gray-500 hover:text-gray-900 cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </label>
        <div class="hidden peer-checked:block bg-white border-t border-gray-200 p-2">
            <div class="space-y-1 px-2 pt-2 pb-3 sm:px-3">
                <a href="{{ route('admin.dashboard') }}" 
                    class="block px-3 py-2 rounded-md text-base font-medium {{ $active === 'dashboard' ? 'text-gray-900 bg-gray-100' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">
                    Dashboard
                </a>
                <a href="{{ route('admin.users') }}" 
                    class="block px-3 py-2 rounded-md text-base font-medium {{ $active === 'users' ? 'text-gray-900 bg-gray-100' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">
                    Users
                </a>
            </div>
        </div>
    </div>
</nav>