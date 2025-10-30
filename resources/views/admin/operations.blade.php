@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Admin Operations</h1>
        <p class="text-gray-600">Setup Requests, Pending Water Connections, and Monthly Billing</p>
    </div>

    <!-- Setup Requests & Pending Water Connections -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Setup Requests & Pending Water Connections</h3>
        </div>
        <div class="p-6">
            @if(($setupRequests->count() + $pendingConnections->count()) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plumber / Request</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Connection Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($setupRequests as $request)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                    <span class="text-gray-600 font-medium">{{ substr($request->customer->first_name, 0, 1) }}{{ substr($request->customer->last_name, 0, 1) }}</span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $request->customer->full_name }}</div>
                                                <div class="text-sm text-gray-500">{{ $request->customer->address }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Setup Request</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">—</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <form method="POST" action="{{ route('admin.assign-plumber') }}" class="flex items-center space-x-2">
                                            @csrf
                                            <input type="hidden" name="customer_id" value="{{ $request->customer_id }}">
                                            <select name="plumber_id" class="border border-gray-300 rounded-md text-sm px-2 py-1" required>
                                                <option value="">Select Plumber</option>
                                                @foreach($availablePlumbers as $plumber)
                                                    <option value="{{ $plumber->id }}">{{ $plumber->full_name }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="px-3 py-1 rounded bg-blue-600 text-white text-xs hover:bg-blue-700">Assign</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            @foreach($pendingConnections as $connection)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                @if($connection->customer->photo)
                                                    <img class="h-10 w-10 rounded-full" src="{{ Storage::url($connection->customer->photo) }}" alt="">
                                                @else
                                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                        <span class="text-gray-600 font-medium">{{ substr($connection->customer->first_name, 0, 1) }}{{ substr($connection->customer->last_name, 0, 1) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $connection->customer->full_name }}</div>
                                                <div class="text-sm text-gray-500">{{ $connection->customer->address }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $connection->plumber->full_name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $connection->connection_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No setup requests or pending water connections</p>
            @endif
        </div>
    </div>

    
@endsection



