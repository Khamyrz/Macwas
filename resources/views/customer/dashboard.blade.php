@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Customer Dashboard</h1>
        <p class="text-gray-600">View your water bills and payment information</p>
    </div>

    <!-- Current Bill Status -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Current Bill Status</h3>
        </div>
        <div class="p-6">
            @if($currentBill)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="p-3 rounded-full {{ $currentBill->isOverdue() ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-600' }} mx-auto w-16 h-16 flex items-center justify-center">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h4 class="mt-2 text-sm font-medium text-gray-900">Billing Month</h4>
                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $currentBill->billing_month->format('M Y') }}</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="p-3 rounded-full {{ $currentBill->isOverdue() ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }} mx-auto w-16 h-16 flex items-center justify-center">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <h4 class="mt-2 text-sm font-medium text-gray-900">Total Bill</h4>
                        <p class="mt-1 text-lg font-semibold {{ $currentBill->isOverdue() ? 'text-red-600' : 'text-gray-900' }}">
                            ₱{{ number_format($currentBill->total_amount, 2) }}
                        </p>
                    </div>
                    
                    <div class="text-center">
                        <div class="p-3 rounded-full {{ $currentBill->isOverdue() ? 'bg-red-100 text-red-600' : 'bg-yellow-100 text-yellow-600' }} mx-auto w-16 h-16 flex items-center justify-center">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h4 class="mt-2 text-sm font-medium text-gray-900">Due Date</h4>
                        <p class="mt-1 text-lg font-semibold {{ $currentBill->isOverdue() ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $currentBill->due_date->format('M d, Y') }}
                        </p>
                    </div>
                </div>
                
                @if($currentBill->isOverdue())
                    <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Bill Overdue</h3>
                                <p class="mt-1 text-sm text-red-700">Your bill is overdue. Please visit an accountant to settle your payment.</p>
                            </div>
                        </div>
                    </div>
                @endif
                
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Water Consumption</h4>
                        <p class="text-2xl font-bold text-blue-600">{{ $currentBill->cubic_meters_used }} m³</p>
                        <p class="text-sm text-gray-500">Rate: ₱{{ number_format($currentBill->rate_per_cubic_meter, 2) }}/m³</p>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Total Amount</h4>
                        <p class="text-2xl font-bold text-gray-900">₱{{ number_format($currentBill->total_amount, 2) }}</p>
                        <p class="text-sm text-gray-500">Amount Paid: ₱{{ number_format($currentBill->amount_paid, 2) }}</p>
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 mx-auto w-16 h-16 flex items-center justify-center">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">All Bills Paid</h3>
                    <p class="mt-2 text-sm text-gray-500">You have no outstanding bills for the current month.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Water Connection Info -->
    @if($waterConnection)
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Your Plumber</h3>
        </div>
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-16 w-16">
                    @if($waterConnection->plumber->photo)
                        <img class="h-16 w-16 rounded-full" src="{{ Storage::url($waterConnection->plumber->photo) }}" alt="">
                    @else
                        <div class="h-16 w-16 rounded-full bg-gray-300 flex items-center justify-center">
                            <span class="text-gray-600 font-medium text-lg">{{ substr($waterConnection->plumber->first_name, 0, 1) }}{{ substr($waterConnection->plumber->last_name, 0, 1) }}</span>
                        </div>
                    @endif
                </div>
                <div class="ml-6">
                    <h4 class="text-lg font-medium text-gray-900">{{ $waterConnection->plumber->full_name }}</h4>
                    <p class="text-sm text-gray-500">{{ $waterConnection->plumber->address }}</p>
                    <p class="text-sm text-gray-500">{{ $waterConnection->plumber->phone_number }}</p>
                    <div class="mt-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $waterConnection->status === 'completed' ? 'bg-green-100 text-green-800' : ($waterConnection->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst(str_replace('_', ' ', $waterConnection->status)) }}
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h5 class="text-sm font-medium text-gray-900 mb-1">Connection Date</h5>
                    <p class="text-sm text-gray-600">{{ $waterConnection->connection_date->format('M d, Y') }}</p>
                </div>
                @if($waterConnection->completion_date)
                <div class="bg-gray-50 rounded-lg p-4">
                    <h5 class="text-sm font-medium text-gray-900 mb-1">Completion Date</h5>
                    <p class="text-sm text-gray-600">{{ $waterConnection->completion_date->format('M d, Y') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

   
            </div>
        </div>
    </div>
</div>
@endsection
