@extends('layouts.app')

@section('content')
<style>
    /* Center the login card */
    .login-container {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        min-height: 80vh;
        padding: 20px;
    }

    /* Card Styling */
    .login-card {
        width: 100%;
        max-width: 500px;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.1);
        background: #ffffff;
        transition: all 0.3s ease;
    }

    /* Logo and Title Styling */
    .login-header {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 25px;
        gap: 10px;
    }

    .login-logo {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        object-fit: cover;
    }

    .login-header h3 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
        color: #1a237e;
    }

    /* Input Fields */
    .form-label {
        font-weight: 500;
        color: #333;
        margin-bottom: 8px;
        font-size: 0.95rem;
    }

    .form-control {
        border-radius: 8px;
        padding: 12px 15px;
        border: 1px solid #ddd;
        transition: all 0.3s ease;
        font-size: 1rem;
        min-height: 48px; /* Touch-friendly height */
    }
    
    .form-control:focus {
        border-color: #3f51b5;
        box-shadow: 0 0 0 0.2rem rgba(63, 81, 181, 0.15);
        outline: none;
    }
    
    .input-container {
        position: relative;
        margin-bottom: 1rem;
    }
    
    .password-toggle {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #777;
        background: none;
        border: none;
        padding: 8px;
        min-width: 44px; /* Touch-friendly */
        min-height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.3s ease;
    }

    .password-toggle:hover {
        color: #3f51b5;
    }

    .password-toggle i {
        font-size: 1.1rem;
    }

    /* Login Button */
    .btn-primary {
        background: linear-gradient(135deg, #3f51b5, #1a237e);
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        width: 100%;
        font-weight: 600;
        font-size: 1rem;
        min-height: 48px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(63, 81, 181, 0.2);
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #1a237e, #0d1b5e);
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(63, 81, 181, 0.3);
    }

    .btn-primary:active {
        transform: translateY(0);
    }

    /* Form Check */
    .form-check {
        padding-left: 1.5rem;
    }

    .form-check-input {
        width: 18px;
        height: 18px;
        margin-top: 0.25rem;
        cursor: pointer;
    }

    .form-check-label {
        cursor: pointer;
        margin-left: 0.25rem;
    }

    /* Forgot Password Link */
    .forgot-password {
        display: block;
        text-align: center;
        margin-top: 15px;
        color: #3f51b5;
        text-decoration: none;
        font-size: 0.95rem;
        transition: color 0.3s ease;
    }

    .forgot-password:hover {
        color: #1a237e;
        text-decoration: underline;
    }

    /* Register Section */
    .register-section {
        text-align: center;
        margin-top: 25px;
        padding-top: 20px;
        border-top: 1px solid #e0e0e0;
    }

    .register-section p {
        margin-bottom: 12px;
        color: #666;
        font-size: 0.95rem;
    }

    .register-button {
        background-color: white;
        color: #3f51b5;
        border: 2px solid #3f51b5;
        padding: 12px 20px;
        border-radius: 8px;
        width: 100%;
        font-weight: 600;
        font-size: 1rem;
        min-height: 48px;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .register-button:hover {
        background-color: #3f51b5;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(63, 81, 181, 0.2);
    }

    .register-button:active {
        transform: translateY(0);
    }

    /* Alert Styling */
    .alert {
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 20px;
        border: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border-left: 4px solid #28a745;
    }

    /* Mobile Responsive Styles */
    @media (max-width: 768px) {
        .login-container {
            min-height: 100vh;
            padding: 15px;
            justify-content: flex-start;
            padding-top: 40px;
        }

        .login-card {
            padding: 25px 20px;
            box-shadow: 0px 2px 15px rgba(0, 0, 0, 0.08);
        }

        .login-header {
            margin-bottom: 20px;
        }

        .login-header h3 {
            font-size: 1.3rem;
        }

        .login-logo {
            width: 45px;
            height: 45px;
        }

        .form-label {
            font-size: 0.9rem;
        }

        .form-control {
            padding: 11px 14px;
            font-size: 16px; /* Prevents zoom on iOS */
        }

        .btn-primary,
        .register-button {
            padding: 13px 20px;
            font-size: 1rem;
        }

        .register-section {
            margin-top: 20px;
            padding-top: 15px;
        }
    }

    @media (max-width: 576px) {
        .login-container {
            padding: 10px;
            padding-top: 30px;
        }

        .login-card {
            padding: 20px 15px;
            border-radius: 10px;
        }

        .login-header {
            margin-bottom: 18px;
            gap: 8px;
        }

        .login-header h3 {
            font-size: 1.2rem;
        }

        .login-logo {
            width: 40px;
            height: 40px;
        }

        .form-label {
            font-size: 0.85rem;
            margin-bottom: 6px;
        }

        .form-control {
            padding: 10px 12px;
            font-size: 16px;
        }

        .password-toggle {
            right: 10px;
        }

        .btn-primary,
        .register-button {
            padding: 12px 18px;
            font-size: 0.95rem;
        }

        .forgot-password {
            font-size: 0.9rem;
            margin-top: 12px;
        }

        .register-section p {
            font-size: 0.9rem;
        }
    }

    @media (max-width: 400px) {
        .login-card {
            padding: 18px 12px;
        }

        .login-header h3 {
            font-size: 1.1rem;
        }

        .login-logo {
            width: 35px;
            height: 35px;
        }

        .form-control {
            padding: 9px 11px;
        }

        .btn-primary,
        .register-button {
            padding: 11px 16px;
            font-size: 0.9rem;
        }
    }

    /* Landscape Mode for Mobile */
    @media (max-height: 600px) and (orientation: landscape) {
        .login-container {
            min-height: auto;
            padding: 20px 15px;
        }

        .login-card {
            padding: 20px;
        }

        .login-header {
            margin-bottom: 15px;
        }

        .mb-3 {
            margin-bottom: 0.75rem !important;
        }

        .register-section {
            margin-top: 15px;
            padding-top: 12px;
        }
    }
</style>

@if(session('success'))
    <div class="alert alert-success animate__animated animate__fadeIn">
        {{ session('success') }}
    </div>
@endif

<div class="login-container">
    <div class="card login-card animate__animated animate__fadeInUp">
        <!-- Logo and Login Title -->
        <div class="login-header">
            <img src="{{ asset('images/blessed.logo.jpg') }}" alt="Logo" class="login-logo">
            <h3>Log In</h3>
        </div>

        <div class="card-body p-0">
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('Email Address') }}</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                        name="email" value="{{ old('email') }}" required autocomplete="email" autofocus 
                        placeholder="Enter your email">
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">{{ __('Password') }}</label>
                    <div class="input-container">
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                            name="password" required autocomplete="current-password" 
                            placeholder="Enter your password" style="padding-right: 50px;">
                        <button type="button" class="password-toggle" onclick="togglePassword()" aria-label="Toggle password visibility">
                            <i class="fa fa-eye-slash"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-3 form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember"
                        {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">
                        {{ __('Remember Me') }}
                    </label>
                </div>

                <button type="submit" class="btn btn-primary">
                    {{ __('Login') }}
                </button>

                @if (Route::has('password.request'))
                    <a class="forgot-password" href="{{ route('password.request') }}">
                        {{ __('Forgot Your Password?') }}
                    </a>
                @endif
            </form>

            <!-- Register Section -->
            <div class="register-section">
                <p>Don't have an account?</p>
                <button class="register-button" onclick="window.location.href='{{ route('register') }}'">
                    Create New Account
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const passwordField = document.getElementById('password');
        const toggleIcon = document.querySelector('.password-toggle i');
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        } else {
            passwordField.type = 'password';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        }
    }

    // Auto-dismiss success message
    document.addEventListener('DOMContentLoaded', function() {
        const successAlert = document.querySelector('.alert-success');
        if (successAlert) {
            setTimeout(() => {
                successAlert.style.opacity = '0';
                successAlert.style.transform = 'translateY(-20px)';
                setTimeout(() => successAlert.remove(), 300);
            }, 5000);
        }
    });
</script>
@endsection