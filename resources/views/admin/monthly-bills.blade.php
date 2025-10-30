@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Monthly Bills</h1>
        <p class="text-gray-600">Generate monthly bills and review recent billing activity</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <form action="{{ route('admin.generate-monthly-bills') }}" method="POST" class="inline" onsubmit="return confirm('Generate bills for eligible customers for the current month?');">
            @csrf
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Generate Monthly Bills
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Monthly Earnings</h3>
            </div>
            <div class="p-6">
                <canvas id="earningsChart" width="400" height="200"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Monthly Water Consumption</h3>
            </div>
            <div class="p-6">
                <canvas id="consumptionChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Recent Bills</h3>
        </div>
        <div class="p-6">
            @if($recentBills->count())
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cubic Meters</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($recentBills as $bill)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ $bill->customer_id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($bill->billing_month)->format('Y-m') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $bill->cubic_meters_used }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($bill->total_amount, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $bill->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">{{ ucfirst($bill->status) }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $bill->created_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-center">No bills generated yet.</p>
            @endif
        </div>
    </div>
</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const earningsCtx = document.getElementById('earningsChart').getContext('2d');
new Chart(earningsCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($monthlyEarnings->pluck('month')) !!},
        datasets: [{
            label: 'Monthly Earnings (₱)',
            data: {!! json_encode($monthlyEarnings->pluck('total')) !!},
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.1
        }]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
});

const consumptionCtx = document.getElementById('consumptionChart').getContext('2d');
new Chart(consumptionCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($monthlyConsumption->pluck('month')) !!},
        datasets: [{
            label: 'Cubic Meters Used',
            data: {!! json_encode($monthlyConsumption->pluck('total_cubic_meters')) !!},
            borderColor: 'rgb(34, 197, 94)',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            tension: 0.1
        }]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
});
</script>
@endpush
@endsection


