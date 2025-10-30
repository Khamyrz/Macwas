<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WaterBill;
use App\Models\WaterConnection;
use App\Models\Payment;
use App\Services\BillingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function dashboard(BillingService $billingService)
    {
        // Ensure current month's bill reflects consumption since completion date using current/changed rates
        $billingService->recalculateCurrentBillForCustomer(Auth::id());

        $currentBill = Auth::user()->fresh()->currentBill;
        $recentBills = Auth::user()->waterBills()
            ->orderBy('billing_month', 'desc')
            ->take(6)
            ->get();
            
        $waterConnection = Auth::user()->customerConnections()
            ->with('plumber')
            ->latest()
            ->first();
            
        $paymentHistory = Auth::user()->customerPayments()
            ->with('waterBill')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
            
        return view('customer.dashboard', compact(
            'currentBill',
            'recentBills',
            'waterConnection',
            'paymentHistory'
        ));
    }

    public function bills()
    {
        $bills = Auth::user()->waterBills()
            ->orderBy('billing_month', 'desc')
            ->paginate(12);
            
        return view('customer.bills', compact('bills'));
    }

    public function recentBills()
    {
        $recentBills = Auth::user()->waterBills()
            ->orderBy('billing_month', 'desc')
            ->take(10)
            ->get();
        return view('customer.recent-bills', compact('recentBills'));
    }

    public function paymentHistory()
    {
        $payments = Auth::user()->customerPayments()
            ->with('waterBill')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('customer.payment-history', compact('payments'));
    }

    public function recentPayments()
    {
        $paymentHistory = Auth::user()->customerPayments()
            ->with('waterBill')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        return view('customer.recent-payments', compact('paymentHistory'));
    }

    public function plumberInfo()
    {
        $connection = Auth::user()->customerConnections()
            ->with('plumber')
            ->latest()
            ->first();
            
        return view('customer.plumber-info', compact('connection'));
    }

    public function profile()
    {
        return view('customer.profile');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'age' => 'required|integer|min:18|max:120',
        ]);

        Auth::user()->update($request->only([
            'first_name', 'last_name', 'phone_number', 'address', 'age'
        ]));

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }
}

