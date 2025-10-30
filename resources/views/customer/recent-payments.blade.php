@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">Recent Payments</h1>
    <div class="bg-white rounded shadow p-6">
        @if($paymentHistory->count() > 0)
            <div class="space-y-4">
                @foreach($paymentHistory as $payment)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">{{ $payment->waterBill->billing_month->format('M Y') }}</h4>
                                <p class="text-sm text-gray-500">{{ $payment->payment_method }}</p>
                                <p class="text-sm text-gray-500">{{ $payment->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-green-600">₱{{ number_format($payment->amount_paid, 2) }}</p>
                                @if($payment->reference_number)
                                    <p class="text-sm text-gray-500">Ref: {{ $payment->reference_number }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center">No recent payments found.</p>
        @endif
    </div>
</div>
@endsection



