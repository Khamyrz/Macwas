@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Accountant Dashboard</h1>
        <p class="text-gray-600">Process payments and manage customer bills</p>
    </div>

    <!-- Search Section -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Search Customers</h3>
        </div>
        <div class="p-6">
            <div class="flex gap-4">
                <div class="flex-1">
                    <input type="text" id="searchInput" placeholder="Search by name or address..." 
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                <button onclick="searchCustomers()" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Search
                </button>
            </div>
        </div>
    </div>

    <!-- Search Results -->
    <div id="searchResults" class="bg-white rounded-lg shadow mb-8" style="display: none;">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Search Results</h3>
        </div>
        <div class="p-6">
            <div id="customersList"></div>
        </div>
    </div>

    <!-- Unpaid Bills -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Unpaid Bills (with ₱20 late fee for overdue bills)</h3>
        </div>
        <div class="p-6">
            @if($unpaidBills->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Billing Month</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount Due</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($unpaidBills as $bill)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                @if($bill->customer->photo)
                                                    <img class="h-10 w-10 rounded-full" src="{{ Storage::url($bill->customer->photo) }}" alt="">
                                                @else
                                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                        <span class="text-gray-600 font-medium">{{ substr($bill->customer->first_name, 0, 1) }}{{ substr($bill->customer->last_name, 0, 1) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $bill->customer->full_name }}</div>
                                                <div class="text-sm text-gray-500">{{ $bill->customer->address }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $bill->billing_month->format('M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-600">
                                        ₱{{ number_format($bill->total_amount + $bill->late_fee, 2) }}
                                        @if($bill->late_fee > 0)
                                            <span class="text-xs text-orange-600 block">(includes ₱{{ number_format($bill->late_fee, 2) }} late fee)</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $bill->due_date->format('M d, Y') }}
                                        @if($bill->isOverdue())
                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Overdue
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="openPaymentModal({{ $bill->id }}, '{{ $bill->customer->full_name }}', {{ $bill->total_amount + $bill->late_fee }})" 
                                                class="text-blue-600 hover:text-blue-900">Process Full Payment</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No unpaid bills found</p>
            @endif
        </div>
    </div>

</div>

<!-- Payment Modal -->
<div id="paymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Process Payment</h3>
                <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="paymentForm">
                @csrf
                <input type="hidden" id="waterBillId" name="water_bill_id">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Customer</label>
                    <p id="customerName" class="text-gray-900 font-medium"></p>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Amount Due</label>
                    <p id="amountDue" class="text-red-600 font-bold text-lg"></p>
                </div>
                
                <div class="mb-4">
                    <label for="amountPaid" class="block text-sm font-medium text-gray-700 mb-2">Amount to Pay (Full Payment Required)</label>
                    <input type="number" id="amountPaid" name="amount_paid" step="0.01" min="0" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                           oninput="validateFullPayment()" onkeyup="validateFullPayment()" onchange="validateFullPayment()">
                    <p class="text-xs text-gray-500 mt-1">Must pay the full amount due</p>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                    <input type="text" value="Cash" readonly class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-700">
                    <input type="hidden" id="paymentMethod" name="payment_method" value="cash">
                </div>
                
                <div class="mb-4">
                    <label for="referenceNumber" class="block text-sm font-medium text-gray-700 mb-2">Reference Number (Optional)</label>
                    <input type="text" id="referenceNumber" name="reference_number" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div class="mb-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                    <textarea id="notes" name="notes" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closePaymentModal()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                        Cancel
                    </button>
                    <button type="submit" id="submitPaymentBtn" 
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                        Process Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Receipt Modal -->
<div id="receiptModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-10 mx-auto p-5 border w-[52rem] max-w-full shadow-lg rounded-md bg-white">
        <div class="mt-2">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Payment Receipt</h3>
                <button onclick="closeReceiptModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="receiptContent" class="text-sm text-gray-800">
                <!-- Filled dynamically -->
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button id="printReceiptBtn" onclick="printReceipt()" disabled class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed">Print Receipt</button>
                <button onclick="closeReceiptModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function searchCustomers() {
    const searchTerm = document.getElementById('searchInput').value;
    if (!searchTerm.trim()) return;
    
    fetch(`/accountant/search-customers?search=${encodeURIComponent(searchTerm)}`)
        .then(response => response.json())
        .then(customers => {
            displaySearchResults(customers);
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function displaySearchResults(customers) {
    const resultsDiv = document.getElementById('searchResults');
    const customersListDiv = document.getElementById('customersList');
    
    if (customers.length === 0) {
        customersListDiv.innerHTML = '<p class="text-gray-500 text-center py-4">No customers found</p>';
    } else {
        let html = '<div class="space-y-4">';
        customers.forEach(customer => {
            const hasUnpaidBills = customer.water_bills && customer.water_bills.length > 0;
            html += `
                <div class="border rounded-lg p-4 ${hasUnpaidBills ? 'border-red-200 bg-red-50' : 'border-gray-200'}">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-12 w-12">
                                ${customer.photo ? 
                                    `<img class="h-12 w-12 rounded-full" src="/storage/${customer.photo}" alt="">` :
                                    `<div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-gray-600 font-medium">${customer.first_name.charAt(0)}${customer.last_name.charAt(0)}</span>
                                    </div>`
                                }
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">${customer.first_name} ${customer.last_name}</div>
                                <div class="text-sm text-gray-500">${customer.address}</div>
                                <div class="text-sm text-gray-500">${customer.phone_number}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            ${hasUnpaidBills ? 
                                `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Has Unpaid Bills</span>` :
                                `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">All Bills Paid</span>`
                            }
                        </div>
                    </div>
                    ${hasUnpaidBills ? `
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Unpaid Bills:</h4>
                            ${customer.water_bills.map(bill => `
                                <div class="flex justify-between items-center text-sm">
                                    <span>${new Date(bill.billing_month).toLocaleDateString('en-US', {month: 'short', year: 'numeric'})}</span>
                                    <span class="font-medium text-red-600">₱${(parseFloat(bill.total_amount) + parseFloat(bill.late_fee)).toFixed(2)}</span>
                                    <button onclick="openPaymentModal(${bill.id}, '${customer.first_name} ${customer.last_name}', ${bill.total_amount + bill.late_fee})" 
                                            class="text-blue-600 hover:text-blue-800 text-sm font-medium">Process Full Payment</button>
                                </div>
                            `).join('')}
                        </div>
                    ` : ''}
                </div>
            `;
        });
        html += '</div>';
        customersListDiv.innerHTML = html;
    }
    
    resultsDiv.style.display = 'block';
}

function openPaymentModal(billId, customerName, amountDue) {
    document.getElementById('waterBillId').value = billId;
    document.getElementById('customerName').textContent = customerName;
    document.getElementById('amountDue').textContent = '₱' + parseFloat(amountDue).toFixed(2);
    document.getElementById('amountPaid').value = '';
    document.getElementById('paymentMethod').value = 'cash';
    document.getElementById('referenceNumber').value = '';
    document.getElementById('notes').value = '';
    
    // Reset button states
    document.getElementById('submitPaymentBtn').disabled = true;
    document.getElementById('printReceiptBtn').disabled = true;
    
    document.getElementById('paymentModal').classList.remove('hidden');
    validateFullPayment();
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
}

function validateFullPayment() {
    const amountDue = parseFloat(document.getElementById('amountDue').textContent.replace('₱', ''));
    const amountPaid = parseFloat(document.getElementById('amountPaid').value) || 0;
    const submitBtn = document.getElementById('submitPaymentBtn');
    
    // Enable only if payment equals the full amount due
    const isValidPayment = amountPaid === amountDue && amountPaid > 0;
    submitBtn.disabled = !isValidPayment;
    
    // Visual feedback
    const amountInput = document.getElementById('amountPaid');
    if (amountPaid > 0) {
        if (isValidPayment) {
            amountInput.className = 'w-full px-3 py-2 border border-green-300 rounded-md focus:ring-green-500 focus:border-green-500 bg-green-50';
        } else {
            amountInput.className = 'w-full px-3 py-2 border border-red-300 rounded-md focus:ring-red-500 focus:border-red-500 bg-red-50';
        }
    } else {
        amountInput.className = 'w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500';
    }
}

document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();

    // Enforce: payment must equal the full amount due
    const amountDue = parseFloat(document.getElementById('amountDue').textContent.replace('₱', ''));
    const amountPaid = parseFloat(document.getElementById('amountPaid').value) || 0;
    if (amountPaid !== amountDue || amountPaid <= 0) {
        alert('Payment must equal the full amount due (₱' + amountDue.toFixed(2) + ').');
        return;
    }
    
    const formData = new FormData(this);
    
    fetch('/accountant/process-payment', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show receipt modal
            const r = data.receipt;
            // Store last processed payment id for printing via server-rendered receipt
            window.lastPaymentId = r.payment_id;
            const html = `
                <div id="receiptPrintable">
                    <iframe src="/accountant/receipt/${r.payment_id}" style="width:100%;height:640px;border:0;border-radius:.5rem;background:#fff;"></iframe>
                </div>`;
            document.getElementById('receiptContent').innerHTML = html;
            closePaymentModal();
            openReceiptModal();
            // Enable printing after success
            document.getElementById('printReceiptBtn').disabled = false;
        } else {
            alert('Error processing payment: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error processing payment. Please try again.');
    });
});

// Close modal when clicking outside
document.getElementById('paymentModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePaymentModal();
    }
});

function openReceiptModal() {
    document.getElementById('receiptModal').classList.remove('hidden');
}

function closeReceiptModal() {
    document.getElementById('receiptModal').classList.add('hidden');
}

function printReceipt() {
    const paymentId = window.lastPaymentId;
    if (!paymentId) {
        alert('No receipt available to print. Please process a payment first.');
        return;
    }
    window.open(`/accountant/receipt/${paymentId}?print=1`, '_blank', 'height=600,width=420');
}
</script>
@endsection

