<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="user-id" content="{{ auth()->check() ? auth()->id() : '' }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard')</title>

    <!-- Core Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.11.3/echo.iife.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.10.5/dist/cdn.min.js" defer></script>

    <!-- Font & Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/@videojs/themes@1/dist/forest/index.css" rel="stylesheet" />
    <!-- Calendar -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/main.min.js"></script>

    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4895ef;
            --light-bg: #f8f9fa;
            --dark-text: #212529;
            --light-text: #6c757d;
            --sidebar-width: 260px;
            --sidebar-collapsed: 70px;
            --header-height: 64px;
            --box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            --card-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
            --border-radius: 12px;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f7f9fc;
            color: var(--dark-text);
            overflow-x: hidden;
            padding-top: 70px;
        }

        /* Mobile Top Navigation Bar */
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
            height: 70px;
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
            color: #4361ee;
            text-decoration: none;
            background-color: rgba(67, 97, 238, 0.05);
            transform: translateY(-2px);
        }

        .mobile-nav-item.active {
            color: #4361ee;
            background-color: rgba(67, 97, 238, 0.1);
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(67, 97, 238, 0.2);
        }

        .mobile-nav-item i {
            font-size: 20px;
            margin-bottom: 2px;
            transition: all 0.2s ease;
        }

        .mobile-nav-item.active i {
            transform: scale(1.1);
            color: #4361ee;
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

        /* Mobile nav badges */
        .mobile-nav-badge {
            position: absolute;
            top: 4px;
            right: 8px;
            background: #ff3040;
            color: white;
            border-radius: 10px;
            font-size: 10px;
            font-weight: 600;
            min-width: 16px;
            height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
            border: 2px solid white;
            box-shadow: 0 2px 4px rgba(255, 48, 64, 0.3);
            animation: mobileBadgePulse 2s infinite;
        }

        @keyframes mobileBadgePulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        /* Mobile dropdown menu */
        .mobile-menu-dropdown {
            position: absolute;
            top: 100%;
            right: 10px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            min-width: 180px;
            margin-top: 10px;
            padding: 8px 0;
            display: none;
            animation: fadeInScaleDown 0.2s cubic-bezier(0.4, 0, 0.2, 1);
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

        /* Header */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: var(--header-height);
            background-color: #ffffff;
            box-shadow: var(--card-shadow);
            z-index: 1000;
            padding: 0 1.5rem;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease;
        }

        .navbar.navbar-hidden {
            transform: translateY(-100%);
            opacity: 0;
            pointer-events: none;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--primary-color);
        }

        .mobile-menu-btn {
            color: #495057 !important;
            border: none !important;
            padding: 10px 12px !important;
            border-radius: 8px !important;
            transition: all 0.2s ease !important;
            background: transparent !important;
            font-size: 1.1rem !important;
            min-width: 44px;
            min-height: 44px;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            z-index: 1030 !important;
            position: relative !important;
        }

        .mobile-menu-btn:hover {
            background-color: #f8f9fa !important;
            color: #4361ee !important;
            transform: scale(1.05);
        }

        .mobile-menu-btn:focus {
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25) !important;
        }

        .mobile-menu-btn i {
            font-size: 1.1rem !important;
            color: inherit !important;
        }

        #toggle-sidebar {
            background: none;
            border: none;
            font-size: 1.2rem;
            color: var(--light-text);
            cursor: pointer;
            transition: var(--transition);
        }

        #toggle-sidebar:hover {
            color: var(--primary-color);
        }

        .navbar-nav .nav-item {
            margin: 0 0.5rem;
        }

        .navbar-nav .nav-link {
            color: var(--light-text);
            transition: var(--transition);
            position: relative;
            padding: 0.5rem;
        }

        .navbar-nav .nav-link:hover {
            color: var(--primary-color);
        }

        .navbar .dropdown-menu {
            border-radius: var(--border-radius);
            border: none;
            box-shadow: var(--box-shadow);
            margin-top: 0.5rem;
            animation: fadeIn 0.2s ease-in-out;
        }

        .navbar .dropdown-item {
            padding: 0.7rem 1.2rem;
            font-size: 0.9rem;
            transition: var(--transition);
        }

        .navbar .dropdown-item:hover {
            background-color: rgba(67, 97, 238, 0.05);
            color: var(--primary-color);
        }

        /* Search Bar */


        /* Sidebar */
        .sidebar {
            position: fixed;
            top: var(--header-height);
            left: 0;
            height: calc(100vh - var(--header-height));
            width: var(--sidebar-width);
            background-color: #ffffff;
            box-shadow: var(--card-shadow);
            overflow-y: auto;
            transition: var(--transition);
            z-index: 990;
            padding: 1rem 0;
            border-right: 1px solid rgba(0,0,0,0.05);
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed);
        }

        .sidebar.hidden {
            width: 0;
            overflow: hidden;
            transform: translateX(-100%);
        }

        /* Overlay for mobile */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0.6) 100%);
            backdrop-filter: blur(4px);
            z-index: 989;
            display: none;
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }

        .sidebar-overlay.active {
            display: block;
            opacity: 1;
            animation: fadeInOverlay 0.3s ease-out;
        }

        @keyframes fadeInOverlay {
            from {
                opacity: 0;
                backdrop-filter: blur(0px);
            }
            to {
                opacity: 1;
                backdrop-filter: blur(4px);
            }
        }

        .menu-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.9rem 1.5rem;
            margin: 0.3rem 1rem;
            color: var(--light-text);
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
            font-weight: 500;
            font-size: 0.95rem;
            position: relative;
            overflow: hidden;
            text-decoration: none;
        }

        .menu-item i {
            min-width: 1.5rem;
            margin-right: 0.5rem;
            font-size: 1.1rem;
            transition: transform 0.2s ease;
        }

        .menu-item:hover {
            background-color: rgba(67, 97, 238, 0.08);
            color: var(--primary-color);
        }

        .menu-item.active {
            background-color: var(--primary-color);
            color: #fff;
            box-shadow: 0 4px 10px rgba(67, 97, 238, 0.25);
        }

        .menu-item.active:hover {
            background-color: var(--secondary-color);
        }

        .menu-item.active i {
            color: #fff;
        }

        .menu-text, .menu-chevron {
            transition: var(--transition);
        }

        .sidebar.collapsed .menu-text,
        .sidebar.collapsed .menu-chevron {
            display: none;
        }

        .sidebar.collapsed .menu-item {
            padding: 0.9rem;
            justify-content: center;
            margin: 0.3rem auto;
            width: 45px;
        }

        .sidebar.collapsed .menu-item i {
            margin-right: 0;
            font-size: 1.2rem;
        }

        .submenu {
            margin-left: 1rem;
            margin-right: 1rem;
            padding: 0.2rem 0;
            display: none;
            background-color: rgba(247, 249, 252, 0.7);
            border-radius: var(--border-radius);
            border-left: 2px solid rgba(67, 97, 238, 0.1);
            margin-top: 0.2rem;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }

        .submenu a {
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            color: var(--light-text);
            text-decoration: none;
            font-size: 0.9rem;
            border-radius: calc(var(--border-radius) - 4px);
            margin: 0.2rem 0.5rem;
            transition: var(--transition);
            position: relative;
        }

        .submenu a:hover {
            background-color: rgba(67, 97, 238, 0.08);
            color: var(--primary-color);
            transform: translateX(3px);
        }

        .submenu a.active {
            background-color: rgba(67, 97, 238, 0.1);
            color: var(--primary-color);
            font-weight: 500;
        }

        .submenu a i, .submenu a span {
            margin-right: 0.5rem;
        }

        /* Sidebar footer */
        .sidebar-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 1rem;
            border-top: 1px solid rgba(0,0,0,0.05);
            background-color: #fff;
            display: flex;
            justify-content: center;
            transition: var(--transition);
        }

        .sidebar.collapsed .sidebar-footer {
            padding: 1rem 0;
        }

        /* Improve chevron animation */
        .menu-item .menu-chevron {
            transition: transform 0.3s ease;
        }

        .menu-item.open .menu-chevron {
            transform: rotate(-180deg);
        }

        /* Content Area */
        .content {
            margin-left: var(--sidebar-width);
            margin-top: var(--header-height);
            padding: 2rem;
            transition: var(--transition);
        }

        .content.collapsed {
            margin-left: var(--sidebar-collapsed);
        }

        .content.hidden {
            margin-left: 0;
        }

        /* Cards and Widgets */
        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            margin-bottom: 1.5rem;
            transition: var(--transition);
        }

        .card:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.25rem 1.5rem;
            font-weight: 600;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* User Profile */
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* Hide navbar icons when sidebar is minimized */
        .navbar .nav-item.hide-on-minimize {
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .sidebar.hidden ~ .content .navbar .nav-item.hide-on-minimize,
        .sidebar.collapsed ~ .content .navbar .nav-item.hide-on-minimize {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        .sidebar:not(.hidden):not(.collapsed) ~ .content .navbar .nav-item.hide-on-minimize {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        /* Responsive */
        @media (max-width: 992px) {
            body {
                padding-top: 70px;
            }

            .sidebar {
                width: 0;
                transform: translateX(-100%);
                top: 70px;
                height: calc(100vh - 70px);
            }

            .sidebar.show {
                transform: translateX(0);
                width: var(--sidebar-width);
                box-shadow: 0 0 30px rgba(0, 0, 0, 0.3);
            }

            .sidebar.show .menu-text,
            .sidebar.show .menu-chevron {
                display: block;
            }

            .content {
                margin-left: 0;
                padding-bottom: 80px;
            }

            .content.collapsed {
                margin-left: 0;
            }

            .content.hidden {
                margin-left: 0;
            }

            .mobile-top-nav.show {
                display: block !important;
            }

            .navbar.navbar-hidden {
                transform: translateY(-100%);
                opacity: 0;
                pointer-events: none;
            }
        }

        @media (max-width: 767.98px) {
            .sidebar.show {
                width: 100%;
                max-width: 300px;
            }

            .content {
                padding: 15px 15px 20px 15px;
            }

            .menu-item {
                padding: 14px 18px;
                margin: 3px 12px 3px 0;
                border-radius: 0 30px 30px 0;
                font-size: 1rem;
                min-height: 52px;
            }

            .menu-item i {
                min-width: 24px;
                font-size: 1.2rem;
            }
        }

        @media (max-width: 576px) {
            .navbar-brand span {
                display: none;
            }

            .search-bar {
                display: none;
            }

            .content {
                padding: 10px 10px 90px 10px;
            }

            .sidebar.show {
                width: 100%;
                max-width: 300px;
            }

            .menu-item {
                padding: 16px 20px;
                margin: 4px 15px 4px 0;
                border-radius: 0 35px 35px 0;
                font-size: 1.1rem;
                min-height: 56px;
            }

            .menu-item i {
                min-width: 28px;
                font-size: 1.3rem;
            }
        }

        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }

        /* Utilities */
        .page-title {
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: var(--dark-text);
        }

        .breadcrumb {
            margin-bottom: 1.5rem;
        }

        .breadcrumb-item a {
            color: var(--light-text);
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: var(--primary-color);
        }
    </style>
</head>
@stack('scripts')

<body>
    <!-- Global Configuration -->
    <script type="module">
        window.pusherKey = "{{ env('PUSHER_APP_KEY') }}";
        window.pusherCluster = "{{ env('PUSHER_APP_CLUSTER') }}";
        window.authId = {{ Auth::id() }};
    </script>
    <!-- Firebase scripts -->
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-database.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-storage.js"></script>
    <script src="/js/firebase-config.js"></script>
    <!-- Optimized Notifications -->
    @include('layouts.websocket-config')

    <!-- Your main JS file -->
    <script type="module" src="{{ asset('js/app.js') }}" defer></script>

    <!-- Mobile Top Navigation Bar -->
    <div class="mobile-top-nav" id="mobileTopNav">
        <div class="mobile-nav-container">
            <!-- Dashboard -->
            <a href="/admin/main" class="mobile-nav-item {{ request()->is('admin/main') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>

            <!-- Members -->
            <a href="/admin/members" class="mobile-nav-item {{ request()->is('admin/members*') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Members</span>
            </a>

            <!-- Events -->
            <a href="/admin/events" class="mobile-nav-item {{ request()->is('admin/events*') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i>
                <span>Events</span>
            </a>

            <!-- Sermons -->
            <a href="/admin/sermons" class="mobile-nav-item {{ request()->is('admin/sermons*') ? 'active' : '' }}">
                <i class="fas fa-bible"></i>
                <span>Sermons</span>
            </a>

            <!-- Messages -->
            <a href="{{ route('admin.admin.chat') }}" class="mobile-nav-item {{ request()->is('admin/chat*') ? 'active' : '' }}">
                <i class="fas fa-envelope"></i>
                <span>Messages</span>
                <span id="mobile-msg-badge" class="mobile-nav-badge" style="display: none;">0</span>
            </a>

            <!-- Menu Dropdown -->
            <div class="mobile-nav-item" id="mobileMenuDropdownBtn">
                <i class="fas fa-bars"></i>
                <span>More</span>

                <!-- Dropdown Menu -->
                <div class="mobile-menu-dropdown" id="mobileMenuDropdown">
                    <a href="/admin/donations" class="mobile-dropdown-item">
                        <i class="fas fa-hand-holding-usd"></i>
                        Donations
                    </a>

                    <a href="/admin/announcements" class="mobile-dropdown-item">
                        <i class="fas fa-bullhorn"></i>
                        Announcements
                    </a>

                    <a href="/broadcaster" class="mobile-dropdown-item">
                        <i class="fas fa-video"></i>
                        Live Stream
                    </a>

                    <a href="{{ route('admin.logs') }}" class="mobile-dropdown-item">
                        <i class="fas fa-history"></i>
                        User Logs
                    </a>

                    <div class="mobile-dropdown-divider"></div>

                    <a href="{{ route('admin.admin.profile') }}" class="mobile-dropdown-item">
                        <i class="fas fa-user"></i>
                        Profile
                    </a>

                    <a href="{{ route('admin.settings') }}" class="mobile-dropdown-item">
                        <i class="fas fa-cog"></i>
                        Settings
                    </a>

                    <div class="mobile-dropdown-divider"></div>

                    <a href="{{ route('logout') }}" class="mobile-dropdown-item"
                       onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>

                    <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Overlay for mobile -->
    <div class="sidebar-overlay"></div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <!-- Brand -->
            <div class="d-flex align-items-center">
                <button class="btn btn-link d-md-none mobile-menu-btn" type="button" id="mobileMenuBtn" aria-label="Toggle navigation menu">

                </button>
                <button id="toggle-sidebar" class="me-3 d-none d-md-block">
                    <i class="fas fa-bars"></i>
                </button>
                <a class="navbar-brand d-flex align-items-center" href="#">
                    <i class="fas fa-church me-2 text-primary"></i>
                    <span>Admin Dashboard</span>
                </a>
            </div>

            <!-- Right Side Icons -->
            <ul class="navbar-nav d-flex align-items-center">
                <!-- Messages Icon -->
                <li class="nav-item hide-on-minimize">
                    <a class="nav-link" href="{{ route('admin.admin.chat') }}">
                        <i class="far fa-envelope"></i>
                        <span id="admin-msg-badge" class="badge bg-danger rounded-pill" style="display: none;">0</span>
                    </a>
                </li>




                <!-- User Profile Dropdown -->
                <li class="nav-item dropdown ms-3">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : 'https://via.placeholder.com/40' }}" class="user-avatar me-2" alt="User Avatar">
                        <span class="d-none d-md-block">{{ Auth::user()->name }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <div class="px-4 py-3 text-center border-bottom">
                            <span class="d-block text-sm text-muted">Signed in as</span>
                            <h6 class="mb-0">{{ Auth::user()->name }}</h6>
                        </div>
                        <a class="dropdown-item" href="{{ route('admin.admin.profile') }}">
                            <i class="far fa-user me-2"></i> Profile
                        </a>

                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar collapsed">
        <a href="/admin/main" class="menu-item {{ request()->is('admin/main') ? 'active' : '' }}">
            <div class="d-flex align-items-center">
                <i class="fas fa-tachometer-alt"></i>
                <span class="menu-text ms-2">Dashboard</span>
            </div>
        </a>


        <div class="menu-item" onclick="toggleMenu(this, 'members-menu')">
            <div class="d-flex align-items-center">
                <i class="fas fa-users"></i>
                <span class="menu-text ms-2">Manage Members</span>
            </div>
            <i class="fas fa-chevron-down menu-chevron"></i>
        </div>
        <div id="members-menu" class="submenu">
            <a href="/admin/members">
                <i class="fas fa-user"></i>
                <span>Members</span>
            </a>

        </div>

        <div class="menu-item" onclick="toggleMenu(this, 'worship-menu')">
            <div class="d-flex align-items-center">
                <i class="fas fa-music"></i>
                <span class="menu-text ms-2">Worship & Events</span>
            </div>
            <i class="fas fa-chevron-down menu-chevron"></i>
        </div>
        <div id="worship-menu" class="submenu">
            <a href="/admin/sermons">
                <i class="fas fa-bible"></i>
                <span>Sermons</span>
            </a>
            <a href="/admin/livestreams">
                <i class="fas fa-video"></i>
                <span>Live Stream</span>
            </a>
            <a href="/admin/events">
                <i class="bi bi-calendar-range"></i>
                <span>Events</span>
            </a>
            <a href="{{ route('admin.events.index', ['type' => 'wedding']) }}">
                <i class="bi bi-heart"></i>
                <span>Weddings</span>
            </a>
            <a href="{{ route('admin.events.index', ['type' => 'baptism']) }}">
                <i class="bi bi-water"></i>
                <span>Baptisms</span>
            </a>
        </div>

        <div class="menu-item" onclick="toggleMenu(this, 'donations-menu')">
            <div class="d-flex align-items-center">
                <i class="fas fa-hand-holding-usd"></i>
                <span class="menu-text ms-2">Donations</span>
            </div>
            <i class="fas fa-chevron-down menu-chevron"></i>
        </div>
        <div id="donations-menu" class="submenu">
            <a href="/admin/donations">
                <i class="fas fa-money-bill-wave"></i>
                <span>All Donations</span>
            </a>
            <a href="/admin/donations/monthly">
                <i class="fas fa-chart-bar"></i>
                <span>Monthly Reports</span>
            </a>
            <a href="/admin/donations/transparency">
                <i class="fas fa-file-invoice"></i>
                <span>Member Transparency</span>
            </a>
        </div>

        <div class="menu-item" onclick="toggleMenu(this, 'communication-menu')">
            <div class="d-flex align-items-center">
                <i class="fas fa-bullhorn"></i>
                <span class="menu-text ms-2">Communication</span>
            </div>
            <i class="fas fa-chevron-down menu-chevron"></i>
        </div>
        <div id="communication-menu" class="submenu">
            <a href="/admin/announcements">
                <i class="fas fa-bullhorn"></i>
                <span>Announcements</span>
            </a>
            <a href="{{ route('admin.admin.chat') }}">
                <i class="fas fa-comments"></i>
                <span>Messaging</span>
            </a>
        </div>

        <div class="menu-item" onclick="toggleMenu(this, 'security-menu')">
            <div class="d-flex align-items-center">
                <i class="fas fa-shield-alt"></i>
                <span class="menu-text ms-2">User & Security</span>
            </div>
            <i class="fas fa-chevron-down menu-chevron"></i>
        </div>
        <div id="security-menu" class="submenu">
            <a href="{{ route('admin.logs') }}">
                <i class="fas fa-history"></i>
                <span>System Logs</span>
            </a>
            <a href="{{ route('admin.admin.profile') }}">
                <i class="fas fa-cog"></i>
                <span>Profile</span>
            </a>
        </div>

        <!-- Sidebar Footer -->
        <div class="sidebar-footer">
            <div class="d-flex align-items-center justify-content-center">
                <a href="{{ route('admin.settings') }}" class="btn btn-sm btn-light me-2" title="Settings">
                    <i class="fas fa-cog"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content collapsed">
        <div class="container-fluid">
            <!-- Content Area -->
            @yield('content')
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get DOM elements
            const sidebar = document.querySelector('.sidebar');
            const toggleBtn = document.getElementById('toggle-sidebar');
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const mainContent = document.querySelector('.content');
            const sidebarOverlay = document.querySelector('.sidebar-overlay');
            const mobileTopNav = document.getElementById('mobileTopNav');
            const mobileMenuDropdownBtn = document.getElementById('mobileMenuDropdownBtn');
            const mobileMenuDropdown = document.getElementById('mobileMenuDropdown');
            const navbar = document.querySelector('.navbar');

            // Function to check if we're on mobile
            function isMobile() {
                return window.innerWidth <= 991.98;
            }

            // Function to show/hide navbar based on sidebar state
            function toggleNavbar() {
                if (isMobile() && (sidebar.classList.contains('hidden') || sidebar.classList.contains('collapsed'))) {
                    navbar.classList.add('navbar-hidden');
                    document.body.style.paddingTop = '70px';
                } else {
                    navbar.classList.remove('navbar-hidden');
                    document.body.style.paddingTop = '70px';
                }
            }

            // Function to show/hide mobile top navigation
            function toggleMobileNav() {
                if (isMobile() && (sidebar.classList.contains('hidden') || sidebar.classList.contains('collapsed'))) {
                    mobileTopNav.classList.add('show');
                } else {
                    mobileTopNav.classList.remove('show');
                }
            }

            // Function to toggle sidebar state
            function toggleSidebar() {
                if (isMobile()) {
                    // On mobile, toggle between hidden and show
                    if (sidebar.classList.contains('show')) {
                        sidebar.classList.remove('show');
                        sidebar.classList.add('hidden');
                        sidebarOverlay.classList.remove('active');
                        mainContent.classList.add('hidden');
                    } else {
                        sidebar.classList.remove('hidden', 'collapsed');
                        sidebar.classList.add('show');
                        sidebarOverlay.classList.add('active');
                        mainContent.classList.remove('hidden', 'collapsed');
                    }
                } else {
                    // On desktop, toggle between collapsed and expanded
                    if (sidebar.classList.contains('collapsed')) {
                        sidebar.classList.remove('collapsed');
                        sidebar.classList.add('expanded');
                        mainContent.classList.remove('collapsed');
                        mainContent.classList.add('expanded');
                    } else {
                        sidebar.classList.remove('expanded');
                        sidebar.classList.add('collapsed');
                        mainContent.classList.remove('expanded');
                        mainContent.classList.add('collapsed');
                    }
                }

                toggleMobileNav();
                toggleNavbar();
            }

            // Toggle sidebar when button is clicked (desktop)
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    toggleSidebar();
                });
            }

            // Mobile menu button functionality
            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    toggleSidebar();
                });
            }

            // Close sidebar when clicking on overlay
            sidebarOverlay.addEventListener('click', function() {
                if (sidebar.classList.contains('show')) {
                    sidebar.classList.remove('show');
                    sidebar.classList.add('hidden');
                    sidebarOverlay.classList.remove('active');
                    mainContent.classList.add('hidden');
                    toggleMobileNav();
                    toggleNavbar();
                }
            });

            // Handle mobile nav clicks
            const mobileNavLinks = document.querySelectorAll('.mobile-nav-item');
            mobileNavLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    // Skip if it's the dropdown menu button
                    if (this.id === 'mobileMenuDropdownBtn') {
                        return;
                    }

                    // Remove active class from all mobile nav items
                    mobileNavLinks.forEach(item => item.classList.remove('active'));
                    // Add active class to clicked item
                    this.classList.add('active');

                    // Navigate to the href if it exists
                    const href = this.getAttribute('href');
                    if (href && href !== '#') {
                        window.location.href = href;
                    }
                });
            });

            // Mobile dropdown menu functionality
            if (mobileMenuDropdownBtn && mobileMenuDropdown) {
                mobileMenuDropdownBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    mobileMenuDropdown.classList.toggle('show');
                });

                // Handle dropdown item clicks
                const dropdownItems = mobileMenuDropdown.querySelectorAll('.mobile-dropdown-item');
                dropdownItems.forEach(item => {
                    item.addEventListener('click', function(e) {
                        // Close dropdown
                        mobileMenuDropdown.classList.remove('show');

                        // Handle logout form submission
                        if (this.getAttribute('onclick')) {
                            return; // Let the onclick handler take care of it
                        }

                        // Navigate to href
                        const href = this.getAttribute('href');
                        if (href && href !== '#') {
                            window.location.href = href;
                        }
                    });
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(event) {
                    if (!mobileMenuDropdownBtn.contains(event.target)) {
                        mobileMenuDropdown.classList.remove('show');
                    }
                });
            }

            // Handle window resize
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    if (isMobile()) {
                        // On mobile, ensure proper state
                        if (sidebar.classList.contains('show')) {
                            sidebarOverlay.classList.add('active');
                            mobileTopNav.classList.remove('show');
                        } else {
                            sidebar.classList.add('hidden');
                            sidebar.classList.remove('collapsed', 'expanded');
                            mobileTopNav.classList.add('show');
                            sidebarOverlay.classList.remove('active');
                        }
                        mainContent.classList.remove('collapsed', 'expanded');
                    } else {
                        // On desktop, return to collapsed state
                        sidebar.classList.remove('show', 'hidden', 'expanded');
                        sidebar.classList.add('collapsed');
                        mainContent.classList.remove('hidden', 'expanded');
                        mainContent.classList.add('collapsed');
                        sidebarOverlay.classList.remove('active');
                        mobileTopNav.classList.remove('show');
                    }

                    toggleMobileNav();
                    toggleNavbar();
                }, 250);
            });

            // Initialize based on screen size
            if (isMobile()) {
                sidebar.classList.remove('collapsed', 'expanded');
                sidebar.classList.add('hidden');
                mainContent.classList.remove('collapsed', 'expanded');
                mainContent.classList.add('hidden');
            } else {
                sidebar.classList.remove('hidden', 'expanded', 'show');
                sidebar.classList.add('collapsed');
                mainContent.classList.remove('hidden', 'expanded');
                mainContent.classList.add('collapsed');
            }

            // Initialize mobile navigation and navbar visibility
            setTimeout(() => {
                toggleMobileNav();
                toggleNavbar();
            }, 100);

            // Menu toggle functionality
            window.toggleMenu = function(element, id) {
                $(element).toggleClass('open');
                $(element).find(".menu-chevron").toggleClass("open");
                $("#" + id).slideToggle();
            };

            // Set active menu item based on current route
            const currentPath = window.location.pathname;
            $('.submenu a').each(function() {
                const href = $(this).attr('href');
                if (href === currentPath || currentPath.startsWith(href)) {
                    $(this).addClass('active');
                    $(this).closest('.submenu').show();
                    $(this).closest('.submenu').prev('.menu-item').addClass('open');
                    $(this).closest('.submenu').prev('.menu-item').find('.menu-chevron').addClass('open');
                }
            });
        });
    </script>
</body>
</html>