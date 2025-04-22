@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo and Brand -->
                <a href="{{ route('home') }}" class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <img class="h-10 w-auto" src="{{ asset('logo.png') }}" alt="Q-Phoria Logo">
                        <span class="ml-2 text-xl font-semibold text-gray-800">Q-Phoria</span>
                    </div>
                </a>
                
                <!-- Navigation Links - Center -->
                <div class="hidden md:flex md:items-center md:space-x-8">
                    <a href="#features" class="px-3 py-2 rounded-md text-sm font-medium text-primary-500 hover:text-gray-900 hover:bg-gray-100">Features</a>
                    <a href="#how-it-works" class="px-3 py-2 rounded-md text-sm font-medium text-primary-500 hover:text-gray-900 hover:bg-gray-100">How It Works</a>                    
                    <a href="#testimonials" class="px-3 py-2 rounded-md text-sm font-medium text-primary-500 hover:text-gray-900 hover:bg-gray-100">Testimonials</a>
                </div>
            </div>
        </div>
        
        <!-- Mobile menu toggle -->
        <div class="md:hidden">
            <input type="checkbox" id="mobile-menu-toggle" class="hidden peer">
            <label for="mobile-menu-toggle" class="block px-2 py-2 text-gray-500 hover:text-gray-900 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </label>
            <div class="hidden peer-checked:block bg-white border-t border-gray-200 p-2">
                <div class="space-y-1 px-2 pt-2 pb-3 sm:px-3">
                    <a href="#features" class="block px-3 py-2 rounded-md text-base font-medium text-gray-500 hover:text-gray-900 hover:bg-gray-100">Features</a>
                    <a href="#how-it-works" class="block px-3 py-2 rounded-md text-base font-medium text-gray-500 hover:text-gray-900 hover:bg-gray-100">How It Works</a>
                    <a href="#testimonials" class="block px-3 py-2 rounded-md text-base font-medium text-gray-500 hover:text-gray-900 hover:bg-gray-100">Testimonials</a>
                    <div class="pt-4 pb-3 border-t border-gray-200">
                        <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-500 hover:text-gray-900 hover:bg-gray-100">Login</a>
                        <a href="{{ route('register') }}" class="mt-1 block w-full px-3 py-2 rounded-md text-base font-medium text-center text-white bg-gradient-to-r from-primary-gradient-from to-primary-gradient-to">Sign Up</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section with Background Image (No Fade) -->
    <div class="relative overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('event.jpg') }}" alt="Event background" class="w-full h-full object-cover">
        </div>
        
        <div class="max-w-7xl mx-auto relative z-10">
            <div class="relative pb-8 sm:pb-16 md:pb-20 lg:w-full lg:pb-28 xl:pb-32">
                <div class="pt-10 mx-auto max-w-7xl px-4 sm:pt-12 sm:px-6 md:pt-16 lg:pt-20 lg:px-8 xl:pt-28">
                    <div class="sm:text-center lg:text-left">
                        <h1 class="text-4xl tracking-tight font-extrabold text-white sm:text-5xl md:text-6xl">
                            <span class="block">Elevate Your</span>
                            <span class="block text-transparent bg-clip-text bg-gradient-to-r from-primary-gradient-from to-primary-gradient-to">Event Experience</span>
                        </h1>
                        <p class="mt-3 text-base text-white sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                            Q-Phoria streamlines event management and enhances attendee experiences. Organize, discover, and enjoy events like never before.
                        </p>
                        <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                            <div class="rounded-md shadow">
                            <a href="#how-it-works" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-gray-800 bg-opacity-60 hover:bg-opacity-70 md:py-4 md:text-lg md:px-10 transition-colors duration-300">
                                    Learn More
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div id="features" class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:text-center">
                <h2 class="text-base text-primary font-semibold tracking-wide uppercase">Features</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    Everything you need for perfect events
                </p>
                <p class="mt-4 max-w-2xl text-xl text-gray-500 lg:mx-auto">
                    Q-Phoria provides powerful tools for event organizers and an amazing experience for attendees.
                </p>
            </div>

            <div class="mt-10">
                <div class="space-y-10 md:space-y-0 md:grid md:grid-cols-2 md:gap-x-8 md:gap-y-10">
                    <!-- Feature 1 -->
                    <div class="relative">
                        <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-gradient-to-r from-primary-gradient-from to-primary-gradient-to text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-16">
                            <h3 class="text-lg font-medium text-gray-900">Seamless Event Creation</h3>
                            <p class="mt-2 text-base text-gray-500">
                                Create and customize events in minutes with our intuitive event builder. Set dates, venues, and ticket tiers effortlessly.
                            </p>
                        </div>
                    </div>

                    <!-- Feature 2 -->
                    <div class="relative">
                        <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-gradient-to-r from-primary-gradient-from to-primary-gradient-to text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <div class="ml-16">
                            <h3 class="text-lg font-medium text-gray-900">Secure Payments</h3>
                            <p class="mt-2 text-base text-gray-500">
                                Process payments securely with our integrated payment system. Support for multiple payment methods and currencies.
                            </p>
                        </div>
                    </div>

                    <!-- Feature 3 -->
                    <div class="relative">
                        <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-gradient-to-r from-primary-gradient-from to-primary-gradient-to text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                        <div class="ml-16">
                            <h3 class="text-lg font-medium text-gray-900">Smart Notifications</h3>
                            <p class="mt-2 text-base text-gray-500">
                                Keep attendees informed with automated reminders and updates. Reduce no-shows and improve event attendance.
                            </p>
                        </div>
                    </div>

                    <!-- Feature 4 -->
                    <div class="relative">
                        <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-gradient-to-r from-primary-gradient-from to-primary-gradient-to text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div class="ml-16">
                            <h3 class="text-lg font-medium text-gray-900">Powerful Analytics</h3>
                            <p class="mt-2 text-base text-gray-500">
                                Gain insights into attendee behavior and event performance with detailed analytics and customizable reports.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- How It Works Section -->
    <div id="how-it-works" class="bg-gray-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:text-center">
                <h2 class="text-base text-primary font-semibold tracking-wide uppercase">How It Works</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    Simple steps to event success
                </p>
                <p class="mt-4 max-w-2xl text-xl text-gray-500 lg:mx-auto">
                    Get started with Q-Phoria in just a few simple steps and transform your event management experience.
                </p>
            </div>

            <div class="mt-10">
                <div class="space-y-10 md:space-y-0 md:grid md:grid-cols-3 md:gap-x-8 md:gap-y-10">
                    <!-- Step 1 -->
                    <div class="relative">
                        <div class="flex items-center justify-center h-16 w-16 rounded-full bg-gradient-to-r from-primary-gradient-from to-primary-gradient-to text-white text-xl font-bold mb-4">1</div>
                        <h3 class="text-lg font-medium text-gray-900">Create your event</h3>
                        <p class="mt-2 text-base text-gray-500">
                            Sign up as an organizer and use our intuitive builder to create and customize your event with all the necessary details.
                        </p>
                    </div>

                    <!-- Step 2 -->
                    <div class="relative">
                        <div class="flex items-center justify-center h-16 w-16 rounded-full bg-gradient-to-r from-primary-gradient-from to-primary-gradient-to text-white text-xl font-bold mb-4">2</div>
                        <h3 class="text-lg font-medium text-gray-900">Promote and sell tickets</h3>
                        <p class="mt-2 text-base text-gray-500">
                            Share your event page and use our marketing tools to reach potential attendees and sell tickets securely.
                        </p>
                    </div>

                    <!-- Step 3 -->
                    <div class="relative">
                        <div class="flex items-center justify-center h-16 w-16 rounded-full bg-gradient-to-r from-primary-gradient-from to-primary-gradient-to text-white text-xl font-bold mb-4">3</div>
                        <h3 class="text-lg font-medium text-gray-900">Host and manage</h3>
                        <p class="mt-2 text-base text-gray-500">
                            Use our check-in tools and real-time dashboard to manage your event seamlessly and provide the best experience.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Testimonials Section -->
    <div id="testimonials" class="bg-gray-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:text-center mb-10">
                <h2 class="text-base text-primary font-semibold tracking-wide uppercase">Testimonials</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    Loved by organizers and attendees
                </p>
                <p class="mt-4 max-w-2xl text-xl text-gray-500 lg:mx-auto">
                    Hear what our users have to say about their Q-Phoria experience.
                </p>
            </div>

            <div class="mt-10 grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                <!-- Testimonial 1 -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-primary" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9.983 3v7.391c0 5.704-3.731 9.57-8.983 10.609l-.995-2.151c2.432-.917 3.995-3.638 3.995-5.849h-4v-10h9.983zm14.017 0v7.391c0 5.704-3.748 9.571-9 10.609l-.996-2.151c2.433-.917 3.996-3.638 3.996-5.849h-3.983v-10h9.983z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-600">
                                "Q-Phoria has completely transformed how we manage our corporate events. The platform is intuitive, powerful, and has saved us countless hours of work."
                            </p>
                            <div class="mt-4 flex items-center">
                                <div class="flex-shrink-0">
                                    <img class="h-10 w-10 rounded-full" src="{{ asset('testimonial1.png') }}" alt="Sarah Johnson">
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">Sarah Johnson</p>
                                    <p class="text-sm text-gray-500">Event Director, TechCorp Inc.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 2 -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-primary" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9.983 3v7.391c0 5.704-3.731 9.57-8.983 10.609l-.995-2.151c2.432-.917 3.995-3.638 3.995-5.849h-4v-10h9.983zm14.017 0v7.391c0 5.704-3.748 9.571-9 10.609l-.996-2.151c2.433-.917 3.996-3.638 3.996-5.849h-3.983v-10h9.983z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-600">
                                "As a frequent event-goer, I love how easy it is to find and book tickets through Q-Phoria. Their notification system ensures I never miss an event!"
                            </p>
                            <div class="mt-4 flex items-center">
                                <div class="flex-shrink-0">
                                    <img class="h-10 w-10 rounded-full" src="{{ asset('testimonial2.png') }}" alt="Michael Chen">
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">Michael Chen</p>
                                    <p class="text-sm text-gray-500">Regular Attendee</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 3 -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-primary" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9.983 3v7.391c0 5.704-3.731 9.57-8.983 10.609l-.995-2.151c2.432-.917 3.995-3.638 3.995-5.849h-4v-10h9.983zm14.017 0v7.391c0 5.704-3.748 9.571-9 10.609l-.996-2.151c2.433-.917 3.996-3.638 3.996-5.849h-3.983v-10h9.983z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-600">
                                "Our music festival attendance increased by 40% after switching to Q-Phoria. The analytics alone are worth it - we now know exactly how to target our promotions."
                            </p>
                            <div class="mt-4 flex items-center">
                                <div class="flex-shrink-0">
                                    <img class="h-10 w-10 rounded-full" src="{{ asset('testimonial3.png') }}" alt="Alicia Rodriguez">
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">Alicia Rodriguez</p>
                                    <p class="text-sm text-gray-500">Festival Organizer, Harmony Productions</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    @include('layouts.footer')
</div>
@endsection