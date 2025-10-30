<?php

namespace App\Http\Controllers;

use App\Models\LoginAttempt;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class LoginMonitoringController extends Controller
{
    /**
     * Display the login monitoring dashboard
     */
    public function index(): View
    {
        return view('admin.monitor');
    }

    /**
     * Get login attempts data for the monitoring dashboard
     */
    public function getLoginAttempts(Request $request): JsonResponse
    {
        $query = LoginAttempt::query();

        // Apply filters
        if ($request->has('date_from')) {
            $query->where('attempted_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to')) {
            $query->where('attempted_at', '<=', $request->date_to);
        }
        
        if ($request->has('status')) {
            $query->where('success', $request->status === 'success');
        }
        
        if ($request->has('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        // Get paginated results
        $attempts = $query->orderBy('attempted_at', 'desc')
            ->paginate(50);

        // Transform data for frontend
        $transformedAttempts = $attempts->map(function ($attempt) {
            $hasPrecise = !is_null($attempt->latitude) && !is_null($attempt->longitude);
            $coords = $hasPrecise
                ? ['lat' => (float) $attempt->latitude, 'lng' => (float) $attempt->longitude, 'accuracy' => 'precise']
                : $this->getCoordinatesFromIP($attempt->ip_address);
            return [
                'id' => $attempt->id,
                'email' => $attempt->email,
                'ip_address' => $attempt->ip_address,
                'location' => $this->getLocationFromIP($attempt->ip_address),
                'success' => $attempt->success,
                'status' => $attempt->success ? 'Success' : 'Failed',
                'status_class' => $attempt->success ? 'success' : 'danger',
                'attempted_at' => $attempt->attempted_at->format('Y-m-d H:i:s'),
                'attempted_at_human' => $attempt->attempted_at->diffForHumans(),
                'user_agent' => $attempt->user_agent,
                'lockout_until' => $attempt->lockout_until ? $attempt->lockout_until->format('Y-m-d H:i:s') : null,
                'coordinates' => $coords,
                'attempts_count' => LoginAttempt::where('email', $attempt->email)
                    ->where('ip_address', $attempt->ip_address)
                    ->count()
            ];
        });

        return response()->json([
            'attempts' => $transformedAttempts,
            'pagination' => [
                'current_page' => $attempts->currentPage(),
                'last_page' => $attempts->lastPage(),
                'per_page' => $attempts->perPage(),
                'total' => $attempts->total(),
                'from' => $attempts->firstItem(),
                'to' => $attempts->lastItem(),
            ],
            'stats' => $this->getStats()
        ]);
    }

    /**
     * Get statistics for the monitoring dashboard
     */
    public function getStats(): array
    {
        $totalAttempts = LoginAttempt::count();
        $successfulAttempts = LoginAttempt::where('success', true)->count();
        $failedAttempts = LoginAttempt::where('success', false)->count();
        $lockedAttempts = LoginAttempt::whereNotNull('lockout_until')->count();
        
        $todayAttempts = LoginAttempt::whereDate('attempted_at', today())->count();
        $todayFailed = LoginAttempt::whereDate('attempted_at', today())->where('success', false)->count();
        
        $uniqueIPs = LoginAttempt::distinct('ip_address')->count();
        $uniqueEmails = LoginAttempt::distinct('email')->count();

        return [
            'total_attempts' => $totalAttempts,
            'successful_attempts' => $successfulAttempts,
            'failed_attempts' => $failedAttempts,
            'locked_attempts' => $lockedAttempts,
            'success_rate' => $totalAttempts > 0 ? round(($successfulAttempts / $totalAttempts) * 100, 2) : 0,
            'failure_rate' => $totalAttempts > 0 ? round(($failedAttempts / $totalAttempts) * 100, 2) : 0,
            'today_attempts' => $todayAttempts,
            'today_failed' => $todayFailed,
            'unique_ips' => $uniqueIPs,
            'unique_emails' => $uniqueEmails,
        ];
    }

    /**
     * Get location information from IP address
     */
    private function getLocationFromIP(string $ip): array
    {
        // For demo purposes, we'll use a simple IP geolocation service
        // In production, you might want to use a more reliable service like MaxMind GeoIP
        
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return [
                'country' => 'Local',
                'city' => 'Localhost',
                'region' => 'Development',
                'timezone' => 'UTC'
            ];
        }

        try {
            // Using ipapi.co (free tier available)
            $response = file_get_contents("http://ipapi.co/{$ip}/json/");
            $data = json_decode($response, true);
            
            if ($data && !isset($data['error'])) {
                return [
                    'country' => $data['country_name'] ?? 'Unknown',
                    'city' => $data['city'] ?? 'Unknown',
                    'region' => $data['region'] ?? 'Unknown',
                    'timezone' => $data['timezone'] ?? 'UTC',
                    'isp' => $data['org'] ?? 'Unknown'
                ];
            }
        } catch (\Exception $e) {
            // Log error but don't break the flow
            \Log::warning('Failed to get location for IP: ' . $ip . ' - ' . $e->getMessage());
        }

        return [
            'country' => 'Unknown',
            'city' => 'Unknown',
            'region' => 'Unknown',
            'timezone' => 'UTC'
        ];
    }

    /**
     * Get coordinates from IP address for mapping
     */
    private function getCoordinatesFromIP(string $ip): array
    {
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return [
                'lat' => 0,
                'lng' => 0,
                'accuracy' => 'localhost'
            ];
        }

        try {
            $response = file_get_contents("http://ipapi.co/{$ip}/json/");
            $data = json_decode($response, true);
            
            if ($data && !isset($data['error']) && isset($data['latitude'], $data['longitude'])) {
                return [
                    'lat' => (float) $data['latitude'],
                    'lng' => (float) $data['longitude'],
                    'accuracy' => $data['accuracy'] ?? 'city'
                ];
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to get coordinates for IP: ' . $ip . ' - ' . $e->getMessage());
        }

        return [
            'lat' => 0,
            'lng' => 0,
            'accuracy' => 'unknown'
        ];
    }

    /**
     * Clear old login attempts (cleanup)
     */
    public function clearOldAttempts(Request $request): JsonResponse
    {
        $days = $request->input('days', 30);
        $deleted = LoginAttempt::where('attempted_at', '<', now()->subDays($days))->delete();
        
        return response()->json([
            'success' => true,
            'message' => "Cleared {$deleted} login attempts older than {$days} days.",
            'deleted_count' => $deleted
        ]);
    }

    /**
     * Export login attempts data
     */
    public function export(Request $request)
    {
        $query = LoginAttempt::query();

        // Apply same filters as getLoginAttempts
        if ($request->has('date_from')) {
            $query->where('attempted_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to')) {
            $query->where('attempted_at', '<=', $request->date_to);
        }
        
        if ($request->has('status')) {
            $query->where('success', $request->status === 'success');
        }

        $attempts = $query->orderBy('attempted_at', 'desc')->get();

        $csvData = [];
        $csvData[] = ['Email', 'IP Address', 'Status', 'Attempted At', 'User Agent', 'Location'];

        foreach ($attempts as $attempt) {
            $location = $this->getLocationFromIP($attempt->ip_address);
            $csvData[] = [
                $attempt->email,
                $attempt->ip_address,
                $attempt->success ? 'Success' : 'Failed',
                $attempt->attempted_at->format('Y-m-d H:i:s'),
                $attempt->user_agent,
                $location['city'] . ', ' . $location['country']
            ];
        }

        $filename = 'login_attempts_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
