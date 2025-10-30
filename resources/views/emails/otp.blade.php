<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .otp-code {
            background: #fff;
            border: 2px dashed #667eea;
            padding: 20px;
            text-align: center;
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
            margin: 20px 0;
            border-radius: 8px;
            letter-spacing: 5px;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔐 OTP Verification</h1>
        <p>Madridejos Community Waterworks System</p>
    </div>
    
    <div class="content">
        <h2>Hello {{ $user->full_name }}!</h2>
        
        @if($type === 'login')
            <p>You are attempting to log in to your account. Please use the verification code below to complete your login:</p>
        @else
            <p>Thank you for registering with Madridejos Community Waterworks System! Please use the verification code below to activate your account:</p>
        @endif
        
        <div class="otp-code">
            {{ $otpCode }}
        </div>
        
        <div class="warning">
            <strong>⚠️ Important:</strong>
            <ul>
                <li>This code will expire in 10 minutes</li>
                <li>Do not share this code with anyone</li>
                <li>If you didn't request this code, please ignore this email</li>
            </ul>
        </div>
        
        <p>If you have any questions or need assistance, please contact our support team.</p>
        
        <p>Best regards,<br>
        <strong>Madridejos Community Waterworks System</strong></p>
    </div>
    
    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
    </div>
</body>
</html>

