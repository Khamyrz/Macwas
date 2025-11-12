<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- SEO Meta Tags -->
    @if(request()->is('public/*') || request()->is('storage/*') || request()->is('vendor/*') || request()->is('images/*'))
        <meta name="robots" content="noindex, nofollow" />
    @else
        <meta name="robots" content="index, follow" />
    @endif

    <title>{{ config('app.name', 'Macwas') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen">
        <nav class="bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('dashboard') }}" class="text-xl font-bold text-blue-600">Water Management System</a>
                    </div>
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        @auth
                            <div class="relative">
                                <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition duration-150 ease-in-out">
                                    <div class="flex items-center">
                                        @if(auth()->user()->photo)
                                            <img class="h-8 w-8 rounded-full" src="{{ Storage::url(auth()->user()->photo) }}" alt="">
                                        @else
                                            <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                                <span class="text-gray-600 font-medium text-sm">{{ substr(auth()->user()->first_name, 0, 1) }}{{ substr(auth()->user()->last_name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                        <span class="ml-2">{{ auth()->user()->full_name }}</span>
                                        <span class="ml-2 text-xs text-gray-400">({{ ucfirst(auth()->user()->role) }})</span>
                                    </div>
                                </button>
                            </div>

                            <!-- Logout -->
                            <form method="POST" action="{{ route('logout') }}" class="ml-4">
                                @csrf
                                <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">
                                    Logout
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-gray-700">Login</a>
                            <a href="{{ route('register') }}" class="ml-4 text-sm text-gray-500 hover:text-gray-700">Register</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <div class="flex">
            <!-- Sidebar -->
            @auth
            <aside class="w-64 bg-white border-r min-h-screen hidden md:block">
                <div class="p-4">
                    <div class="text-xs text-gray-400 uppercase mb-2">Navigation</div>
                    <nav class="space-y-1">
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded hover:bg-gray-100">Dashboard</a>
                            <a href="{{ route('admin.pending-accounts') }}" class="block px-3 py-2 rounded hover:bg-gray-100">Pending Accounts</a>
                            <a href="{{ route('admin.water-rates') }}" class="block px-3 py-2 rounded hover:bg-gray-100">Water Rates</a>
                            <a href="{{ route('admin.create-user') }}" class="block px-3 py-2 rounded hover:bg-gray-100">Create User</a>
                            <div class="border-t my-2"></div>
                            <div class="text-xs text-gray-400 uppercase mb-2">User Records</div>
                            <a href="{{ route('admin.user-records', 'customer') }}" class="block px-3 py-2 rounded hover:bg-gray-100">Customer Records</a>
                            <a href="{{ route('admin.user-records', 'plumber') }}" class="block px-3 py-2 rounded hover:bg-gray-100">Plumber Records</a>
                            <a href="{{ route('admin.user-records', 'accountant') }}" class="block px-3 py-2 rounded hover:bg-gray-100">Accountant Records</a>
                            <div class="border-t my-2"></div>
                            <div class="text-xs text-gray-400 uppercase mb-2">Operations</div>
                            <a href="{{ route('admin.monthly-bills') }}" class="block px-3 py-2 rounded hover:bg-gray-100">Monthly Bills</a>
                            <div class="border-t my-2"></div>
                            <div class="text-xs text-gray-400 uppercase mb-2">Security</div>
                            <a href="{{ route('admin.monitoring') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('admin.monitoring*') ? 'bg-blue-100 text-blue-700' : '' }}">
                                <i class="fas fa-shield-alt mr-2"></i>Security Monitoring
                            </a>
                        @elseif(auth()->user()->isAccountant())
                            <a href="{{ route('accountant.dashboard') }}" class="block px-3 py-2 rounded hover:bg-gray-100">Dashboard</a>
                            <a href="{{ route('accountant.payment-history') }}" class="block px-3 py-2 rounded hover:bg-gray-100">Payment History</a>
                        @elseif(auth()->user()->isPlumber())
                            <a href="{{ route('plumber.dashboard') }}" class="block px-3 py-2 rounded hover:bg-gray-100">Dashboard</a>
                            <a href="{{ route('plumber.customer-history') }}" class="block px-3 py-2 rounded hover:bg-gray-100">Customer History</a>
                        @elseif(auth()->user()->isCustomer())
                            <a href="{{ route('customer.dashboard') }}" class="block px-3 py-2 rounded hover:bg-gray-100">Dashboard</a>
                            <a href="{{ route('customer.recent-bills') }}" class="block px-3 py-2 rounded hover:bg-gray-100">Recent Bills</a>
                            <a href="{{ route('customer.recent-payments') }}" class="block px-3 py-2 rounded hover:bg-gray-100">Recent Payments</a>
                            <div class="border-t my-2"></div>
                            <a href="{{ route('customer.bills') }}" class="block px-3 py-2 rounded hover:bg-gray-100">All Bills</a>
                            <a href="{{ route('customer.payment-history') }}" class="block px-3 py-2 rounded hover:bg-gray-100">All Payments</a>
                            <a href="{{ route('customer.profile') }}" class="block px-3 py-2 rounded hover:bg-gray-100">Profile</a>
                        @endif
                    </nav>
                </div>
            </aside>
            @endauth

            <!-- Page Content -->
            <main class="flex-1">
            @if(session('success'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            @yield('content')
            </main>
        </div>
    </div>
    
    <!-- Cookie Consent -->
    @include('components.cookie-consent')
    
    @stack('scripts')
</body>
</html>



