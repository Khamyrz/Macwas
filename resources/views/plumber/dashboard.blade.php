@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Plumber Dashboard</h1>
        <p class="text-gray-600">Manage water connection jobs and customer assignments</p>
    </div>

    <!-- Notifications -->
    @if(auth()->user()->unreadNotifications->count() > 0)
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">New Notifications ({{ auth()->user()->unreadNotifications->count() }})</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach(auth()->user()->unreadNotifications->take(5) as $notification)
                    <div class="border border-blue-200 rounded-lg p-4 bg-blue-50">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-blue-900">
                                            New Customer Assignment
                                        </p>
                                        <p class="text-sm text-blue-700 mt-1">
                                            {{ $notification->data['message'] }}
                                        </p>
                                        <div class="mt-2 text-xs text-blue-600">
                                            <strong>Customer:</strong> {{ $notification->data['customer_name'] }} ({{ $notification->data['customer_number'] }})<br>
                                            <strong>Address:</strong> {{ $notification->data['customer_address'] }}<br>
                                            <strong>Phone:</strong> {{ $notification->data['customer_phone'] }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ml-4 flex-shrink-0">
                                <form action="{{ route('plumber.mark-notification-read', $notification->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        Mark as Read
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @if(auth()->user()->unreadNotifications->count() > 5)
                <div class="mt-4 text-center">
                    <a href="{{ route('plumber.notifications') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View All Notifications ({{ auth()->user()->unreadNotifications->count() }})
                    </a>
                </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Availability Status -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Availability Status</h3>
        </div>
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="p-3 rounded-full {{ auth()->user()->is_available ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Current Status</p>
                        <p class="text-2xl font-semibold {{ auth()->user()->is_available ? 'text-green-900' : 'text-red-900' }}">
                            {{ auth()->user()->is_available ? 'Available' : 'Unavailable' }}
                        </p>
                    </div>
                </div>
                <form action="{{ route('plumber.update-availability') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="is_available" value="{{ auth()->user()->is_available ? '0' : '1' }}">
                    <button type="submit" 
                            class="px-6 py-2 {{ auth()->user()->is_available ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 {{ auth()->user()->is_available ? 'focus:ring-red-500' : 'focus:ring-green-500' }}">
                        {{ auth()->user()->is_available ? 'Mark as Unavailable' : 'Mark as Available' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Pending Jobs -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Pending Jobs</h3>
        </div>
        <div class="p-6">
            @if($pendingConnections->count() > 0)
                <div class="space-y-4">
                    @foreach($pendingConnections as $connection)
                        <div class="border border-yellow-200 rounded-lg p-4 bg-yellow-50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12">
                                        @if($connection->customer->photo)
                                            <img class="h-12 w-12 rounded-full" src="{{ Storage::url($connection->customer->photo) }}" alt="">
                                        @else
                                            <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center">
                                                <span class="text-gray-600 font-medium">{{ substr($connection->customer->first_name, 0, 1) }}{{ substr($connection->customer->last_name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $connection->customer->full_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $connection->customer->address }}</div>
                                        <div class="text-sm text-gray-500">{{ $connection->customer->phone_number }}</div>
                                        <div class="text-sm text-gray-500">Assigned: {{ $connection->connection_date->format('M d, Y') }}</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mb-2">
                                        Pending
                                    </span>
                                    <form action="{{ route('plumber.start-job', $connection->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="block w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            Start Job
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No pending jobs</p>
            @endif
        </div>
    </div>

    <!-- In Progress Jobs -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">In Progress Jobs</h3>
        </div>
        <div class="p-6">
            @if($inProgressConnections->count() > 0)
                <div class="space-y-4">
                    @foreach($inProgressConnections as $connection)
                        <div class="border border-blue-200 rounded-lg p-4 bg-blue-50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12">
                                        @if($connection->customer->photo)
                                            <img class="h-12 w-12 rounded-full" src="{{ Storage::url($connection->customer->photo) }}" alt="">
                                        @else
                                            <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center">
                                                <span class="text-gray-600 font-medium">{{ substr($connection->customer->first_name, 0, 1) }}{{ substr($connection->customer->last_name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $connection->customer->full_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $connection->customer->address }}</div>
                                        <div class="text-sm text-gray-500">{{ $connection->customer->phone_number }}</div>
                                        <div class="text-sm text-gray-500">Started: {{ $connection->connection_date->format('M d, Y') }}</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mb-2">
                                        In Progress
                                    </span>
                                    <form action="{{ route('plumber.complete-job', $connection->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="block w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                            Complete Job
                                        </button>
                                    </form>
                                    <form action="{{ route('plumber.record-reading', $connection->customer_id) }}" method="POST" class="mt-2">
                                        @csrf
                                        <div class="grid grid-cols-2 gap-2 text-left">
                                            <input type="date" name="reading_date" class="border px-2 py-1 rounded" value="{{ now()->format('Y-m-d') }}">
                                            <input type="number" step="0.0001" name="present_reading" placeholder="Present" class="border px-2 py-1 rounded">
                                        </div>
                                        <button type="submit" class="mt-2 w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save Reading</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No jobs in progress</p>
            @endif
        </div>
    </div>

    <!-- Completed Jobs -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Recently Completed Jobs</h3>
        </div>
        <div class="p-6">
            @if($completedConnections->count() > 0)
                <div class="space-y-4">
                    @foreach($completedConnections->take(5) as $connection)
                        <div class="border border-green-200 rounded-lg p-4 bg-green-50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12">
                                        @if($connection->customer->photo)
                                            <img class="h-12 w-12 rounded-full" src="{{ Storage::url($connection->customer->photo) }}" alt="">
                                        @else
                                            <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center">
                                                <span class="text-gray-600 font-medium">{{ substr($connection->customer->first_name, 0, 1) }}{{ substr($connection->customer->last_name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $connection->customer->full_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $connection->customer->address }}</div>
                                        <div class="text-sm text-gray-500">Completed: {{ $connection->completion_date ? $connection->completion_date->format('M d, Y') : 'N/A' }}</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Completed
                                    </span>
                                    <div class="flex space-x-2">
                                        <button type="button" onclick="openMeterModal({{ $connection->customer_id }}, '{{ $connection->customer->full_name }}')" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            Set Meter
                                        </button>
                                        <a href="{{ route('plumber.receipt', $connection->customer_id) }}" target="_blank" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 text-center">
                                            Print Receipt
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($completedConnections->count() > 5)
                    <div class="mt-4 text-center">
                        <a href="{{ route('plumber.customer-history') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            View All Completed Jobs →
                        </a>
                    </div>
                @endif
            @else
                <p class="text-gray-500 text-center py-4">No completed jobs yet</p>
            @endif
        </div>
    </div>
    <!-- Meter Modal -->
    <div id="meterModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50" onclick="closeMeterModal()">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white z-50" onclick="event.stopPropagation()">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Set Meter for <span id="meterCustomerName" class="font-semibold"></span></h3>
                    <button onclick="closeMeterModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <form id="meterForm" method="POST" oninput="updateBillPreview()" onsubmit="handleMeterSubmission(event)">
                    @csrf
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reading Date</label>
                        <input id="meterReadingDate" type="date" name="reading_date" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Period</label>
                            <select id="meterPeriod" name="period" class="w-full px-3 py-2 border border-gray-300 rounded-md" disabled>
                                <option value="end" selected>30th</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Present Reading</label>
                            <input id="presentReading" type="number" step="0.0001" name="present_reading" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                        </div>
                    </div>
                    <div class="mt-3 p-3 bg-gray-50 border rounded text-sm">
                        <div><span class="font-medium">Usage (this entry):</span> <span id="usageEntry">0.0000</span> m³</div>
                        <div><span class="font-medium">Projected Bill:</span> ₱<span id="billPreview">0.00</span></div>
                        <div class="text-xs text-gray-500">Rule: first 10 m³ = ₱160; excess at ₱5.3333333333333/m³</div>
                    </div>
                    <div class="mt-4 flex justify-end space-x-2">
                        <button type="button" onclick="closeMeterModal()" class="px-4 py-2 text-sm bg-gray-200 rounded hover:bg-gray-300">Cancel</button>
                        <button type="submit" id="submitBtn" class="px-4 py-2 text-sm text-white bg-blue-600 rounded hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span id="submitText">Save & Print Receipt</span>
                            <span id="loadingText" class="hidden">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
    </div>

</div>

<script>
const RECORD_READING_BASE = "{{ url('/plumber/record-reading') }}";
const LAST_READING_BASE = "{{ url('/plumber/last-reading') }}";
const EXCESS_RATE = 5.3333333333333;
const BASE_ALLOWANCE = 10.0;
const BASE_CHARGE = 160.0;
function openMeterModal(customerId, name) {
    console.log('Opening meter modal for customer:', customerId, name);
    
    const modal = document.getElementById('meterModal');
    if (!modal) {
        console.error('Modal element not found');
        return;
    }
    
    document.getElementById('meterCustomerName').textContent = name;
    const form = document.getElementById('meterForm');
    form.action = `${RECORD_READING_BASE}/${customerId}`;
    form.reset();
    document.getElementById('meterReadingDate').value = new Date().toISOString().slice(0,10);
    modal.classList.remove('hidden');
    
    // Reset button state
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const loadingText = document.getElementById('loadingText');
    
    submitBtn.disabled = false;
    submitText.classList.remove('hidden');
    loadingText.classList.add('hidden');
    
    // Fixed to end-of-month (30th)
    document.getElementById('meterPeriod').value = 'end';
    
    // Prefill baseline from last reading
    fetch(`${LAST_READING_BASE}/${customerId}`)
        .then(r => {
            if (!r.ok) throw new Error('Network response was not ok');
            return r.json();
        })
        .then(data => {
            console.log('Last reading data:', data);
            window.__baseline = parseFloat(data.previous_reading || 0);
            document.getElementById('presentReading').value = '';
            updateBillPreview();
        })
        .catch(error => {
            console.warn('Failed to fetch last reading:', error);
            window.__baseline = 0;
            document.getElementById('presentReading').value = '';
            updateBillPreview();
        });
}
function closeMeterModal() {
    console.log('Closing meter modal');
    const modal = document.getElementById('meterModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}
function handlePeriodChange() {}
function updateBillPreview() {
    const prev = typeof window.__baseline === 'number' ? window.__baseline : 0;
    const pres = parseFloat(document.getElementById('presentReading').value) || 0;
    const used = Math.max(pres - prev, 0);
    document.getElementById('usageEntry').textContent = used.toFixed(4);
    // Calculate bill for this entry alone using the same rule
    let amount = 0;
    if (used <= 0) amount = 0;
    else if (used <= BASE_ALLOWANCE) amount = BASE_CHARGE;
    else amount = BASE_CHARGE + (used - BASE_ALLOWANCE) * EXCESS_RATE;
    document.getElementById('billPreview').textContent = amount.toFixed(2);
}
function handleMeterSubmission(event) {
    event.preventDefault();
    console.log('Handling meter submission');
    
    const form = event.target;
    const formData = new FormData(form);
    
    // Validate form data
    const presentReading = formData.get('present_reading');
    const readingDate = formData.get('reading_date');
    
    if (!presentReading || !readingDate) {
        alert('Please fill in all required fields.');
        return;
    }
    
    // Show loading state
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const loadingText = document.getElementById('loadingText');
    
    submitBtn.disabled = true;
    submitText.classList.add('hidden');
    loadingText.classList.remove('hidden');
    
    console.log('Submitting to:', form.action);
    console.log('Form data:', Object.fromEntries(formData));
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (response.ok) {
            return response.json();
        } else {
            return response.text().then(text => {
                throw new Error(`Server error: ${response.status} - ${text}`);
            });
        }
    })
    .then(data => {
        console.log('Success response:', data);
        
        // Close modal
        closeMeterModal();
        
        // Show success message
        alert('Meter reading saved successfully!');
        
        // Get customer ID from form action
        const customerId = form.action.split('/').pop();
        
        // Open receipt in new tab
        window.open(`{{ url('/plumber/receipt') }}/${customerId}`, '_blank');
        
        // Reload page to update the dashboard
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to save meter reading: ' + error.message);
        
        // Reset button state
        submitBtn.disabled = false;
        submitText.classList.remove('hidden');
        loadingText.classList.add('hidden');
    });
}

function toggleAvailability() {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("plumber.update-availability") }}';
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    
    const availabilityInput = document.createElement('input');
    availabilityInput.type = 'hidden';
    availabilityInput.name = 'is_available';
    availabilityInput.value = '{{ auth()->user()->is_available ? "0" : "1" }}';
    
    form.appendChild(csrfToken);
    form.appendChild(availabilityInput);
    document.body.appendChild(form);
    form.submit();
}
</script>

@endsection



