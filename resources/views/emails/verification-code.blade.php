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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .content {
            padding: 40px 30px;
        }
        .verification-code {
            background: #f8f9fa;
            border: 2px dashed #667eea;
            border-radius: 8px;
            padding: 25px;
            text-align: center;
            margin: 30px 0;
            font-size: 36px;
            font-weight: bold;
            letter-spacing: 8px;
            color: #667eea;
            font-family: 'Courier New', monospace;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            font-size: 14px;
            color: #666;
            border-top: 1px solid #e9ecef;
        }
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
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
            <h1>✝️ FaithConnect</h1>
            <p>Connecting Faith Communities</p>
        </div>
        
        <div class="content">
            <h2>Welcome{{ isset($name) ? ', ' . $name : '' }}! 👋</h2>
            <p>Thank you for registering with FaithConnect. To complete your registration, please use the verification code below:</p>
            
            <div class="verification-code">
                {{ $code }}
            </div>
            
            <div class="warning">
                <strong>⏱️ Important:</strong> This verification code will expire in <strong>10 minutes</strong>. If you didn't request this code, please ignore this email.
            </div>
            
            <p>If you have any questions or need assistance, please don't hesitate to contact our support team.</p>
            
            <p>Blessings,<br><strong>The FaithConnect Team</strong></p>
        </div>
        
        <div class="footer">
            <p>© 2024 FaithConnect. All rights reserved.</p>
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>