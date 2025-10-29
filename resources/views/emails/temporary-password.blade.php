<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome to Our Church</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4e73df; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f8f9fa; }
        .password-box { background: white; padding: 15px; border: 2px solid #4e73df; border-radius: 5px; text-align: center; margin: 20px 0; }
        .footer { text-align: center; padding: 20px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to Our Church!</h1>
        </div>
        
        <div class="content">
            <p>Dear {{ $memberName }},</p>
            
            <p>Welcome to our church family! Your member account has been created successfully.</p>
            
            <p><strong>Your login details:</strong></p>
            <ul>
                <li><strong>Email:</strong> {{ $email }}</li>
                <li><strong>Temporary Password:</strong></li>
            </ul>
            
            <div class="password-box">
                <h2 style="color: #4e73df; margin: 0;">{{ $password }}</h2>
            </div>
            
            <p><strong>Important:</strong> Please change your password after your first login for security purposes.</p>
            
            <p>You can now access the member portal to:</p>
            <ul>
                <li>View church events and announcements</li>
                <li>Watch sermons and spiritual content</li>
                <li>Connect with other members</li>
                <li>Make donations</li>
            </ul>
            
            <p>If you have any questions, please don't hesitate to contact us.</p>
            
            <p>God bless,<br>Church Administration</p>
        </div>
        
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>