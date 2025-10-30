<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class OtpVerification extends Model
{
    protected $fillable = [
        'user_id',
        'otp_code',
        'expires_at',
        'is_used',
        'type'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isValid(): bool
    {
        return !$this->is_used && !$this->isExpired();
    }

    public static function generateOtp(int $userId, string $type = 'registration'): self
    {
        // Deactivate any existing OTPs for this user
        self::where('user_id', $userId)
            ->where('type', $type)
            ->update(['is_used' => true]);

        // Generate new OTP
        $otpCode = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        
        return self::create([
            'user_id' => $userId,
            'otp_code' => $otpCode,
            'expires_at' => Carbon::now()->addMinutes(10), // OTP expires in 10 minutes
            'type' => $type
        ]);
    }
}
