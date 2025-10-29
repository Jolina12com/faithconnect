<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Email Verification</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: #4a6fa5;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .content {
            padding: 40px 30px;
        }
        .verification-code {
            background: #f8f9fa;
            border: 2px solid #4a6fa5;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 3px;
            color: #4a6fa5;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            font-size: 14px;
            color: #666;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Email Verification</h1>
        </div>
        
        <div class="content">
            <h2>Welcome!</h2>
            <p>Thank you for registering with us. To complete your registration, please use the verification code below:</p>
            
            <div class="verification-code">
                {{ $code }}
            </div>
            
            <div class="warning">
                <strong>Important:</strong> This verification code will expire in 10 minutes. If you didn't request this code, please ignore this email.
            </div>
            
            <p>If you have any questions or need assistance, please don't hesitate to contact our support team.</p>
        </div>
        
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>