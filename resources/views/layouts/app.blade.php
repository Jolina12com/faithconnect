<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'JVC Website') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/js/app.js', 'resources/css/app.css'])

    <!-- Scripts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    <!-- Pusher and Laravel Echo -->
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.0/dist/echo.min.js"></script>
    
    <script>
        window.pusherKey = "{{ config('broadcasting.connections.pusher.key') }}";
        window.pusherCluster = "{{ config('broadcasting.connections.pusher.options.cluster') }}";
        
        // Initialize Echo globally
        document.addEventListener('DOMContentLoaded', function() {
            try {
                window.Echo = new Echo({
                    broadcaster: 'pusher',
                    key: window.pusherKey,
                    cluster: window.pusherCluster,
                    forceTLS: true,
                    encrypted: true,
                    authEndpoint: '/broadcasting/auth',
                    auth: {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    }
                });
                console.log('Echo initialized globally - ready for private channels');
            } catch (error) {
                console.error('Failed to initialize Echo:', error);
            }
        });
    </script>

</head>
<style>
    /* Mobile Top Navigation Bar - Facebook Style */
    .mobile-top-nav {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        background: #ffffff;
        border-bottom: 1px solid #e3e3e3;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        z-index: 1050;
        padding: 8px 0;
        display: none;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 60px;
    }
    
    .mobile-top-nav.show {
        display: block;
        animation: slideDownFromTop 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    @keyframes slideDownFromTop {
        from {
            transform: translateY(-100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    .mobile-nav-container {
        display: flex;
        justify-content: space-around;
        align-items: center;
        max-width: 600px;
        margin: 0 auto;
        padding: 0 10px;
    }
    
    .mobile-nav-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        color: #65676b;
        padding: 8px 6px;
        border-radius: 12px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        min-width: 44px;
        min-height: 44px;
        flex: 1;
        max-width: 60px;
    }
    
    .mobile-nav-item:hover {
        color: #3f51b5;
        text-decoration: none;
        background-color: rgba(63, 81, 181, 0.05);
        transform: translateY(-2px);
    }
    
    .mobile-nav-item.active {
        color: #3f51b5;
        background-color: rgba(63, 81, 181, 0.1);
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(63, 81, 181, 0.2);
    }
    
    .mobile-nav-item i {
        font-size: 20px;
        margin-bottom: 2px;
        transition: all 0.2s ease;
    }
    
    .mobile-nav-item.active i {
        transform: scale(1.1);
        color: #3f51b5;
    }
    
    .mobile-nav-item span {
        font-size: 10px;
        font-weight: 500;
        white-space: nowrap;
        opacity: 0.8;
    }
    
    .mobile-nav-item.active span {
        opacity: 1;
        font-weight: 600;
    }
    
    /* Mobile dropdown menu */
    .mobile-menu-dropdown {
        position: fixed;
        top: 60px;
        right: 10px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        min-width: 200px;
        padding: 8px 0;
        display: none;
        z-index: 1051;
        animation: fadeInScaleDown 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* Mobile menu overlay */
    .mobile-menu-overlay {
        display: none;
        position: fixed;
        top: 60px;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.3);
        z-index: 1049;
    }
    
    .mobile-menu-overlay.show {
        display: block;
    }
    
    @keyframes fadeInScaleDown {
        from {
            opacity: 0;
            transform: scale(0.9) translateY(-10px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }
    
    .mobile-menu-dropdown.show {
        display: block;
    }
    
    .mobile-dropdown-item {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        text-decoration: none;
        color: #1c1e21;
        font-size: 14px;
        font-weight: 500;
        transition: background-color 0.2s ease;
    }
    
    .mobile-dropdown-item:hover {
        background-color: #f2f3f4;
        color: #1c1e21;
        text-decoration: none;
    }
    
    .mobile-dropdown-item i {
        font-size: 16px;
        margin-right: 12px;
        width: 20px;
        text-align: center;
        color: #65676b;
    }
    
    .mobile-dropdown-divider {
        height: 1px;
        background-color: #e4e6ea;
        margin: 4px 0;
    }

    /* Make sure the body takes full height */
    body, html {
        min-height: 100vh;
        margin: 0;
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(145deg, #f8faff, #ffffff);
        color: #333;
        padding-top: 0;
    }

    /* Add padding when mobile nav is shown */
    body.mobile-nav-active {
        padding-top: 60px;
    }

    /* Animations */
    @keyframes fadeInUp {
        0% {
            opacity: 0;
            transform: translateY(20px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Full height container */
    .full-height-container {
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        min-height: 100vh;
        height: auto;
        width: 85%;
        margin: 0 auto;
        background-color: #ffffff;
        padding: 0;
        position: relative;
        z-index: 5;
        animation: fadeInUp 0.8s ease;
        border-radius: 0 0 20px 20px;
        overflow: hidden;
    }

    /* Logo Container - Floating Design */
    .logo-container {
        position: absolute;
        top: -10px;
        left: 20px;
        background-color: #ffffff;
        padding: 10px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease;
        z-index: 11;
    }

    .logo-container:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
    }

    .logo {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        object-fit: cover;
    }

    .text-container {
        font-weight: 600;
        color: #1a237e;
        transition: all 0.3s ease;
        font-size: 1.2rem;
        margin: 0;
        padding: 0;
        padding-left: 90px; /* Space for floating logo */
    }

    /* Top Navbar - Mobile Responsive */
    .top-navbar {
        background-color: #f8faff !important;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        padding: 15px 0;
        border-bottom: 1px solid #eaeaea;
        z-index: 10;
        position: relative;
        width: 85%;
        margin: 0 auto;
        transition: all 0.3s ease;
    }

    .top-navbar .container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
    }

    .top-navbar .navbar-left {
        display: flex;
        align-items: center;
        flex: 1;
    }

    .top-navbar .navbar-right {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Main Navbar - Mobile Responsive */
    .main-navbar {
        background-color: #1a237e !important;
        padding: 15px 0;
        width: 85%;
        margin: 0 auto;
        border-radius: 0 0 20px 20px;
        transition: all 0.3s ease;
    }

    .navbar-toggler {
        border: none;
        padding: 8px 12px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .navbar-toggler:focus {
        box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25);
    }

    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }

    /* Navigation Links */
    .nav-link {
        transition: all 0.3s ease;
        position: relative;
        font-weight: 500;
        padding: 10px 20px !important;
        border-radius: 8px;
    }

    .nav-link::after {
        content: '';
        position: absolute;
        width: 0;
        height: 2px;
        bottom: 0;
        left: 0;
        background-color: #fff;
        transition: width 0.3s ease;
    }

    .nav-link:hover::after {
        width: 100%;
    }

    .nav-link.active {
        background-color: rgba(255, 255, 255, 0.1) !important;
        border-radius: 25px;
    }

    /* Mobile Menu Styles */
    @media (max-width: 991.98px) {
        .full-height-container {
            width: 95%;
        }

        .top-navbar,
        .main-navbar {
            width: 95%;
            display: none; /* Hide default navbars on mobile */
        }

        body.mobile-nav-active {
            padding-top: 60px;
        }

        .mobile-top-nav.show {
            display: block;
        }

        .logo-container {
            position: relative;
            top: 0;
            left: 0;
            margin: 0 auto 10px;
            display: inline-block;
        }

        .text-container {
            padding-left: 0;
            text-align: center;
            margin-top: 10px;
            font-size: 1rem;
        }

        .top-navbar .container {
            flex-direction: column;
            align-items: center !important;
            text-align: center;
        }

        .top-navbar .d-flex {
            flex-direction: column;
            width: 100%;
            align-items: center !important;
        }

        .top-navbar .d-flex > div:first-child {
            flex-direction: column;
            align-items: center;
            margin-bottom: 15px;
        }

        .top-navbar .d-flex > div:last-child {
            width: 100%;
            justify-content: center;
        }

        .top-navbar .btn {
            margin: 5px;
        }

        /* Mobile Navigation Menu */
        .navbar-collapse {
            background-color: #1a237e;
            border-radius: 15px;
            margin-top: 15px;
            padding: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .navbar-nav {
            align-items: stretch !important;
        }

        .nav-item {
            margin: 5px 0;
        }

        .nav-link {
            padding: 12px 20px !important;
            border-radius: 10px;
            text-align: center;
            background-color: rgba(255, 255, 255, 0.05);
            margin: 5px 0;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.15);
            transform: translateX(5px);
        }

        .nav-link::after {
            display: none;
        }
    }

    @media (max-width: 767.98px) {
        .logo {
            width: 60px;
        }

        .text-container {
            font-size: 0.9rem;
        }

        .hero-content h1 {
            font-size: 2rem;
        }

        .hero-banner {
            height: 400px;
        }

        .navbar-brand {
            font-size: 1rem;
        }
    }

    @media (max-width: 575.98px) {
        .full-height-container {
            width: 100%;
            border-radius: 0;
        }

        .top-navbar,
        .main-navbar {
            width: 100%;
            border-radius: 0;
        }

        .main-navbar {
            border-radius: 0 !important;
        }

        .logo {
            width: 50px;
        }

        .text-container {
            font-size: 0.8rem;
        }

        .top-navbar .btn {
            font-size: 0.85rem;
            padding: 6px 15px;
        }
    }

    /* Hero Banner Styles */
    .hero-banner {
        position: relative;
        height: 600px;
        background: url('{{ asset('images/church-banner.jpg') }}') center/cover no-repeat;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 40px;
        border-radius: 0;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        animation: fadeInUp 1s ease;
    }

    .hero-banner .overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.3));
    }

    .hero-content {
        position: relative;
        z-index: 2;
        max-width: 800px;
        padding: 20px;
        animation: fadeInUp 1.2s ease;
    }

    .hero-content h1 {
        font-size: 3.5rem;
        font-weight: 700;
        margin-bottom: 20px;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    }

    .hero-content p {
        font-size: 1.5rem;
        margin-bottom: 30px;
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
    }

    /* Quick Links Section */
    .quick-links-section {
        background: linear-gradient(135deg, #ffffff, #f1f8ff);
        padding: 50px 0;
        border-radius: 0;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03);
        animation: fadeInUp 1.4s ease;
    }

    .quick-link-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 30px 20px;
        height: 100%;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.04);
        transition: transform 0.4s ease, box-shadow 0.4s ease;
    }

    .quick-link-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08);
    }

    .quick-link-card i {
        color: #3f51b5;
        margin-bottom: 20px;
        transition: transform 0.3s ease;
    }

    .quick-link-card:hover i {
        transform: scale(1.2);
    }

    .quick-link-card h3 {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 15px;
        color: #1a237e;
    }

    .quick-link-card p {
        color: #555;
        margin-bottom: 20px;
    }
    
    /* Icon Circle */
    .icon-circle {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #f1f8ff, #e1eeff);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #3f51b5;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(63, 81, 181, 0.1);
    }
    
    .quick-link-card:hover .icon-circle {
        transform: scale(1.1);
        background: linear-gradient(135deg, #3f51b5, #1a237e);
        color: white;
        box-shadow: 0 8px 25px rgba(63, 81, 181, 0.3);
    }

    /* Additional Styles for Enhanced Home Page */
    .hover-shadow {
        transition: all 0.3s ease;
    }

    .hover-shadow:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1) !important;
    }

    .featured-sermon {
        position: relative;
        overflow: hidden;
        animation: fadeInUp 1.6s ease;
        padding: 60px 0;
        background-color: #f8faff;
    }

    .sermon-video-container {
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }
    
    .sermon-video-container:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .play-btn {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #3f51b5, #1a237e);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        transition: all 0.3s ease;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }
    
    .play-btn:hover {
        transform: scale(1.1);
        background: linear-gradient(135deg, #1a237e, #0d1b5e);
        color: white;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
    }
    
    .sermon-meta-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #f1f8ff, #e1eeff);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #3f51b5;
        margin-right: 15px;
    }

    
    .upcoming-events {
        animation: fadeInUp 2s ease;
        padding: 60px 0;
    }

    .upcoming-events .card-img-top {
        height: 200px;
        object-fit: cover;
        border-top-left-radius: 20px;
        border-top-right-radius: 20px;
    }
    
    .event-date-badge {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        padding: 10px 15px;
        text-align: center;
        transition: all 0.3s ease;
    }
    
    .card:hover .event-date-badge {
        transform: scale(1.05);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }
    
    .event-date-month {
        color: #3f51b5;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 2px;
    }
    
    .event-date-day {
        color: #1a237e;
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1;
    }
    
    .event-details {
        margin-top: 20px;
    }
    
    .event-detail {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }
    
    .event-detail i {
        width: 25px;
        margin-right: 10px;
    }
    
    .event-detail span {
        font-size: 0.9rem;
        color: #555;
    }
   
    html {
        scroll-behavior: smooth;
    }

    .newsletter-section {
        background: linear-gradient(135deg, #3f51b5, #1a237e);
        position: relative;
        overflow: hidden;
        border-radius: 0;
        box-shadow: 0 8px 25px rgba(63, 81, 181, 0.3);
        animation: fadeInUp 2.2s ease;
        margin-top: 60px;
    }

    .newsletter-section::before {
        content: "";
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background: url('{{ asset('images/pattern.png') }}') repeat;
        opacity: 0.1;
        z-index: 0;
    }

    .newsletter-section .container {
        position: relative;
        z-index: 1;
    }
    
    .newsletter-content {
        animation: fadeInLeft 1s ease;
    }
    
    .newsletter-form {
        animation: fadeInRight 1s ease;
        border-radius: 20px;
    }
    
    @keyframes fadeInLeft {
        0% {
            opacity: 0;
            transform: translateX(-30px);
        }
        100% {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes fadeInRight {
        0% {
            opacity: 0;
            transform: translateX(30px);
        }
        100% {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Add some depth to cards and elements */
    .card, .quick-link-card, .hero-banner, .ratio {
        border-radius: 20px;
        overflow: hidden;
    }

    /* Consistent button styling */
    .btn {
        border-radius: 50px;
        padding: 12px 30px;
        font-weight: 500;
        transition: all 0.3s;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .btn-primary {
        background: linear-gradient(to right, #3f51b5, #1a237e);
        border: none;
    }

    .btn-primary:hover {
        background: linear-gradient(to right, #1a237e, #0d1b5e);
        transform: translateY(-3px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
    }

    .btn-outline-primary {
        color: #3f51b5;
        border: 1px solid #3f51b5;
        background: transparent;
    }

    .btn-outline-primary:hover {
        background: linear-gradient(to right, #3f51b5, #1a237e);
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        border-color: transparent;
    }

    .text-primary {
        color: #3f51b5 !important;
    }

    .bg-primary {
        background: linear-gradient(to right, #3f51b5, #1a237e) !important;
    }

    /* Service Times & Location Styles */
    .services-location {
        background: linear-gradient(135deg, #ffffff, #f1f8ff);
        border-radius: 0;
        animation: fadeInUp 2.4s ease;
        padding: 60px 0;
    }

    .service-times, .location-map {
        transition: all 0.3s ease;
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.04);
    }

    .service-times:hover, .location-map:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1) !important;
    }

    .service-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #3f51b5;
        background-color: #f1f8ff;
        border-radius: 50%;
        font-size: 1.5rem;
        transition: all 0.3s ease;
    }
    
    .service-times:hover .service-icon,
    .location-map:hover .contact-icon {
        transform: scale(1.1);
        background-color: #3f51b5;
        color: white;
        box-shadow: 0 8px 15px rgba(63, 81, 181, 0.2);
    }

    .contact-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #3f51b5;
        background-color: #f1f8ff;
        border-radius: 50%;
        font-size: 1.2rem;
        transition: all 0.3s ease;
    }
    
    .address-details i {
        width: 25px;
        text-align: center;
        color: #3f51b5;
    }

    .map-container {
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        border: none;
        border-radius: 20px;
        overflow: hidden;
    }

    /* Dynamic events styling */
    .event-img-placeholder {
        border-top-left-radius: 20px;
        border-top-right-radius: 20px;
        transition: all 0.5s ease;
    }

    .card:hover .event-img-placeholder {
        transform: scale(1.05);
    }

    /* Rounded badges */
    .badge {
        border-radius: 50px;
        padding: 8px 15px;
        font-weight: 500;
    }

    /* Footer styling */
    .footer {
        background-color: #1a237e;
        color: #fff;
        padding: 60px 0 30px;
        border-radius: 20px 20px 0 0;
        margin-top: 60px;
    }

    .footer h5 {
        color: #fff;
        font-weight: 600;
        margin-bottom: 20px;
        position: relative;
        padding-bottom: 10px;
    }

    .footer h5::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        height: 2px;
        width: 50px;
        background-color: #fff;
    }

    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-links li {
        margin-bottom: 10px;
    }

    .footer-links a {
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        transition: all 0.3s;
    }

    .footer-links a:hover {
        color: #fff;
        padding-left: 5px;
    }

    .social-icons a {
        display: inline-block;
        width: 40px;
        height: 40px;
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        text-align: center;
        line-height: 40px;
        margin-right: 10px;
        color: #fff;
        transition: all 0.3s;
    }

    .social-icons a:hover {
        background-color: #fff;
        color: #1a237e;
        transform: translateY(-3px);
    }

    .copyright {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        padding-top: 20px;
        margin-top: 40px;
    }
</style>
<body>
    <!-- Flash Messages -->
    @include('partials.flash-messages')
    
    <!-- Mobile Top Navigation Bar (Facebook Style) -->
    <div class="mobile-top-nav" id="mobileTopNav">
        <div class="mobile-nav-container">
            <!-- Home -->
            <a href="{{ url('/') }}" class="mobile-nav-item {{ request()->is('/') ? 'active' : '' }}">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
            
            <!-- Events -->
            <a href="#events" class="mobile-nav-item {{ request()->is('events*') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i>
                <span>Events</span>
            </a>
            
            <!-- Sermons -->
            <a href="#sermons" class="mobile-nav-item {{ request()->is('sermons*') ? 'active' : '' }}">
                <i class="fas fa-microphone-alt"></i>
                <span>Sermons</span>
            </a>
            
            <!-- About -->
            <a href="#about" class="mobile-nav-item {{ request()->is('about*') ? 'active' : '' }}">
                <i class="fas fa-info-circle"></i>
                <span>About</span>
            </a>
            
            <!-- Contact -->
            <a href="#contact" class="mobile-nav-item {{ request()->is('contact*') ? 'active' : '' }}">
                <i class="fas fa-envelope"></i>
                <span>Contact</span>
            </a>
            
            <!-- Menu Dropdown -->
            <div class="mobile-nav-item" id="mobileMenuDropdownBtn">
                <i class="fas fa-bars"></i>
                <span>Menu</span>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>

    <!-- Mobile Menu Dropdown -->
    <div class="mobile-menu-dropdown" id="mobileMenuDropdown">
        <a href="#" class="mobile-dropdown-item">
            <i class="fas fa-cross"></i>
            Ministries
        </a>
        
        <a href="#outreach" class="mobile-dropdown-item">
            <i class="fas fa-hands-helping"></i>
            Outreach
        </a>
        
        <a href="#services" class="mobile-dropdown-item">
            <i class="fas fa-church"></i>
            Services
        </a>
        
        <div class="mobile-dropdown-divider"></div>
        
        @guest
            <a href="{{ route('login') }}" class="mobile-dropdown-item">
                <i class="fas fa-sign-in-alt"></i>
                Login
            </a>
            <a href="{{ route('register') }}" class="mobile-dropdown-item">
                <i class="fas fa-user-plus"></i>
                Register
            </a>
        @else
            <a href="{{ route('logout') }}" class="mobile-dropdown-item"
               onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
            <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        @endguest
    </div>

    <div id="app">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light top-navbar">
            <div class="container">
                <div class="navbar-left">
                    <a href="{{ url('/') }}" class="logo-container">
                        <img src="{{ asset('images/blessed.logo.jpg') }}" alt="Logo" class="logo">
                    </a>
                    <h4 class="text-container mb-0">Jesus Blessed Full Gospel Church</h4>
                </div>
                <div class="navbar-right">
                    <!-- Authentication Links -->
                    @guest
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-user me-1"></i> Login</a>
                        @endif
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-sm btn-primary"><i class="fas fa-user-plus me-1"></i> Register</a>
                        @endif
                    @else
                        <div class="dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-1"></i> {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    @endguest
                </div>
            </div>
        </nav>
        
        <!-- Main Navigation Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark main-navbar shadow-sm">
            <div class="container">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item">
                            <a class="nav-link text-white {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">
                                <i class="fas fa-home me-1"></i> Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white {{ request()->is('about*') ? 'active' : '' }}" href="#">
                                <i class="fas fa-info-circle me-1"></i> About Us
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white {{ request()->is('ministries*') ? 'active' : '' }}" href="#">
                                <i class="fas fa-cross me-1"></i> Ministries
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white {{ request()->is('events*') ? 'active' : '' }}" href="#events">
                                <i class="fas fa-calendar-alt me-1"></i> Events
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white {{ request()->is('sermons*') ? 'active' : '' }}" href="#sermons">
                                <i class="fas fa-microphone-alt me-1"></i> Sermons
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white {{ request()->is('outreach*') ? 'active' : '' }}" href="#outreach">
                                <i class="fas fa-hands-helping me-1"></i> Outreach
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white {{ request()->is('contact*') ? 'active' : '' }}" href="#contact">
                                <i class="fas fa-envelope me-1"></i> Contact
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        
        <div class="full-height-container">
        <!-- Hero Banner Section -->
        @if(request()->is('/'))
        <div class="hero-banner">
            <div class="overlay"></div>
            <div class="hero-content text-center animate__animated animate__fadeIn">
                <span class="badge bg-primary mb-3 px-3 py-2">WELCOME TO OUR CHURCH</span>
                <h1 class="animate__animated animate__fadeInUp animate__delay-1s">Jesus Blessed Full Gospel Church</h1>
                <p class="lead animate__animated animate__fadeInUp animate__delay-2s">Growing in faith, serving with love, building community</p>
                <div class="hero-buttons mt-4 animate__animated animate__fadeInUp animate__delay-3s">
                    <a href="#services" class="btn btn-primary btn-lg me-2">Our Services</a>
                    <a href="#events" class="btn btn-outline-light btn-lg">Upcoming Events</a>
                </div>
            </div>
        </div>

        <!-- Our Mission Section -->
        <div class="mission-section py-5">
            <div class="container text-center">
                <span class="badge bg-primary px-3 py-2 mb-2">OUR MISSION</span>
                <h2 class="mb-4">Loving God, Loving People, Changing Lives</h2>
                <div class="row">
                    <div class="col-md-4">
                        <i class="fas fa-heart fa-3x text-primary mb-3"></i>
                        <h4>Love</h4>
                        <p>We love God with all our hearts and love our neighbors as ourselves</p>
                    </div>
                    <div class="col-md-4">
                        <i class="fas fa-people-group fa-3x text-primary mb-3"></i>
                        <h4>Community</h4>
                        <p>Building authentic relationships and supporting one another</p>
                    </div>
                    <div class="col-md-4">
                        <i class="fas fa-seedling fa-3x text-primary mb-3"></i>
                        <h4>Growth</h4>
                        <p>Growing in faith and helping others discover their purpose</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
        <!-- End Home Sections -->

        @if(request()->is('/'))
        <!-- Featured Sermon Section -->
        <div class="featured-sermon py-5 " id="sermons">
            <div class="container">
                <div class="text-center mb-5">
                    <span class="badge bg-primary px-3 py-2 mb-2">LATEST MESSAGE</span>
                    <h2 class="mb-3">Featured Sermon</h2>
                    <p class="lead text-muted">Deepen your faith with our latest teachings</p>
                </div>
                <div class="row">
                    @inject('sermonModel', 'App\Models\Sermon')
                    @php
                        // Fetch the latest featured sermon
                        $latestSermon = $sermonModel::where('featured', true)
                            ->latest('date_preached')
                            ->first();
                        
                        // If no featured sermon exists, get the most recent sermon
                        if (!$latestSermon) {
                            $latestSermon = $sermonModel::latest('date_preached')->first();
                        }
                    @endphp
                    
                    @if($latestSermon)
                    <div class="col-lg-6">
                        <div class="sermon-video-container position-relative overflow-hidden rounded-4 shadow-lg">
                            <div class="ratio ratio-16x9">
                                @if($latestSermon->thumbnail_path)
                                    <img src="{{ asset('storage/' . $latestSermon->thumbnail_path) }}" alt="{{ $latestSermon->title }}" class="img-fluid">
                                @else
                                    <div class="d-flex align-items-center justify-content-center bg-dark h-100">
                                        <i class="fas fa-microphone-alt fa-4x text-white opacity-50"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="position-absolute top-50 start-50 translate-middle">
                                <a href="{{ route('member.sermons.index') }}" class="play-btn">
                                    <i class="fas fa-play"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 d-flex flex-column justify-content-center mt-4 mt-lg-0">
                        <h6 class="text-primary fw-bold">LATEST SERMON</h6>
                        <h2 class="mb-3">{{ $latestSermon->title }}</h2>
                        <p class="lead mb-4">{{ \Illuminate\Support\Str::limit($latestSermon->description, 150) }}</p>
                        <div class="sermon-meta mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="sermon-meta-icon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <span>{{ \Carbon\Carbon::parse($latestSermon->date_preached)->format('F d, Y') }}</span>
                            </div>
                            @if($latestSermon->speaker_name)
                            <div class="d-flex align-items-center mb-3">
                                <div class="sermon-meta-icon">
                                    <i class="fas fa-user"></i>
                                </div>
                                <span>{{ $latestSermon->speaker_name }}</span>
                            </div>
                            @endif
                            @if($latestSermon->scripture_reference)
                            <div class="d-flex align-items-center mb-3">
                                <div class="sermon-meta-icon">
                                    <i class="fas fa-bible"></i>
                                </div>
                                <span>{{ $latestSermon->scripture_reference }}</span>
                            </div>
                            @endif
                        </div>
                        <div>
                            <a href="{{ route('member.sermons.index') }}" class="btn btn-primary me-2">Watch Now</a>
                            <a href="{{ route('member.sermons.index') }}" class="btn btn-outline-primary">View All Sermons</a>
                        </div>
                    </div>
                    @else
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-microphone-slash fa-3x text-muted mb-3"></i>
                        <h3 class="h4">No sermons available</h3>
                        <p class="text-muted">Check back soon for new sermon content.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Upcoming Events Section with Modern Card Design -->
        <div class="upcoming-events py-5" id="events">
            <div class="container">
                <div class="text-center mb-5">
                    <span class="badge bg-primary px-3 py-2 mb-2">JOIN US</span>
                    <h2 class="mb-3">Upcoming Events</h2>
                    <p class="lead text-muted">Be part of our vibrant community through these upcoming activities</p>
                </div>
                <div class="row">
                    @inject('eventModel', 'App\Models\Event')
                    @php
                        // Fetch upcoming events from the database
                        $upcomingEvents = $eventModel::where('event_date', '>=', now())
                            ->orderBy('event_date', 'asc')
                            ->take(3)
                            ->get();
                    @endphp
                    
                    @if(isset($upcomingEvents) && $upcomingEvents->count() > 0)
                        @foreach($upcomingEvents as $event)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 border-0 shadow hover-shadow">
                                <div class="position-relative">
                                    <div class="card-img-top event-img-placeholder d-flex align-items-center justify-content-center" style="height: 200px; background: linear-gradient(135deg, {{ $event->color ?? '#3f51b5' }}, {{ $event->color ? 'darken('.$event->color.', 15%)' : '#1a237e' }});">
                                        <i class="fas fa-calendar-day fa-3x text-white opacity-50"></i>
                                    </div>
                                    <div class="position-absolute top-0 start-0 m-3">
                                        <div class="event-date-badge">
                                            <div class="event-date-month">{{ \Carbon\Carbon::parse($event->event_date)->format('M') }}</div>
                                            <div class="event-date-day">{{ \Carbon\Carbon::parse($event->event_date)->format('d') }}</div>
                                        </div>
                                    </div>
                                    @if($event->poll)
                                    <div class="position-absolute top-0 end-0 m-3">
                                        <span class="badge bg-info rounded-pill">
                                            <i class="fas fa-poll me-1"></i> Poll
                                        </span>
                                    </div>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">{{ $event->title }}</h5>
                                    <p class="card-text text-muted">{{ \Illuminate\Support\Str::limit($event->description, 100) }}</p>
                                    <div class="event-details">
                                        <div class="event-detail">
                                            <i class="fas fa-clock text-primary"></i>
                                            <span>{{ $event->event_time ? \Carbon\Carbon::parse($event->event_time)->format('g:i A') : 'All Day' }}</span>
                                        </div>
                                        <div class="event-detail">
                                            <i class="fas fa-map-marker-alt text-primary"></i>
                                            <span>{{ $event->location }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-white border-0 pt-0">
                                    <a href="{{ route('member.events.show', $event->id) }}" class="btn btn-sm btn-outline-primary">View Details</a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="col-12">
                            <div class="alert alert-info text-center py-4">
                                <i class="fas fa-calendar-times fa-2x mb-3"></i>
                                <p class="mb-0">No upcoming events scheduled at this time. Check back soon!</p>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="text-center mt-4">
                    <a href="{{ route('member.events.index') }}" class="btn btn-primary">View All Events</a>
                </div>
            </div>
        </div>

       

        <!-- Service Times & Location Section -->
        <div class="services-location py-5">
            <div class="container">
                <div class="text-center mb-5">
                    <span class="badge bg-primary px-3 py-2 mb-2">VISIT US</span>
                    <h2 class="mb-3">Service Times & Location</h2>
                    <p class="lead text-muted">Join us for worship and fellowship</p>
                </div>
                <div class="row">
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <div class="service-times p-4 h-100">
                            <h3 class="mb-4 border-bottom pb-2 d-flex align-items-center">
                                <i class="fas fa-clock text-primary me-2"></i> Service Times
                            </h3>
                            <div class="d-flex align-items-start mb-4">
                                <div class="service-icon me-3">
                                    <i class="fas fa-church"></i>
                                </div>
                                <div>
                                    <h5>Sunday Worship</h5>
                                    <p class="mb-1">9:00 AM - First Service</p>
                                    <p class="mb-1">11:30 AM - Second Service</p>
                                    <p class="mb-0 text-muted small">Children's ministry available at both services</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-start mb-4">
                                <div class="service-icon me-3">
                                    <i class="fas fa-book-open"></i>
                                </div>
                                <div>
                                    <h5>Bible Study</h5>
                                    <p class="mb-1">Wednesday: 6:30 PM</p>
                                    <p class="mb-0 text-muted small">Deep dive into scripture with fellowship</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-start">
                                <div class="service-icon me-3">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div>
                                    <h5>Youth Group</h5>
                                    <p class="mb-1">Friday: 7:00 PM</p>
                                    <p class="mb-0 text-muted small">For teens and young adults</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="location-map p-4 h-100">
                            <h3 class="mb-4 border-bottom pb-2 d-flex align-items-center">
                                <i class="fas fa-map-marker-alt text-primary me-2"></i> Find Us
                            </h3>
                            <div class="map-container mb-4">
                                <!-- Replace with your actual map embed code -->
                                <div class="ratio ratio-16x9 h-100">
                                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3456.789012345678!2d-122.123456!3d47.123456!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDfCsDA3JzI0LjQiTiAxMjLCsDA3JzI0LjQiVw!5e0!3m2!1sen!2sus!4v1623456789012!5m2!1sen!2sus" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                                </div>
                            </div>
                            <div class="address-details">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="contact-icon me-3">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">Church Address</h5>
                                        <p class="mb-0">123 Blessing Avenue, Faith City, FC 12345</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start mb-3">
                                    <div class="contact-icon me-3">
                                        <i class="fas fa-phone"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">Phone</h5>
                                        <p class="mb-0">(123) 456-7890</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start">
                                    <div class="contact-icon me-3">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">Email</h5>
                                        <p class="mb-0">info@blessedfullgospel.org</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Newsletter Section -->
        <div class="newsletter-section py-5">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <div class="newsletter-content">
                            <span class="badge bg-light text-primary px-3 py-2 mb-3">STAY CONNECTED</span>
                            <h2 class="text-white mb-3">Join Our Community</h2>
                            <p class="lead text-white-50 mb-0">Subscribe to our newsletter to receive updates on church events, sermons, and community activities.</p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="newsletter-form bg-white p-4 rounded-4 shadow">
                            <h5 class="mb-3">Subscribe to Our Newsletter</h5>
                            <form class="row g-3">
                                <div class="col-md-8">
                                    <div class="form-floating">
                                        <input type="email" class="form-control" id="newsletterEmail" placeholder="Your email address">
                                        <label for="newsletterEmail">Your email address</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary h-100 w-100">Subscribe</button>
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="privacyCheck">
                                        <label class="form-check-label small text-muted" for="privacyCheck">
                                            I agree to the privacy policy and terms
                                        </label>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <main class="py-4">
            @yield('content')
        </main>
        
        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 mb-4 mb-lg-0">
                        <h5>About Us</h5>
                        <p class="text-white-50">Jesus Blessed Full Gospel Church is a vibrant, welcoming community dedicated to spreading God's love and message through worship, fellowship, and service.</p>
                        <div class="social-icons mt-4">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
                        <h5>Quick Links</h5>
                        <ul class="footer-links">
                            <li><a href="{{ url('/') }}">Home</a></li>
                            <li><a href="#">About Us</a></li>
                            <li><a href="#">Ministries</a></li>
                            <li><a href="#">Events</a></li>
                            <li><a href="#">Sermons</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-3 col-md-4 mb-4 mb-md-0">
                        <h5>Services</h5>
                        <ul class="footer-links">
                            <li><a href="#">Sunday Worship</a></li>
                            <li><a href="#">Bible Study</a></li>
                            <li><a href="#">Youth Ministry</a></li>
                            <li><a href="#">Children's Church</a></li>
                            <li><a href="#">Prayer Meetings</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <h5>Contact Us</h5>
                        <ul class="footer-links">
                            <li><i class="fas fa-map-marker-alt me-2"></i> 123 Blessing Avenue, Faith City</li>
                            <li><i class="fas fa-phone me-2"></i> (123) 456-7890</li>
                            <li><i class="fas fa-envelope me-2"></i> info@blessedfullgospel.org</li>
                        </ul>
                        <div class="mt-3">
                            <a href="#" class="btn btn-sm btn-outline-light">Contact Us</a>
                        </div>
                    </div>
                </div>
                <div class="copyright text-center text-white-50">
                    <p>&copy; {{ date('Y') }} Jesus Blessed Full Gospel Church. All Rights Reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</div>

<!-- Initialize Bootstrap tooltips and smooth scrolling -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if we're on mobile
        function isMobile() {
            return window.innerWidth <= 991.98;
        }

        // Show/hide mobile navigation
        function toggleMobileNav() {
            const mobileNav = document.getElementById('mobileTopNav');
            const topNavbar = document.querySelector('.top-navbar');
            const mainNavbar = document.querySelector('.main-navbar');
            
            if (isMobile()) {
                mobileNav.classList.add('show');
                document.body.classList.add('mobile-nav-active');
                if (topNavbar) topNavbar.style.display = 'none';
                if (mainNavbar) mainNavbar.style.display = 'none';
            } else {
                mobileNav.classList.remove('show');
                document.body.classList.remove('mobile-nav-active');
                if (topNavbar) topNavbar.style.display = 'block';
                if (mainNavbar) mainNavbar.style.display = 'block';
            }
        }

        // Initialize mobile navigation
        toggleMobileNav();

        // Handle window resize
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(toggleMobileNav, 250);
        });

        // Mobile dropdown menu functionality
        const mobileMenuDropdownBtn = document.getElementById('mobileMenuDropdownBtn');
        const mobileMenuDropdown = document.getElementById('mobileMenuDropdown');
        const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
        
        if (mobileMenuDropdownBtn) {
            mobileMenuDropdownBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const isShown = mobileMenuDropdown.classList.contains('show');
                
                if (isShown) {
                    mobileMenuDropdown.classList.remove('show');
                    mobileMenuOverlay.classList.remove('show');
                    mobileMenuDropdownBtn.classList.remove('active');
                } else {
                    mobileMenuDropdown.classList.add('show');
                    mobileMenuOverlay.classList.add('show');
                    mobileMenuDropdownBtn.classList.add('active');
                }
            });
        }

        // Close dropdown when clicking overlay
        if (mobileMenuOverlay) {
            mobileMenuOverlay.addEventListener('click', function() {
                mobileMenuDropdown.classList.remove('show');
                mobileMenuOverlay.classList.remove('show');
                mobileMenuDropdownBtn.classList.remove('active');
            });
        }

        // Close dropdown when clicking dropdown items
        const dropdownItems = document.querySelectorAll('.mobile-dropdown-item');
        dropdownItems.forEach(item => {
            item.addEventListener('click', function() {
                mobileMenuDropdown.classList.remove('show');
                mobileMenuOverlay.classList.remove('show');
                mobileMenuDropdownBtn.classList.remove('active');
            });
        });

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Smooth scroll and active nav
        const navLinks = document.querySelectorAll('.nav-link[href^="#"], .mobile-nav-item[href^="#"]');
        const sections = document.querySelectorAll('[id]');
        
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const targetSection = document.querySelector(targetId);
                
                if (targetSection) {
                    targetSection.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    
                    // Close mobile menu after clicking
                    const navbarCollapse = document.getElementById('navbarNav');
                    if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                        bootstrap.Collapse.getInstance(navbarCollapse).hide();
                    }

                    // Close mobile dropdown
                    if (mobileMenuDropdown) {
                        mobileMenuDropdown.classList.remove('show');
                        mobileMenuOverlay.classList.remove('show');
                        mobileMenuDropdownBtn.classList.remove('active');
                    }
                }
            });
        });
        
        // Update active nav on scroll
        window.addEventListener('scroll', function() {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop - 100;
                if (scrollY >= sectionTop) {
                    current = section.getAttribute('id');
                }
            });
            
            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.add('active');
                }
            });
        });

        // Auto-close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const navbarCollapse = document.getElementById('navbarNav');
            const navbarToggler = document.querySelector('.navbar-toggler');
            
            if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                if (!navbarCollapse.contains(event.target) && !navbarToggler.contains(event.target)) {
                    bootstrap.Collapse.getInstance(navbarCollapse).hide();
                }
            }
        });
    });
</script>

<!-- Optimized Notifications -->
@include('layouts.websocket-config')

<!-- Firebase Scripts -->
<script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-database.js"></script>
<script src="/js/firebase-config.js"></script>
</body>
</html>