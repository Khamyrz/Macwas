@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">Recent Bills</h1>
    <div class="bg-white rounded shadow p-6">
        @if($recentBills->count() > 0)
            <div class="space-y-4">
                @foreach($recentBills as $bill)
                    <div class="border rounded-lg p-4 {{ $bill->status === 'paid' ? 'border-green-2 00 bg-green-50' : ($bill->status === 'partially_paid' ? 'border-yellow-200 bg-yellow-50' : 'border-red-200 bg-red-50') }}">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">{{ $bill->billing_month->format('M Y') }}</h4>
                                <p class="text-sm text-gray-500">{{ $bill->cubic_meters_used }} m³ used</p>
                                <p class="text-sm text-gray-500">Due: {{ $bill->due_date->format('M d, Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold {{ $bill->status === 'paid' ? 'text-green-600' : ($bill->status === 'partially_paid' ? 'text-yellow-600' : 'text-red-600') }}">
                                    ₱{{ number_format($bill->balance, 2) }}
                                </p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $bill->status === 'paid' ? 'bg-green-100 text-green-800' : ($bill->status === 'partially_paid' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst(str_replace('_', ' ', $bill->status)) }}
                                </span>
                                @if($bill->status === 'paid')
                                    <div class="mt-1">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            ✓ Paid {{ $bill->paid_date ? $bill->paid_date->format('M Y') : '' }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center">No recent bills found.</p>
        @endif
    </div>
</div>
@endsection



