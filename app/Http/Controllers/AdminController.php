<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WaterRate;
use App\Models\WaterConnection;
use App\Models\WaterBill;
use App\Models\Payment;
use App\Models\SetupRequest;
use App\Models\OtpVerification;
use App\Mail\OtpMail;
use App\Mail\OverdueNotice;
use App\Mail\AccountRestored;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Models\DisconnectionRequest;
use Illuminate\Support\Facades\Schema;
use App\Notifications\PlumberAssignedNotification;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Get pending account approvals
        $pendingPlumbers = User::where('role', 'plumber')->where('status', 'pending')->count();
        $pendingAccountants = User::where('role', 'accountant')->where('status', 'pending')->count();
        $pendingCustomers = User::where('role', 'customer')->where('status', 'pending')->count();
        
        // Get current water rate
        $currentRate = WaterRate::current()->first();
        
        // Build database-agnostic month expression for grouping (SQLite/MySQL/Postgres)
        $driver = DB::getDriverName();
        if ($driver === 'sqlite') {
            $monthExpr = "strftime('%Y-%m', billing_month)";
        } elseif ($driver === 'pgsql') {
            $monthExpr = "to_char(billing_month, 'YYYY-MM')";
        } else {
            // mysql / mariadb
            $monthExpr = "DATE_FORMAT(billing_month, '%Y-%m')";
        }

        // Get monthly earnings data for chart
        $monthlyEarnings = WaterBill::selectRaw("$monthExpr as month, SUM(total_amount) as total")
            ->where('status', 'paid')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Get monthly water consumption data for chart
        $monthlyConsumption = WaterBill::selectRaw("$monthExpr as month, SUM(cubic_meters_used) as total_cubic_meters")
            ->groupBy('month')
            ->orderBy('month')
            ->get();
            
        // Get pending water connections
        $pendingConnections = WaterConnection::with(['customer', 'plumber'])->pending()->get();
        $setupRequests = SetupRequest::with('customer')->where('status', 'pending')->get();
        $disconnectionRequests = collect();
        if (Schema::hasTable('disconnection_requests')) {
            $disconnectionRequests = DisconnectionRequest::with(['customer'])->where('status', 'pending')->get();
        }
        
        // Get available plumbers
        $availablePlumbers = User::where('role', 'plumber')->where('is_available', true)->get();
        
        // Get total customers
        $totalCustomers = User::where('role', 'customer')->count();
        $totalPlumbers = User::where('role', 'plumber')->count();
        $totalAccountants = User::where('role', 'accountant')->count();
        
        return view('admin.dashboard', compact(
            'pendingPlumbers',
            'pendingAccountants',
            'currentRate',
            'monthlyEarnings',
            'monthlyConsumption',
            'pendingConnections',
            'availablePlumbers',
            'totalCustomers',
            'totalPlumbers',
            'totalAccountants',
            'pendingCustomers',
            'setupRequests',
            'disconnectionRequests'
        ));
    }

    public function operations()
    {
        // Mirror data required by dashboard for operations view
        $pendingConnections = WaterConnection::with(['customer', 'plumber'])->pending()->get();
        $setupRequests = SetupRequest::with('customer')->where('status', 'pending')->get();
        $availablePlumbers = User::where('role', 'plumber')->where('is_available', true)->get();

        return view('admin.operations', compact(
            'setupRequests',
            'pendingConnections',
            'availablePlumbers'
        ));
    }

    public function monthlyBillsPage()
    {
        // Provide context for the monthly bills page
        // Build database-agnostic month expression for grouping (SQLite/MySQL/Postgres)
        $driver = DB::getDriverName();
        if ($driver === 'sqlite') {
            $monthExpr = "strftime('%Y-%m', billing_month)";
        } elseif ($driver === 'pgsql') {
            $monthExpr = "to_char(billing_month, 'YYYY-MM')";
        } else {
            $monthExpr = "DATE_FORMAT(billing_month, '%Y-%m')";
        }

        $monthlyEarnings = WaterBill::selectRaw("$monthExpr as month, SUM(total_amount) as total")
            ->where('status', 'paid')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyConsumption = WaterBill::selectRaw("$monthExpr as month, SUM(cubic_meters_used) as total_cubic_meters")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Recent bills (last 12)
        $recentBills = WaterBill::orderBy('created_at', 'desc')->limit(12)->get();

        return view('admin.monthly-bills', compact('monthlyEarnings', 'monthlyConsumption', 'recentBills'));
    }
    
    public function assignDisconnection(Request $request)
    {
        $request->validate([
            'request_id' => 'required|exists:disconnection_requests,id',
            'plumber_id' => 'required|exists:users,id',
        ]);

        $req = DisconnectionRequest::findOrFail($request->request_id);
        $req->assigned_plumber_id = $request->plumber_id;
        $req->status = 'assigned';
        $req->save();

        return redirect()->back()->with('success', 'Disconnection assigned to plumber.');
    }

    public function pendingAccounts()
    {
        $pendingPlumbers = User::where('role', 'plumber')->where('status', 'pending')->get();
        $pendingAccountants = User::where('role', 'accountant')->where('status', 'pending')->get();
        $pendingCustomers = User::where('role', 'customer')->where('status', 'pending')->get();

        return view('admin.pending-accounts', compact('pendingPlumbers', 'pendingAccountants', 'pendingCustomers'));
    }

    public function approveAccount(Request $request, $id)
    {
        $user = User::findOrFail($id);
        // Activate account but DO NOT auto-verify email
        $user->update([
            'status' => 'active',
        ]);

        // Send OTP so user must verify on first login
        try {
            $otp = OtpVerification::generateOtp($user->id, 'login');
            Mail::to($user->email)->send(new OtpMail($user, $otp->otp_code, 'login'));
        } catch (\Exception $e) {
            \Log::error('Failed to send OTP on approval: '.$e->getMessage());
        }

        return redirect()->back()->with('success', 'Account approved. OTP sent to the user; they must verify on first login.');
    }

    public function rejectAccount(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'inactive']);
        
        return redirect()->back()->with('success', 'Account rejected successfully!');
    }

    public function waterRates()
    {
        $rates = WaterRate::orderBy('effective_date', 'desc')->get();
        return view('admin.water-rates', compact('rates'));
    }

    public function setWaterRate(Request $request)
    {
        $request->validate([
            'rate_per_cubic_meter' => 'required|numeric|min:0',
            'effective_date' => 'required|date',
        ]);

        // Deactivate current rate
        WaterRate::where('is_active', true)->update(['is_active' => false]);

        // Create new rate
        WaterRate::create([
            'rate_per_cubic_meter' => $request->rate_per_cubic_meter,
            'effective_date' => $request->effective_date,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Water rate updated successfully!');
    }

    public function assignPlumber(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:users,id',
            'plumber_id' => 'required|exists:users,id',
        ]);

        WaterConnection::create([
            'customer_id' => $request->customer_id,
            'plumber_id' => $request->plumber_id,
            'status' => 'pending',
            'connection_date' => now(),
        ]);

        // Mark plumber as unavailable
        User::find($request->plumber_id)->update(['is_available' => false]);

        return redirect()->back()->with('success', 'Plumber assigned successfully!');
    }

    public function userRecords($role)
    {
        try {
            $query = User::where('role', $role);
            
            // Only filter by deleted_at if the column exists
            try {
                if (Schema::hasColumn('users', 'deleted_at')) {
                    $query->whereNull('deleted_at');
                }
            } catch (\Exception $e) {
                // Column doesn't exist or schema check failed, continue without filter
            }
            
            $users = $query->orderBy('created_at', 'asc')->paginate(10);
        return view('admin.user-records', compact('users', 'role'));
        } catch (\Exception $e) {
            \Log::error('Error in userRecords: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while loading user records.');
        }
    }

    public function searchUsers(Request $request, $role)
    {
        $searchTerm = $request->get('q', '');
        
        $users = User::where('role', $role)
            ->where(function($query) use ($searchTerm) {
                $query->where('first_name', 'like', "%{$searchTerm}%")
                      ->orWhere('last_name', 'like', "%{$searchTerm}%")
                      ->orWhere('email', 'like', "%{$searchTerm}%")
                      ->orWhere('phone_number', 'like', "%{$searchTerm}%")
                      ->orWhere('address', 'like', "%{$searchTerm}%")
                      ->orWhere('customer_number', 'like', "%{$searchTerm}%");
            })
            ->get();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'users' => $users,
                'html' => view('admin.user-records-partial', compact('users', 'role'))->render()
            ]);
        }

        return view('admin.user-records', compact('users', 'role'));
    }

    public function generateMonthlyBills()
    {
        // Only generate bills for customers with completed water connections
        $eligibleCustomerIds = WaterConnection::completed()
            ->pluck('customer_id')
            ->unique()
            ->values();

        $customers = User::where('role', 'customer')
            ->whereIn('id', $eligibleCustomerIds)
            ->get();
        $currentRate = WaterRate::current()->first();
        
        if (!$currentRate) {
            return redirect()->back()->with('error', 'No active water rate found!');
        }

        $billingMonth = now()->format('Y-m-01');
        $dueDate = now()->addMonth()->format('Y-m-01');

        foreach ($customers as $customer) {
            // Check if bill already exists for this month
            $existingBill = WaterBill::where('customer_id', $customer->id)
                ->where('billing_month', $billingMonth)
                ->first();

            if (!$existingBill) {
                // Generate random consumption for demo (in real app, this would come from meter readings)
                $cubicMeters = rand(5, 50);
                $totalAmount = $cubicMeters * $currentRate->rate_per_cubic_meter;

                WaterBill::create([
                    'customer_id' => $customer->id,
                    'cubic_meters_used' => $cubicMeters,
                    'rate_per_cubic_meter' => $currentRate->rate_per_cubic_meter,
                    'total_amount' => $totalAmount,
                    'balance' => $totalAmount,
                    'billing_month' => $billingMonth,
                    'due_date' => $dueDate,
                    'status' => 'unpaid',
                ]);
            }
        }

        return redirect()->back()->with('success', 'Monthly bills generated successfully!');
    }

    public function sendOverdueNotices()
    {
        $overdueBills = WaterBill::overdue()
            ->with('customer')
            ->get()
            ->groupBy('customer_id');

        if ($overdueBills->isEmpty()) {
            return redirect()->back()->with('info', 'No overdue customers to notify right now.');
        }

        $sentCount = 0;

        foreach ($overdueBills as $customerBills) {
            $customer = $customerBills->first()->customer;

            if (!$customer || !$customer->email) {
                continue;
            }

            try {
                Mail::to($customer->email)->send(new OverdueNotice($customer, $customerBills));
                $sentCount++;
            } catch (\Throwable $e) {
                \Log::error('Failed to send overdue notice to '.$customer->email.': '.$e->getMessage());
            }
        }

        if ($sentCount === 0) {
            return redirect()->back()->with('info', 'Unable to send notices. Please verify customer email addresses.');
        }

        return redirect()->back()->with('success', "Sent overdue reminders to {$sentCount} customer(s).");
    }

    // Admin-created accounts
    public function createUser()
    {
        return view('admin.create-user');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'role' => 'required|in:customer,plumber,accountant',
            'first_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/', 'not_regex:/[0-9]/'],
            'last_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/', 'not_regex:/[0-9]/'],
            'email' => 'required|email|unique:users,email',
            'age' => 'required|integer|min:18|max:120',
            'address' => 'required|string|max:500',
            'phone_number' => 'required|string|max:20',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[A-Z]/', // Must contain at least one uppercase letter
                'regex:/[^a-zA-Z0-9]/', // Must contain at least one special character (non-alphanumeric)
            ],
            'assigned_plumber_id' => 'nullable|exists:users,id',
        ], [
            'first_name.regex' => 'First name must contain only letters and spaces.',
            'first_name.not_regex' => 'First name cannot contain numbers.',
            'last_name.regex' => 'Last name must contain only letters and spaces.',
            'last_name.not_regex' => 'Last name cannot contain numbers.',
            'password.regex' => 'Password must contain at least one uppercase letter and one special character.',
            'password.min' => 'Password must be at least 8 characters long.',
        ]);

        $userData = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'plain_password' => $request->password,
            'role' => $request->role,
            'age' => $request->age,
            'phone_number' => $request->phone_number,
            'national_id' => 'AUTO-'.uniqid(),
            'address' => $request->address,
            'status' => 'active',
            'is_available' => $request->role === 'plumber',
            'admin_created' => true, // Mark as admin-created account
        ];

        // Only assign customer number to customers
        if ($request->role === 'customer') {
            $userData['customer_number'] = User::generateCustomerNumber();
        }

        $user = User::create($userData);

        // Send OTP verification for admin-created accounts
        $otp = OtpVerification::generateOtp($user->id, 'login');
        
        try {
            Mail::to($user->email)->send(new OtpMail($user, $otp->otp_code, 'login'));
        } catch (\Exception $e) {
            \Log::error('Failed to send OTP email: ' . $e->getMessage());
        }

        $message = ucfirst($request->role).' account created. User must verify OTP on first login before accessing the dashboard.';
        if ($request->role === 'customer') {
            $message .= ' with Customer Number: ' . $user->customer_number;
            $message .= '. Customer can login using their email or customer number.';
        } else {
            $message .= '. ' . ucfirst($request->role) . ' can login using their email and password.';
        }

        // Handle plumber assignment for customers
        if ($request->role === 'customer') {
            $plumber = null;
            
            // If plumber is manually assigned, use it
            if ($request->assigned_plumber_id) {
                $plumber = User::findOrFail($request->assigned_plumber_id);
                
                // Verify the assigned user is actually a plumber
                if ($plumber->role !== 'plumber') {
                    $user->delete(); // Rollback user creation
                    return redirect()->back()->withErrors(['assigned_plumber_id' => 'Selected user is not a plumber.'])->withInput();
                }
                
                // Verify plumber is active
                if ($plumber->status !== 'active') {
                    $user->delete(); // Rollback user creation
                    return redirect()->back()->withErrors(['assigned_plumber_id' => 'Selected plumber is not active.'])->withInput();
                }
            } else {
                // Auto-assign an available plumber (free/available)
                $plumber = User::where('role', 'plumber')
                    ->where('status', 'active')
                    ->where('is_available', true)
                    ->first();
                
                if ($plumber) {
                    // Mark plumber as unavailable
                    $plumber->update(['is_available' => false]);
                }
            }
            
            // Create water connection if plumber is assigned
            if ($plumber) {
                $waterConnection = WaterConnection::create([
                    'customer_id' => $user->id,
                    'plumber_id' => $plumber->id,
                    'status' => 'pending',
                    'connection_date' => now(),
                    'notes' => $request->assigned_plumber_id ? 'Customer created with plumber assignment' : 'Customer created with auto-assigned plumber',
                ]);

                // Send notification to plumber
                $plumber->notify(new PlumberAssignedNotification($user, $plumber));

                $message .= ' and assigned to plumber ' . $plumber->full_name . '. The plumber has been notified.';
            } else {
                $message .= '. No available plumber found for auto-assignment.';
            }
        }

        $redirectData = [
            'id' => $user->id,
            'name' => $user->full_name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'role' => $user->role,
            'password' => $user->plain_password,
        ];

        // Only include customer number for customers
        if ($request->role === 'customer') {
            $redirectData['customer_number'] = $user->customer_number;
        }

        return redirect()->route('admin.user-records', ['role' => $request->role])
            ->with('success', $message)
            ->with('created_user', $redirectData);
    }

    public function showUser($id)
    {
        $user = User::findOrFail($id);
        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'first_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/', 'not_regex:/[0-9]/'],
            'last_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/', 'not_regex:/[0-9]/'],
            'email' => 'required|email|unique:users,email,' . $id,
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'status' => 'required|in:active,pending,inactive',
            'password' => [
                'nullable',
                'string',
                'min:8',
                'regex:/[A-Z]/', // Must contain at least one uppercase letter
                'regex:/[^a-zA-Z0-9]/', // Must contain at least one special character (non-alphanumeric)
            ],
        ], [
            'first_name.regex' => 'First name must contain only letters and spaces.',
            'first_name.not_regex' => 'First name cannot contain numbers.',
            'last_name.regex' => 'Last name must contain only letters and spaces.',
            'last_name.not_regex' => 'Last name cannot contain numbers.',
            'password.regex' => 'Password must contain at least one uppercase letter and one special character.',
            'password.min' => 'Password must be at least 8 characters long.',
        ]);

        $updateData = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'status' => $request->status,
        ];

        // Update password if provided
        if ($request->password) {
            $updateData['password'] = Hash::make($request->password);
            $updateData['plain_password'] = $request->password;
        }

        $user->update($updateData);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'User updated successfully!'
            ]);
        }

        return redirect()->back()->with('success', 'User updated successfully!');
    }

    public function destroyUser($id)
    {
        try {
        $user = User::findOrFail($id);
        
        // Prevent deletion of admin users
        if ($user->role === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete admin users.'
            ], 403);
        }

            // Check if soft deletes are available
            try {
                if (Schema::hasColumn('users', 'deleted_at')) {
                    // Soft delete the user (moves to delete history)
                    $user->delete();
                    $message = 'User deleted successfully! Account moved to delete history.';
                } else {
                    // Fallback to hard delete if column doesn't exist
                    $user->waterBills()->delete();
                    $user->payments()->delete();
                    $user->customerPayments()->delete();
                    $user->waterConnections()->delete();
                    $user->customerConnections()->delete();
                    $user->forceDelete();
                    $message = 'User deleted successfully!';
                }
            } catch (\Exception $e) {
                // If schema check fails, use hard delete
        $user->waterBills()->delete();
        $user->payments()->delete();
        $user->customerPayments()->delete();
        $user->waterConnections()->delete();
        $user->customerConnections()->delete();
                $user->forceDelete();
                $message = 'User deleted successfully!';
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in destroyUser: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the user.'
            ], 500);
        }
    }

    public function deleteHistory($role)
    {
        try {
            // Check if soft deletes are available
            try {
                if (!Schema::hasColumn('users', 'deleted_at')) {
                    return response()->json([
                        'success' => true,
                        'users' => [],
                        'message' => 'Soft delete feature not available. Please run migrations.'
                    ]);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'success' => true,
                    'users' => [],
                    'message' => 'Soft delete feature not available.'
                ]);
            }

            $deletedUsers = User::onlyTrashed()
                ->where('role', $role)
                ->orderBy('deleted_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'users' => $deletedUsers
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in deleteHistory: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'users' => [],
                'message' => 'An error occurred while loading delete history.'
            ], 500);
        }
    }

    public function restoreUser($id)
    {
        // Check if soft deletes are available
        if (!Schema::hasColumn('users', 'deleted_at')) {
            return response()->json([
                'success' => false,
                'message' => 'Soft delete feature not available. Please run migrations.'
            ], 400);
        }

        $user = User::onlyTrashed()->findOrFail($id);
        
        // Restore the user
        $user->restore();

        // Send notification email
        try {
            Mail::to($user->email)->send(new AccountRestored($user));
        } catch (\Exception $e) {
            // Log error but don't fail the restore operation
            \Log::error('Failed to send account restored email: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'User restored successfully! Notification email sent.'
        ]);
    }

    public function clearUser($id)
    {
        // Check if soft deletes are available
        if (!Schema::hasColumn('users', 'deleted_at')) {
            return response()->json([
                'success' => false,
                'message' => 'Soft delete feature not available. Please run migrations.'
            ], 400);
        }

        $user = User::onlyTrashed()->findOrFail($id);
        
        // Prevent permanent deletion of admin users
        if ($user->role === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot permanently delete admin users.'
            ], 403);
        }

        // Permanently delete related records
        $user->waterBills()->forceDelete();
        $user->payments()->forceDelete();
        $user->customerPayments()->forceDelete();
        $user->waterConnections()->forceDelete();
        $user->customerConnections()->forceDelete();
        
        // Permanently delete the user
        $user->forceDelete();

        return response()->json([
            'success' => true,
            'message' => 'User permanently deleted and all credentials cleared.'
        ]);
    }
}
