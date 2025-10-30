@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Notifications</h1>
        <p class="text-gray-600">Manage your notifications and assignments</p>
    </div>

    <!-- Mark All Read Button -->
    @if(auth()->user()->unreadNotifications->count() > 0)
    <div class="mb-6">
        <form action="{{ route('plumber.mark-all-notifications-read') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                Mark All as Read
            </button>
        </form>
    </div>
    @endif

    <!-- Notifications List -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">
                All Notifications ({{ auth()->user()->notifications->count() }})
            </h3>
        </div>
        <div class="p-6">
            @if($notifications->count() > 0)
                <div class="space-y-4">
                    @foreach($notifications as $notification)
                        <div class="border {{ $notification->read_at ? 'border-gray-200 bg-gray-50' : 'border-blue-200 bg-blue-50' }} rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            @if(!$notification->read_at)
                                                <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                                            @endif
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium {{ $notification->read_at ? 'text-gray-900' : 'text-blue-900' }}">
                                                New Customer Assignment
                                            </p>
                                            <p class="text-sm {{ $notification->read_at ? 'text-gray-600' : 'text-blue-700' }} mt-1">
                                                {{ $notification->data['message'] }}
                                            </p>
                                            <div class="mt-2 text-xs {{ $notification->read_at ? 'text-gray-500' : 'text-blue-600' }}">
                                                <strong>Customer:</strong> {{ $notification->data['customer_name'] }} ({{ $notification->data['customer_number'] }})<br>
                                                <strong>Address:</strong> {{ $notification->data['customer_address'] }}<br>
                                                <strong>Phone:</strong> {{ $notification->data['customer_phone'] }}
                                            </div>
                                            <div class="mt-2 text-xs text-gray-500">
                                                {{ $notification->created_at->format('M d, Y h:i A') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if(!$notification->read_at)
                                <div class="ml-4 flex-shrink-0">
                                    <form action="{{ route('plumber.mark-notification-read', $notification->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            Mark as Read
                                        </button>
                                    </form>
                                </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="mt-6">
                    {{ $notifications->links() }}
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No notifications found</p>
            @endif
        </div>
    </div>

    <!-- Back to Dashboard -->
    <div class="mt-6">
        <a href="{{ route('plumber.dashboard') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
            ← Back to Dashboard
        </a>
    </div>
</div>
@endsection