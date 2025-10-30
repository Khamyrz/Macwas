<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WaterConnection;
use App\Models\WaterBill;
use App\Models\WaterRate;
use App\Services\BillingService;
use App\Models\MeterReading;
use App\Models\SetupRequest;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\BillUpdated;

class PlumberController extends Controller
{
    public function dashboard()
    {
        $pendingConnections = WaterConnection::with('customer')
            ->where('plumber_id', Auth::id())
            ->pending()
            ->get();
            
        $inProgressConnections = WaterConnection::with('customer')
            ->where('plumber_id', Auth::id())
            ->inProgress()
            ->get();
            
        $completedConnections = WaterConnection::with('customer')
            ->where('plumber_id', Auth::id())
            ->completed()
            ->get();
            
        return view('plumber.dashboard', compact(
            'pendingConnections',
            'inProgressConnections',
            'completedConnections'
        ));
    }

    public function startJob($connectionId)
    {
        $connection = WaterConnection::where('plumber_id', Auth::id())
            ->findOrFail($connectionId);
            
        $connection->update(['status' => 'in_progress']);
        
        return redirect()->back()->with('success', 'Job started successfully!');
    }

    public function completeJob($connectionId, BillingService $billingService)
    {
        $connection = WaterConnection::where('plumber_id', Auth::id())
            ->findOrFail($connectionId);
            
        $connection->markAsCompleted();

        // Mark any pending setup request for this customer as approved so it no longer shows in admin pending lists
        SetupRequest::where('customer_id', $connection->customer_id)
            ->where('status', 'pending')
            ->update(['status' => 'approved']);

        // Ensure a current-month bill exists for this customer once setup is completed
        $billingMonth = now()->format('Y-m-01');
        $dueDate = now()->addMonth()->format('Y-m-01');

        $existingBill = WaterBill::where('customer_id', $connection->customer_id)
            ->where('billing_month', $billingMonth)
            ->first();

        if (!$existingBill) {
            $currentRate = WaterRate::current()->first();
            if ($currentRate) {
                WaterBill::create([
                    'customer_id' => $connection->customer_id,
                    'cubic_meters_used' => 0,
                    'rate_per_cubic_meter' => $currentRate->rate_per_cubic_meter,
                    'total_amount' => 0,
                    'balance' => 0,
                    'billing_month' => $billingMonth,
                    'due_date' => $dueDate,
                    'status' => 'unpaid',
                ]);
            }
        }

        // Recalculate bill immediately so dashboards reflect consumption from completion date
        $billingService->recalculateCurrentBillForCustomer($connection->customer_id);
        
        // Mark plumber as available again
        Auth::user()->update(['is_available' => true]);
        
        return redirect()->back()->with('success', 'Job completed successfully!');
    }

    public function customerHistory()
    {
        $connections = WaterConnection::with('customer')
            ->where('plumber_id', Auth::id())
            ->completed()
            ->orderBy('completion_date', 'desc')
            ->get();
            
        return view('plumber.customer-history', compact('connections'));
    }

    public function recordReading(Request $request, $customerId)
    {
        $request->validate([
            'reading_date' => 'required|date',
            'present_reading' => 'required|numeric|min:0',
        ]);

        $present = (float) $request->present_reading;
        // Use the latest present reading as baseline; fallback to 0
        $previous = (float) (MeterReading::where('customer_id', $customerId)
            ->orderBy('reading_date', 'desc')
            ->value('present_reading') ?? 0);

        $used = max($present - $previous, 0);

        $reading = MeterReading::create([
            'customer_id' => $customerId,
            'plumber_id' => Auth::id(),
            'reading_date' => $request->reading_date,
            'previous_reading' => $previous,
            'present_reading' => $present,
            'used_cubic_meters' => $used,
        ]);

        // Update bill based on cumulative readings this month
        app(BillingService::class)->recalculateCurrentBillForCustomer($customerId);

        // Notify customer and accountants
        $bill = WaterBill::where('customer_id', $customerId)
            ->orderBy('billing_month', 'desc')
            ->first();
        $customer = User::find($customerId);
        if ($bill && $customer) {
            try { Mail::to($customer->email)->send(new BillUpdated($customer, $bill)); } catch (\Throwable $e) {}
            $accountantEmails = User::where('role', 'accountant')->pluck('email')->filter();
            if ($accountantEmails->count() > 0) {
                try { Mail::to($accountantEmails->all())->send(new BillUpdated($customer, $bill)); } catch (\Throwable $e) {}
            }
        }

        // Return JSON response for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Reading recorded and bill notifications sent.',
                'reading' => $reading,
                'bill' => $bill
            ]);
        }

        return redirect()->back()->with('success', 'Reading recorded and bill notifications sent.');
    }

    public function lastReading($customerId)
    {
        $last = MeterReading::where('customer_id', $customerId)
            ->orderBy('reading_date', 'desc')
            ->first();
        return response()->json([
            'previous_reading' => optional($last)->present_reading ?? 0,
            'reading_date' => optional($last)->reading_date,
        ]);
    }

    public function printBillReceipt($customerId)
    {
        // Use the most recent payment if exists; otherwise show a preview using current bill
        $payment = Payment::where('customer_id', $customerId)
            ->with(['waterBill.customer', 'accountant'])
            ->latest('created_at')
            ->first();

        if (!$payment) {
            $bill = WaterBill::where('customer_id', $customerId)
                ->with('customer')
                ->latest('billing_month')
                ->first();
            if (!$bill) {
                abort(404, 'No bill found for this customer');
            }
            // Create a transient payment-like object for preview
            $payment = new Payment([
                'customer_id' => $customerId,
                'amount_paid' => $bill->total_amount,
                'created_at' => now(),
            ]);
            $payment->setRelation('waterBill', $bill);
            $payment->setRelation('accountant', Auth::user());
        }

        return view('receipt', compact('payment'));
    }

    public function updateAvailability(Request $request)
    {
        $request->validate([
            'is_available' => 'required|boolean'
        ]);
        
        Auth::user()->update(['is_available' => $request->is_available]);
        
        return redirect()->back()->with('success', 'Availability updated successfully!');
    }

    public function markNotificationRead($notificationId)
    {
        $notification = Auth::user()->notifications()->findOrFail($notificationId);
        $notification->markAsRead();
        
        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    public function notifications()
    {
        $notifications = Auth::user()->notifications()->paginate(10);
        
        return view('plumber.notifications', compact('notifications'));
    }

    public function markAllNotificationsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        
        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
}

