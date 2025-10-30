@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
        <p class="text-gray-600">Manage water system operations and user accounts</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Customers</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalCustomers }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Plumbers</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalPlumbers }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Accountants</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalAccountants }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pending Approvals</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $pendingPlumbers + $pendingAccountants + $pendingCustomers }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Pending Account Approvals -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Pending Account Approvals</h3>
            </div>
            <div class="p-6">
                @if($pendingPlumbers > 0 || $pendingAccountants > 0 || $pendingCustomers > 0)
                    <div class="space-y-4">
                        @if($pendingPlumbers > 0)
                            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                <span class="text-sm font-medium text-blue-900">{{ $pendingPlumbers }} Plumber(s) pending approval</span>
                                <a href="{{ route('admin.pending-accounts') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Review</a>
                            </div>
                        @endif
                        @if($pendingAccountants > 0)
                            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                <span class="text-sm font-medium text-green-900">{{ $pendingAccountants }} Accountant(s) pending approval</span>
                                <a href="{{ route('admin.pending-accounts') }}" class="text-green-600 hover:text-green-800 text-sm font-medium">Review</a>
                            </div>
                        @endif
                        @if($pendingCustomers > 0)
                            <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                                <span class="text-sm font-medium text-purple-900">{{ $pendingCustomers }} Customer(s) pending approval</span>
                                <a href="{{ route('admin.pending-accounts') }}" class="text-purple-600 hover:text-purple-800 text-sm font-medium">Review</a>
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No pending approvals</p>
                @endif
            </div>
        </div>

        <!-- Water Rate Management -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Water Rate Management</h3>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <p class="text-sm text-gray-600">Current Rate:</p>
                    <p class="text-2xl font-bold text-blue-600">
                        ₱{{ $currentRate ? number_format($currentRate->rate_per_cubic_meter, 2) : '0.00' }} per cubic meter
                    </p>
                </div>
                <a href="{{ route('admin.water-rates') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Update Rate
                </a>
            </div>
        </div>

    </div>

    

    <!-- Setup Requests & Pending Water Connections -->
    
        
    
            
           
 
@push('scripts')
<script>
// If the URL has a hash, scroll to the section smoothly
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.hash) {
        const el = document.querySelector(window.location.hash);
        if (el) {
            el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }
});
</script>
@endpush
@endsection
