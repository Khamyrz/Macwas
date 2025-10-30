<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WaterBill;
use App\Models\Payment;
use App\Services\BillingService;
use App\Models\DisconnectionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountantController extends Controller
{
    public function dashboard(BillingService $billingService)
    {
        // Refresh bills for all customers with completed connections so accountant sees up-to-date amounts
        $customerIds = User::where('role', 'customer')->pluck('id');
        foreach ($customerIds as $customerId) {
            $billingService->recalculateCurrentBillForCustomer($customerId);
        }
        $customers = User::where('role', 'customer')->get();
        // Show only bills that have actual consumption (readings set)
        $unpaidBills = WaterBill::with('customer')
            ->where('status', '!=', 'paid')
            ->where('cubic_meters_used', '>', 0)
            ->get();

        // Proactively apply ₱20 late fee to overdue bills not yet applied, so accountants see accurate amounts
        $applyLateFee = function ($bills) {
            foreach ($bills as $bill) {
                if (now()->startOfDay()->gt($bill->due_date) && !$bill->late_fee_applied) {
                    $bill->late_fee += 20;
                    $bill->late_fee_applied = true;
                    $bill->calculateBalance();
                }
                // Ensure no negative balance is shown
                if ($bill->balance < 0) {
                    $bill->balance = 0;
                    $bill->save();
                }
            }
        };
        $applyLateFee($unpaidBills);
        
        return view('accountant.dashboard', compact('customers', 'unpaidBills'));
    }

    public function searchCustomers(Request $request)
    {
        $search = $request->get('search');
        
        $customers = User::where('role', 'customer')
            ->where(function($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('address', 'like', "%{$search}%");
            })
            ->with(['waterBills' => function($query) {
                $query->where('status', '!=', 'paid')
                      ->where('cubic_meters_used', '>', 0);
            }])
            ->get();

        // Ensure ₱20 late fee is reflected for overdue unpaid bills in search results
        foreach ($customers as $customer) {
            foreach ($customer->waterBills as $bill) {
                if (now()->startOfDay()->gt($bill->due_date) && !$bill->late_fee_applied) {
                    $bill->late_fee += 20;
                    $bill->late_fee_applied = true;
                    $bill->calculateBalance();
                }
                if ($bill->balance < 0) {
                    $bill->balance = 0;
                    $bill->save();
                }
            }
        }
            
        return response()->json($customers);
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'water_bill_id' => 'required|exists:water_bills,id',
            'amount_paid' => 'required|numeric|min:0.01',
            'payment_method' => 'nullable|string|in:cash',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $waterBill = WaterBill::findOrFail($request->water_bill_id);

        // Apply ₱20 penalty if after due date and not applied yet
        if (now()->startOfDay()->gt($waterBill->due_date) && !$waterBill->late_fee_applied) {
            $waterBill->late_fee += 20;
            $waterBill->late_fee_applied = true;
            $waterBill->save();
        }
        
        // Create payment record
        $payment = Payment::create([
            'water_bill_id' => $waterBill->id,
            'customer_id' => $waterBill->customer_id,
            'accountant_id' => Auth::id(),
            'amount_paid' => $request->amount_paid,
            'payment_method' => $request->payment_method ?? 'cash',
            'reference_number' => $request->reference_number,
            'notes' => $request->notes,
        ]);

        // Update water bill - require full payment
        if ($request->amount_paid < $waterBill->balance) {
            return response()->json([
                'success' => false,
                'message' => 'Payment must be equal to or greater than the total amount due (₱' . number_format($waterBill->balance, 2) . ')'
            ], 400);
        }
        
        $waterBill->amount_paid = $waterBill->total_amount + $waterBill->late_fee;
        $waterBill->status = 'paid';
        $waterBill->paid_date = now();
        $waterBill->balance = 0;
        $waterBill->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Payment processed successfully!',
            'new_balance' => $waterBill->balance,
            'status' => $waterBill->status,
            'receipt' => [
                'payment_id' => $payment->id,
                'date' => now()->toDateTimeString(),
                'customer_name' => $waterBill->customer->full_name,
                'accountant_name' => Auth::user()->full_name ?? 'Accountant',
                'billing_month' => $waterBill->billing_month->format('M Y'),
                'amount_paid' => (float) $payment->amount_paid,
                'remaining_balance' => (float) $waterBill->balance,
                'payment_method' => $payment->payment_method,
                'reference_number' => $payment->reference_number,
            ]
        ]);
    }

    public function requestDisconnection(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:users,id',
            'due_since' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $req = DisconnectionRequest::create([
            'customer_id' => $request->customer_id,
            'requested_by' => Auth::id(),
            'status' => 'pending',
            'due_since' => $request->due_since,
            'notes' => $request->notes,
        ]);

        return redirect()->back()->with('success', 'Disconnection request sent to admin.');
    }

    public function getCustomerBills($customerId)
    {
        $bills = WaterBill::where('customer_id', $customerId)
            ->orderBy('billing_month', 'desc')
            ->get();
            
        return response()->json($bills);
    }

    public function paymentHistory()
    {
        $payments = Payment::with(['waterBill.customer', 'customer'])
            ->where('accountant_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('accountant.payment-history', compact('payments'));
    }

    public function printReceipt($paymentId)
    {
        $payment = Payment::with(['waterBill.customer', 'accountant'])
            ->findOrFail($paymentId);
            
        return view('receipt', compact('payment'));
    }
}
