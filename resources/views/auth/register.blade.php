@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: #f5f5f5;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .registration-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    
    .registration-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        width: 100%;
        max-width: 900px;
        display: flex;
        overflow: hidden;
    }
    
    .sidebar {
        background: #f8f9fa;
        width: 300px;
        padding: 40px 30px;
        border-right: 1px solid #e9ecef;
    }
    
    
    .form-title {
        font-size: 24px;
        font-weight: 600;
        color: #333;
        margin-bottom: 40px;
    }
    
    .step-indicator {
        display: flex;
        flex-direction: column;
        gap: 0;
    }
    
    .step {
        display: flex;
        align-items: center;
        padding: 15px 0;
        position: relative;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .step:not(:last-child)::after {
        content: '';
        position: absolute;
        left: 15px;
        top: 45px;
        width: 2px;
        height: 30px;
        background: #e0e0e0;
        transition: background 0.3s ease;
    }
    
    .step.completed:not(:last-child)::after {
        background: #4a6fa5;
    }
    
    .step-number {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #e0e0e0;
        color: #666;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        flex-shrink: 0;
    }
    
    .step-text {
        font-size: 16px;
        color: #666;
        font-weight: 500;
        transition: color 0.3s ease;
    }
    
    .step.active .step-number {
        background: #4a6fa5;
        color: white;
        transform: scale(1.1);
    }
    
    .step.active .step-text {
        color: #4a6fa5;
        font-weight: 600;
    }
    
    .step.completed .step-number {
        background: #4a6fa5;
        color: white;
    }
    
    .step.completed .step-text {
        color: #4a6fa5;
    }
    
    .step.completed .step-number::before {
        content: '✓';
        font-size: 16px;
    }
    
    .main-content {
        flex: 1;
        padding: 40px;
        min-height: 500px;
        display: flex;
        flex-direction: column;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #333;
        font-size: 14px;
    }
    
    .form-input {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 16px;
        transition: border-color 0.2s;
        background: #f8f9fa;
    }
    
    .form-input:focus {
        outline: none;
        border-color: #4a6fa5;
        background: white;
        box-shadow: 0 0 0 2px rgba(74, 111, 165, 0.1);
    }
    
    .form-input.error {
        border-color: #dc3545;
        background: #fff5f5;
    }
    
    .password-container {
        position: relative;
    }
    
    .password-toggle {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #666;
        cursor: pointer;
        padding: 4px;
    }
    
    .checkbox-container {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin: 20px 0;
    }
    
    .checkbox {
        margin-top: 2px;
    }
    
    .checkbox-label {
        font-size: 14px;
        color: #666;
        line-height: 1.4;
    }
    
    .btn-primary {
        width: 100%;
        padding: 14px;
        background: #4a6fa5;
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.2s;
        margin-top: 10px;
    }
    
    .btn-primary:hover:not(:disabled) {
        background: #3d5a8a;
    }
    
    .btn-primary:disabled {
        background: #ccc;
        cursor: not-allowed;
    }
    
    .btn-secondary {
        padding: 12px 24px;
        background: #f8f9fa;
        color: #666;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
        margin-right: 10px;
    }
    
    .btn-secondary:hover {
        background: #e9ecef;
    }
    
    .button-group {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 30px;
    }
    
    .error-message {
        color: #dc3545;
        font-size: 13px;
        margin-top: 5px;
    }
    
    .success-message {
        color: #28a745;
        font-size: 13px;
        margin-top: 5px;
    }
    
    .login-link {
        text-align: center;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #eee;
        color: #666;
        font-size: 14px;
    }
    
    .login-link a {
        color: #4a6fa5;
        text-decoration: none;
    }
    
    .verification-code {
        text-align: center;
        font-size: 18px;
        letter-spacing: 2px;
        padding: 15px;
        background: #f8f9fa;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin: 20px 0;
    }
    
    .step-content {
        display: none;
    }
    
    .step-content.active {
        display: flex;
        flex-direction: column;
        height: 100%;
        animation: fadeIn 0.3s ease;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes slideIn {
        from { transform: translateY(-50px) scale(0.9); opacity: 0; }
        to { transform: translateY(0) scale(1); opacity: 1; }
    }
    
    .confirmation-info {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin: 20px 0;
        border: 1px solid #e9ecef;
    }
    
    .confirmation-info strong {
        color: #4a6fa5;
    }
    
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        animation: fadeIn 0.3s ease;
    }
    
    .modal-content {
        background-color: white;
        margin: 5% auto;
        padding: 0;
        border-radius: 8px;
        width: 90%;
        max-width: 800px;
        max-height: 80vh;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        animation: slideIn 0.3s ease;
    }
    
    @keyframes slideIn {
        from { transform: translateY(-50px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    
    .modal-header {
        background: #4a6fa5;
        color: white;
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .modal-title {
        font-size: 24px;
        font-weight: 600;
        margin: 0;
    }
    
    .close {
        color: white;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        background: none;
        border: none;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: background-color 0.2s;
    }
    
    .close:hover {
        background-color: rgba(255,255,255,0.2);
    }
    
    .modal-body {
        padding: 30px;
        max-height: 60vh;
        overflow-y: auto;
    }
    
    .legal-section {
        margin-bottom: 25px;
    }
    
    .legal-section h3 {
        color: #4a6fa5;
        font-size: 18px;
        margin-bottom: 10px;
        font-weight: 600;
    }
    
    .legal-section p {
        color: #555;
        line-height: 1.6;
        margin-bottom: 10px;
        font-size: 14px;
    }
    
    .legal-section ul {
        color: #555;
        line-height: 1.6;
        margin-left: 20px;
        font-size: 14px;
    }
    
    @media (max-width: 768px) {
        .registration-card {
            flex-direction: column;
            margin: 10px;
        }
        
        .sidebar {
            width: 100%;
            padding: 20px;
            border-right: none;
            border-bottom: 1px solid #e9ecef;
        }
        
        .step-indicator {
            flex-direction: row;
            overflow-x: auto;
            gap: 20px;
            padding-bottom: 10px;
        }
        
        .step {
            flex-direction: column;
            text-align: center;
            min-width: 80px;
            padding: 10px 0;
        }
        
        .step:not(:last-child)::after {
            display: none;
        }
        
        .step-number {
            margin-right: 0;
            margin-bottom: 5px;
        }
        
        .step-text {
            font-size: 12px;
        }
        
        .main-content {
            padding: 20px;
        }
        
        .form-title {
            font-size: 20px;
        }
        
        .modal-content {
            width: 95%;
            margin: 10% auto;
        }
        
        .modal-body {
            padding: 20px;
        }
    }
</style>

<div class="registration-container">
    <div class="registration-card">
        <div class="sidebar">
            <h1 class="form-title">Create Account</h1>
            
            <div class="step-indicator">
                <div class="step active" id="step-1-indicator">
                    <div class="step-number">1</div>
                    <div class="step-text">Email</div>
                </div>
                <div class="step" id="step-2-indicator">
                    <div class="step-number">2</div>
                    <div class="step-text">Your Name</div>
                </div>
                <div class="step" id="step-3-indicator">
                    <div class="step-number">3</div>
                    <div class="step-text">Password</div>
                </div>
                <div class="step" id="step-4-indicator">
                    <div class="step-number">4</div>
                    <div class="step-text">Terms</div>
                </div>
                <div class="step" id="step-5-indicator">
                    <div class="step-number">5</div>
                    <div class="step-text">Confirm</div>
                </div>
            </div>
        </div>
        
        <div class="main-content">
            <form id="registration-form">
                @csrf
                
                <!-- Step 1: Email -->
                <div class="step-content active" id="step-1">
                    <h2 style="color: #333; margin-bottom: 10px; font-size: 24px; font-weight: 600;">Personal Email Address</h2>
                    <p style="color: #666; margin-bottom: 30px;">We'll use this to send you important updates about your account.</p>
                    
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-input" id="email" name="email" value="{{ old('email') }}" required>
                        <div class="error-message" id="email-error"></div>
                    </div>
                    
                    <div style="margin-top: auto;">
                        <button type="button" class="btn-primary" onclick="nextStep(1)">Next</button>
                        <div class="login-link">
                            Do you already have an account? <a href="{{ route('login') }}">Log In</a>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Name -->
                <div class="step-content" id="step-2">
                    <h2 style="color: #333; margin-bottom: 10px; font-size: 24px; font-weight: 600;">Your Name</h2>
                    <p style="color: #666; margin-bottom: 30px;">Please enter your full name as it appears on official documents.</p>
                    
                    <div class="form-group">
                        <label class="form-label">First Name</label>
                        <input type="text" class="form-input" id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                        <div class="error-message" id="first_name-error"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Last Name</label>
                        <input type="text" class="form-input" id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                        <div class="error-message" id="last_name-error"></div>
                    </div>
                    
                    <div class="button-group" style="margin-top: auto;">
                        <button type="button" class="btn-secondary" onclick="previousStep(2)">Previous</button>
                        <button type="button" class="btn-primary" onclick="nextStep(2)">Next</button>
                    </div>
                </div>

                <!-- Step 3: Password -->
                <div class="step-content" id="step-3">
                    <h2 style="color: #333; margin-bottom: 10px; font-size: 24px; font-weight: 600;">Create Password</h2>
                    <p style="color: #666; margin-bottom: 30px;">Choose a strong password to secure your account.</p>
                    
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <p style="font-size: 13px; color: #666; margin-bottom: 10px;">Password must be at least twelve characters long.</p>
                        <div class="password-container">
                            <input type="password" class="form-input" id="password" name="password" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                <i class="fa fa-eye-slash"></i>
                            </button>
                        </div>
                        <div class="error-message" id="password-error"></div>
                    </div>
                    <div class="checkbox-container">
                        <input type="checkbox" class="checkbox" id="show-password">
                        <label class="checkbox-label" for="show-password">Show Password</label>
                    </div>
                    
                    <div class="button-group" style="margin-top: auto;">
                        <button type="button" class="btn-secondary" onclick="previousStep(3)">Previous</button>
                        <button type="button" class="btn-primary" onclick="nextStep(3)">Next</button>
                    </div>
                </div>

                <!-- Step 4: Terms -->
                <div class="step-content" id="step-4">
                    <h2 style="color: #333; margin-bottom: 10px; font-size: 24px; font-weight: 600;">Terms & Conditions</h2>
                    <p style="color: #666; margin-bottom: 30px;">Please review and accept our terms to continue.</p>
                    
                    <div style="background: #f0f8ff; padding: 20px; border-radius: 8px; margin-bottom: 30px; border-left: 4px solid #4a6fa5;">
                        <div style="color: #28a745; font-weight: 500; margin-bottom: 10px; font-size: 16px;">✓ Terms of Service</div>
                        <p style="color: #666; font-size: 14px; margin: 0;">By creating an account, you agree to our terms and conditions.</p>
                    </div>
                    
                    <div class="checkbox-container">
                        <input type="checkbox" class="checkbox" id="terms" name="terms" required>
                        <label class="checkbox-label" for="terms">
                            I agree to the <a href="#" onclick="openModal('termsModal')" style="color: #4a6fa5; text-decoration: none; font-weight: 500;">Terms of Service</a> and <a href="#" onclick="openModal('privacyModal')" style="color: #4a6fa5; text-decoration: none; font-weight: 500;">Privacy Policy</a>
                        </label>
                    </div>
                    <div class="error-message" id="terms-error"></div>
                    
                    <div class="button-group" style="margin-top: auto;">
                        <button type="button" class="btn-secondary" onclick="previousStep(4)">Previous</button>
                        <button type="button" class="btn-primary" onclick="nextStep(4)">Next</button>
                    </div>
                </div>

                <!-- Step 5: Confirm -->
                <div class="step-content" id="step-5">
                    <h2 style="color: #333; margin-bottom: 10px; font-size: 24px; font-weight: 600;">Confirm Details</h2>
                    <p style="color: #666; margin-bottom: 30px;">Please review your information before creating your account.</p>
                    
                    <div class="confirmation-info">
                        <div style="margin-bottom: 20px;">
                            <label style="font-weight: 600; color: #333; display: block; margin-bottom: 5px;">Email Address</label>
                            <span id="confirm-email" style="color: #666; font-size: 16px;"></span>
                        </div>
                        <div style="margin-bottom: 20px;">
                            <label style="font-weight: 600; color: #333; display: block; margin-bottom: 5px;">Full Name</label>
                            <span id="confirm-name" style="color: #666; font-size: 16px;"></span>
                        </div>
                        
                        <div style="margin-top: 25px; padding: 15px; background: #fff3cd; border-radius: 8px; border-left: 4px solid #ffc107;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <i class="fa fa-info-circle" style="color: #856404;"></i>
                                <span style="color: #856404; font-size: 14px; font-weight: 500;">You cannot change your email after creating your account.</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="button-group" style="margin-top: auto;">
                        <button type="button" class="btn-secondary" onclick="previousStep(5)">Previous</button>
                        <button type="button" class="btn-primary" onclick="sendVerificationCode()">Create Account</button>
                    </div>
                </div>

                <!-- Email Verification Step -->
                <div class="step-content" id="verification-step" style="display: none;">
                    <h2 style="color: #333; margin-bottom: 10px; font-size: 24px; font-weight: 600;">Email Verification</h2>
                    <p style="color: #666; margin-bottom: 30px;">We've sent a 6-digit verification code to your email address. Please enter it below:</p>
                    
                    <div class="form-group">
                        <label class="form-label">Verification Code</label>
                        <input type="text" class="form-input" id="verification_code" name="verification_code" maxlength="6" style="text-align: center; font-size: 24px; letter-spacing: 4px; font-weight: 600;">
                        <div class="error-message" id="verification-error"></div>
                    </div>
                    
                    <div style="margin-top: auto;">
                        <button type="button" class="btn-primary" onclick="verifyCode()">Verify & Complete Registration</button>
                        <div style="text-align: center; margin-top: 20px;">
                            <button type="button" id="resendBtn" style="background: none; border: none; color: #4a6fa5; text-decoration: underline; cursor: pointer; font-size: 14px;" onclick="resendCode()">Didn't receive the code? Resend</button>
                        </div>
                    </div>
                </div>

                <!-- Success Step -->
                <div class="step-content" id="success-step" style="display: none;">
                    <div style="text-align: center; padding: 40px 0;">
                        <div style="width: 80px; height: 80px; background: #28a745; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 30px;">
                            <i class="fa fa-check" style="font-size: 40px; color: white;"></i>
                        </div>
                        <h2 style="color: #28a745; margin-bottom: 15px; font-size: 28px; font-weight: 600;">Welcome!</h2>
                        <p style="color: #666; font-size: 18px; margin-bottom: 10px;">Hello, <strong id="success-name" style="color: #333;"></strong>!</p>
                        <p style="color: #666; margin-bottom: 40px;">Your account has been created successfully. You can now sign in to access your account.</p>
                        <a href="{{ route('login') }}" class="btn-primary" style="display: inline-block; text-decoration: none; padding: 15px 30px; font-size: 16px;">Continue to Sign In</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Terms Modal -->
<div id="termsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Terms of Service</h2>
            <button class="close" onclick="closeModal('termsModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="legal-section">
                <h3>1. Acceptance of Terms</h3>
                <p>By creating an account and using our services, you agree to be bound by these Terms of Service. If you do not agree to these terms, please do not use our services.</p>
            </div>
            <div class="legal-section">
                <h3>2. Account Registration</h3>
                <p>To use our services, you must:</p>
                <ul>
                    <li>Provide accurate and complete information during registration</li>
                    <li>Maintain the security of your account credentials</li>
                    <li>Be at least 13 years of age</li>
                    <li>Use the service for lawful purposes only</li>
                </ul>
            </div>
            <div class="legal-section">
                <h3>3. User Responsibilities</h3>
                <p>You are responsible for:</p>
                <ul>
                    <li>All activities that occur under your account</li>
                    <li>Keeping your login information secure</li>
                    <li>Notifying us immediately of any unauthorized use</li>
                    <li>Complying with all applicable laws and regulations</li>
                </ul>
            </div>
            <div class="legal-section">
                <h3>4. Prohibited Activities</h3>
                <p>You may not:</p>
                <ul>
                    <li>Use the service for any illegal or unauthorized purpose</li>
                    <li>Attempt to gain unauthorized access to our systems</li>
                    <li>Interfere with or disrupt the service</li>
                    <li>Share inappropriate or harmful content</li>
                </ul>
            </div>
            <div class="legal-section">
                <h3>5. Privacy</h3>
                <p>Your privacy is important to us. Please review our Privacy Policy to understand how we collect, use, and protect your information.</p>
            </div>
            <div class="legal-section">
                <h3>6. Service Availability</h3>
                <p>We strive to provide reliable service but cannot guarantee 100% uptime. We reserve the right to modify, suspend, or discontinue the service at any time.</p>
            </div>
            <div class="legal-section">
                <h3>7. Limitation of Liability</h3>
                <p>Our liability is limited to the maximum extent permitted by law. We are not responsible for any indirect, incidental, or consequential damages.</p>
            </div>
            <div class="legal-section">
                <h3>8. Changes to Terms</h3>
                <p>We may update these terms from time to time. We will notify users of significant changes via email or through our service.</p>
            </div>
            <div class="legal-section">
                <h3>9. Contact Information</h3>
                <p>If you have questions about these Terms of Service, please contact us at support@example.com.</p>
            </div>
        </div>
    </div>
</div>

<!-- Privacy Modal -->
<div id="privacyModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Privacy Policy</h2>
            <button class="close" onclick="closeModal('privacyModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="legal-section">
                <h3>1. Information We Collect</h3>
                <p>We collect information you provide directly to us, such as:</p>
                <ul>
                    <li>Name and email address when you create an account</li>
                    <li>Profile information you choose to provide</li>
                    <li>Communications you send to us</li>
                    <li>Usage data and analytics</li>
                </ul>
            </div>
            <div class="legal-section">
                <h3>2. How We Use Your Information</h3>
                <p>We use the information we collect to:</p>
                <ul>
                    <li>Provide and maintain our services</li>
                    <li>Send you important updates and notifications</li>
                    <li>Respond to your questions and support requests</li>
                    <li>Improve our services and user experience</li>
                    <li>Ensure security and prevent fraud</li>
                </ul>
            </div>
            <div class="legal-section">
                <h3>3. Information Sharing</h3>
                <p>We do not sell, trade, or rent your personal information to third parties. We may share your information only in these limited circumstances:</p>
                <ul>
                    <li>With your explicit consent</li>
                    <li>To comply with legal obligations</li>
                    <li>To protect our rights and safety</li>
                    <li>With trusted service providers who assist us</li>
                </ul>
            </div>
            <div class="legal-section">
                <h3>4. Data Security</h3>
                <p>We implement appropriate security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction. However, no method of transmission over the internet is 100% secure.</p>
            </div>
            <div class="legal-section">
                <h3>5. Data Retention</h3>
                <p>We retain your personal information only as long as necessary to provide our services and fulfill the purposes outlined in this policy, unless a longer retention period is required by law.</p>
            </div>
            <div class="legal-section">
                <h3>6. Your Rights</h3>
                <p>You have the right to:</p>
                <ul>
                    <li>Access and update your personal information</li>
                    <li>Request deletion of your account and data</li>
                    <li>Opt out of certain communications</li>
                    <li>Request a copy of your data</li>
                </ul>
            </div>
            <div class="legal-section">
                <h3>7. Cookies and Tracking</h3>
                <p>We use cookies and similar technologies to enhance your experience, analyze usage patterns, and improve our services. You can control cookie settings through your browser.</p>
            </div>
            <div class="legal-section">
                <h3>8. Children's Privacy</h3>
                <p>Our services are not intended for children under 13. We do not knowingly collect personal information from children under 13. If we become aware of such collection, we will delete the information immediately.</p>
            </div>
            <div class="legal-section">
                <h3>9. Changes to This Policy</h3>
                <p>We may update this Privacy Policy from time to time. We will notify you of any material changes by email or through our service.</p>
            </div>
            <div class="legal-section">
                <h3>10. Contact Us</h3>
                <p>If you have any questions about this Privacy Policy, please contact us at privacy@example.com.</p>
            </div>
        </div>
    </div>
</div>

<script>
    let currentStep = 1;
    let formData = {};
    
    function nextStep(step) {
        if (validateStep(step)) {
            document.getElementById(`step-${step}`).classList.remove('active');
            document.getElementById(`step-${step}-indicator`).classList.add('completed');
            
            currentStep = step + 1;
            document.getElementById(`step-${currentStep}`).classList.add('active');
            document.getElementById(`step-${currentStep}-indicator`).classList.add('active');
            
            if (currentStep === 5) {
                updateConfirmation();
            }
        }
    }
    
    function previousStep(step) {
        document.getElementById(`step-${step}`).classList.remove('active');
        document.getElementById(`step-${step}-indicator`).classList.remove('active');
        
        currentStep = step - 1;
        document.getElementById(`step-${currentStep}`).classList.add('active');
        document.getElementById(`step-${currentStep}-indicator`).classList.remove('completed');
    }
    
    function validateStep(step) {
        clearErrors();
        
        switch(step) {
            case 1:
                const email = document.getElementById('email').value;
                if (!email || !isValidEmail(email)) {
                    showError('email-error', 'Please enter a valid email address');
                    return false;
                }
                formData.email = email;
                break;
                
            case 2:
                const firstName = document.getElementById('first_name').value;
                const lastName = document.getElementById('last_name').value;
                if (!firstName.trim()) {
                    showError('first_name-error', 'First name is required');
                    return false;
                }
                if (!lastName.trim()) {
                    showError('last_name-error', 'Last name is required');
                    return false;
                }
                formData.first_name = firstName;
                formData.last_name = lastName;
                break;
                
            case 3:
                const password = document.getElementById('password').value;
                if (!password || password.length < 12) {
                    showError('password-error', 'Password must be at least 12 characters long');
                    return false;
                }
                formData.password = password;
                break;
                
            case 4:
                const terms = document.getElementById('terms').checked;
                if (!terms) {
                    showError('terms-error', 'You must accept the terms and conditions');
                    return false;
                }
                formData.terms = terms;
                break;
        }
        return true;
    }
    
    function updateConfirmation() {
        document.getElementById('confirm-email').textContent = formData.email;
        document.getElementById('confirm-name').textContent = `${formData.first_name} ${formData.last_name}`;
    }
    
    function sendVerificationCode() {
        const btn = event.target;
        btn.disabled = true;
        btn.textContent = 'Sending...';
        
        fetch('/send-verification', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value
            },
            body: JSON.stringify(formData)
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => Promise.reject(err));
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                document.getElementById('step-5').classList.remove('active');
                document.getElementById('verification-step').style.display = 'block';
                document.getElementById('verification-step').classList.add('active');
            } else {
                showErrorModal('Error', 'Error sending verification code. Please try again.');
            }
        })
        .catch(error => {
            console.error('Validation Error:', error);
            if (error.errors) {
                let errorList = '';
                Object.keys(error.errors).forEach(field => {
                    errorList += `<li><strong>${field}:</strong> ${error.errors[field].join(', ')}</li>`;
                });
                showErrorModal('Validation Failed', `<ul style="margin: 0; padding-left: 20px;">${errorList}</ul>`);
            } else {
                showErrorModal('Error', error.message || 'Please check your information and try again.');
            }
        })
        .finally(() => {
            btn.disabled = false;
            btn.textContent = 'Create Account';
        });
    }
    
    function verifyCode() {
        const code = document.getElementById('verification_code').value;
        if (!code || code.length !== 6) {
            showError('verification-error', 'Please enter the 6-digit verification code');
            return;
        }
        
        const btn = event.target;
        btn.disabled = true;
        btn.textContent = 'Verifying...';
        
        fetch('/verify-registration', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value
            },
            body: JSON.stringify({
                ...formData,
                verification_code: code
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => Promise.reject(err));
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                document.getElementById('verification-step').classList.remove('active');
                document.getElementById('success-step').style.display = 'block';
                document.getElementById('success-step').classList.add('active');
                document.getElementById('success-name').textContent = `${formData.first_name} ${formData.last_name}`;
            } else {
                showError('verification-error', data.message || 'Invalid verification code');
            }
        })
        .catch(error => {
            console.error('Verification Error:', error);
            showError('verification-error', error.message || 'Error verifying code. Please try again.');
        })
        .finally(() => {
            btn.disabled = false;
            btn.textContent = 'Verify & Complete Registration';
        });
    }
    
    function resendCode() {
        const btn = document.getElementById('resendBtn');
        btn.disabled = true;
        btn.style.color = '#999';
        btn.style.cursor = 'not-allowed';
        
        let countdown = 60;
        const originalText = btn.textContent;
        
        const timer = setInterval(() => {
            btn.textContent = `Resend in ${countdown}s`;
            countdown--;
            
            if (countdown < 0) {
                clearInterval(timer);
                btn.disabled = false;
                btn.style.color = '#4a6fa5';
                btn.style.cursor = 'pointer';
                btn.textContent = originalText;
            }
        }, 1000);
        
        sendVerificationCode();
    }
    
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = field.parentNode.querySelector('.password-toggle i');
        
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }
    }
    
    function showError(elementId, message) {
        document.getElementById(elementId).textContent = message;
    }
    
    function clearErrors() {
        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
    }
    
    function showErrorModal(title, message) {
        const modal = document.createElement('div');
        modal.style.cssText = `
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
            background: rgba(0,0,0,0.5); z-index: 9999; display: flex; 
            align-items: center; justify-content: center; animation: fadeIn 0.3s ease;
        `;
        
        modal.innerHTML = `
            <div style="
                background: white; border-radius: 8px; padding: 0; max-width: 400px; 
                width: 90%; box-shadow: 0 4px 20px rgba(0,0,0,0.3); 
                animation: slideIn 0.3s ease;
            ">
                <div style="
                    background: #dc3545; color: white; padding: 20px; 
                    border-radius: 8px 8px 0 0; display: flex; 
                    justify-content: space-between; align-items: center;
                ">
                    <h3 style="margin: 0; font-size: 18px;">${title}</h3>
                    <button onclick="this.closest('div').parentElement.remove()" style="
                        background: none; border: none; color: white; 
                        font-size: 24px; cursor: pointer; padding: 0;
                    ">&times;</button>
                </div>
                <div style="padding: 20px; color: #333; line-height: 1.5;">
                    ${message}
                </div>
                <div style="padding: 0 20px 20px; text-align: right;">
                    <button onclick="this.closest('div').parentElement.remove()" style="
                        background: #dc3545; color: white; border: none; 
                        padding: 10px 20px; border-radius: 4px; cursor: pointer;
                    ">OK</button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        setTimeout(() => modal.remove(), 2000);
    }
    
    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }
    
    function openModal(modalId) {
        document.getElementById(modalId).style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
    
    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
        document.body.style.overflow = 'auto';
    }
    
    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }
    
    // Show password checkbox functionality
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('show-password').addEventListener('change', function() {
            const passwordField = document.getElementById('password');
            passwordField.type = this.checked ? 'text' : 'password';
        });
    });
</script>
@endsection