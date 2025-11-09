<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LoginAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'ip_address',
        'user_agent',
        'latitude',
        'longitude',
        'attempted_at',
        'success',
        'lockout_until'
    ];

    protected $casts = [
        'attempted_at' => 'datetime',
        'lockout_until' => 'datetime',
        'success' => 'boolean'
    ];

    /**
     * Record a failed login attempt
     */
    public static function recordFailedAttempt($email, $ipAddress, $userAgent = null, $latitude = null, $longitude = null)
    {
        return self::create([
            'email' => $email,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'attempted_at' => now(),
            'success' => false
        ]);
    }

    /**
     * Record a successful login attempt
     */
    public static function recordSuccessfulAttempt($email, $ipAddress, $userAgent = null, $latitude = null, $longitude = null)
    {
        return self::create([
            'email' => $email,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'attempted_at' => now(),
            'success' => true
        ]);
    }

    /**
     * Get failed attempts count for email/IP combination
     */
    public static function getFailedAttemptsCount($email, $ipAddress, $minutes = 5)
    {
        return self::where('email', $email)
            ->where('ip_address', $ipAddress)
            ->where('success', false)
            ->where('attempted_at', '>=', now()->subMinutes($minutes))
            ->count();
    }

    /**
     * Check if account is locked out
     */
    public static function isLockedOut($email, $ipAddress)
    {
        $lockoutRecord = self::where('email', $email)
            ->where('ip_address', $ipAddress)
            ->where('lockout_until', '>', now())
            ->first();

        return $lockoutRecord ? $lockoutRecord->lockout_until : null;
    }

    /**
     * Set lockout for account
     */
    public static function setLockout($email, $ipAddress, $minutes = 1, $latitude = null, $longitude = null)
    {
        $lockoutUntil = now()->addMinutes($minutes);
        
        return self::create([
            'email' => $email,
            'ip_address' => $ipAddress,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'attempted_at' => now(),
            'success' => false,
            'lockout_until' => $lockoutUntil
        ]);
    }

    /**
     * Clear failed attempts for successful login
     */
    public static function clearFailedAttempts($email, $ipAddress)
    {
        self::where('email', $email)
            ->where('ip_address', $ipAddress)
            ->where('success', false)
            ->delete();
    }

    /**
     * Get remaining attempts before lockout
     */
    public static function getRemainingAttempts($email, $ipAddress, $maxAttempts = 4)
    {
        $failedCount = self::getFailedAttemptsCount($email, $ipAddress);
        return max(0, $maxAttempts - $failedCount);
    }
}
