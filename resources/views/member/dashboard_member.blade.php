<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="user-id" content="{{ auth()->check() ? auth()->id() : '' }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'User Dashboard')</title>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.11.3/echo.iife.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js"></script>
    

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.10.5/dist/cdn.min.js" defer></script>
    <!-- Font & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Calendar -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/main.min.js"></script>
    @include('layouts.websocket-config')

</head>
<style>
    :root {
        --primary-color: #1976d2;
        --secondary-color: #1565c0;
        --light-bg: #f8f9fa;
        --dark-text: #212529;
        --light-text: #6c757d;
        --sidebar-width: 280px;
        --sidebar-collapsed: 80px;
        --menu-item-padding-collapsed: 12px; /* vertical padding for icons */
        --menu-item-padding-expanded: 12px 15px;
        --header-height: 70px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Fix for Sidebar and Navbar Alignment */

/* Remove body padding-top and handle it properly */
body {
    font-family: 'Inter', sans-serif;
    background-color: rgb(247, 247, 247); 
    color: #212529;
    padding-top: 0; /* Remove this - we'll handle it in main-content */
}
    .alert-success {
        border-left: 5px solid #28a745;
        box-shadow: 0 2px 10px rgba(40, 167, 69, 0.2);
    }
    
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
        color: #1877f2;
        text-decoration: none;
        background-color: rgba(24, 119, 242, 0.05);
        transform: translateY(-2px);
    }
    
    .mobile-nav-item.active {
        color: #1877f2;
        background-color: rgba(24, 119, 242, 0.1);
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(24, 119, 242, 0.2);
    }
    
    .mobile-nav-item i {
        font-size: 20px;
        margin-bottom: 2px;
        transition: all 0.2s ease;
    }
    
    .mobile-nav-item.active i {
        transform: scale(1.1);
        color: #1877f2;
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
    
    /* Desktop notification badges */
    .navbar .nav-link .badge {
        position: absolute !important;
        top: -2px !important;
        right: -2px !important;
        background: #dc3545 !important;
        color: white !important;
        border-radius: 50% !important;
        font-size: 10px !important;
        font-weight: 600 !important;
        min-width: 18px !important;
        height: 18px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 0 !important;
        border: 2px solid white !important;
        box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3) !important;
        z-index: 10 !important;
        animation: desktopBadgePulse 2s infinite !important;
    }
    
    /* Force navbar badges to be visible */
    #notif-badge, #unread-count {
        display: inline-flex !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    
    @keyframes mobileBadgePulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }
    
    @keyframes desktopBadgePulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
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

    /* Responsive Sidebar Styles - Admin Dashboard Style */
    .member-sidebar {
    position: fixed;
    left: 0;
    top: var(--header-height); /* Start below navbar */
    height: calc(100vh - var(--header-height));
    width: var(--sidebar-collapsed);
    background: linear-gradient(180deg, #ffffff 0%, #f8f9fa 100%);
    box-shadow: 2px 0 20px rgba(0, 0, 0, 0.08);
    transition: var(--transition);
    z-index: 990; /* Lower than navbar */
    display: flex;
    flex-direction: column;
    border-right: 1px solid rgba(0, 0, 0, 0.05);
    overflow-y: auto;
    padding: 1rem 0;
}


    /* Expanded sidebar */
    .member-sidebar.expanded {
        width: var(--sidebar-width);
    }

    /* Hidden sidebar for mobile */
    .member-sidebar.hidden {
        width: 0;
        overflow: hidden;
        transform: translateX(-100%);
    }

    /* User Profile Section */
    .user-profile {
        display: flex;
        align-items: center;
        padding: 15px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        overflow: hidden;
        white-space: nowrap;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        margin: 8px;
        border-radius: 12px;
        transition: all 0.3s ease;
        min-height: 60px;
        justify-content: center;
    }

    .member-sidebar.expanded .user-profile {
        justify-content: flex-start;
    }

    .user-profile:hover {
        background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .profile-image {
        min-width: 40px;
        width: 40px;
        height: 40px;
        overflow: hidden;
        border-radius: 50%;
        flex-shrink: 0;
        border: 2px solid #ffffff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .profile-image:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    }

    .profile-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-info {
        margin-left: 15px;
        opacity: 0;
        visibility: hidden;
        transform: translateX(-10px);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .member-sidebar.expanded .profile-info {
        opacity: 1;
        visibility: visible;
        transform: translateX(0);
    }

    .profile-name {
        font-weight: 600;
        font-size: 1rem;
        color: #212529;
        margin-bottom: 2px;
    }

    .profile-role {
        font-size: 0.8rem;
        color: #6c757d;
        font-weight: 500;
    }

    /* Sidebar Menu */
    .sidebar-menu {
        flex: 1;
        overflow-y: auto;
        padding: 15px 0;
        scrollbar-width: thin;
        scrollbar-color: #e0e0e0 transparent;
        margin: 0 8px;
    }

    .sidebar-menu::-webkit-scrollbar {
        width: 5px;
    }

    .sidebar-menu::-webkit-scrollbar-track {
        background: transparent;
    }

    .sidebar-menu::-webkit-scrollbar-thumb {
        background-color: #e0e0e0;
        border-radius: 10px;
    }

    .menu-item {
        display: flex;
        align-items: center;
        justify-content: center; /* Center by default (collapsed) */
        padding: 12px;
        color: #495057;
        text-decoration: none;
        transition: var(--transition);
        position: relative;
        white-space: nowrap;
        overflow: hidden;
        border-left: 3px solid transparent;
        border-radius: 12px;
        margin: 2px 8px;
        font-weight: 500;
        min-height: 48px;
        width: 45px; /* Fixed width for collapsed state */
        margin: 0.3rem auto; /* Center in collapsed state */
    }

    .member-sidebar.expanded .menu-item {
        justify-content: flex-start; /* Left align when expanded */
        padding: 12px 15px;
        border-radius: 0 25px 25px 0;
        width: auto;
        margin: 2px 8px 2px 0;
    }

    .menu-item:hover {
        background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
        color: #1976d2;
        transform: translateX(4px);
        box-shadow: 0 4px 12px rgba(25, 118, 210, 0.15);
    }

    .menu-item.active {
        background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
        color: #ffffff;
        border-left: 3px solid #0d47a1;
        box-shadow: 0 4px 16px rgba(25, 118, 210, 0.3);
        transform: translateX(4px);
    }

    .menu-item.active i {
        color: #ffffff;
    }

    .menu-item i {
        min-width: 20px;
        width: 20px;
        text-align: center;
        font-size: 1.1rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0; /* No margin in collapsed state */
    }

    .member-sidebar.expanded .menu-item i {
        margin-right: 12px; /* Add margin when expanded */
    }

    /* Collapsed state hover effects */
    .member-sidebar:not(.expanded) .menu-item:hover i {
        transform: scale(1.2) rotate(5deg);
        color: #1976d2;
    }

    .menu-item:hover i {
        transform: translateX(2px);
    }

    .menu-item.active i {
        transform: translateX(2px) scale(1.05);
    }

    .menu-text {
        opacity: 0;
        visibility: hidden;
        transform: translateX(-10px);
        transition: all 0.3s ease;
        flex: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.2;
        display: flex;
        align-items: center;
    }

    .member-sidebar.expanded .menu-text {
        opacity: 1;
        visibility: visible;
        transform: translateX(0);
    }

    .badge {
        margin-left: auto;
        transition: all 0.3s ease;
        opacity: 0;
        visibility: hidden;
    }

    .member-sidebar.expanded .badge {
        opacity: 1;
        visibility: visible;
    }

    /* Show badges on collapsed sidebar in absolute position */
    .member-sidebar:not(.expanded) .badge {
        position: absolute;
        top: 5px;
        right: 5px;
        transform: scale(0.8);
        opacity: 1;
        visibility: visible;
    }

    /* Menu Sections */
    .menu-section {
        margin-bottom: 20px;
    }

    .menu-section-title {
        font-size: 0.75rem;
        font-weight: 600;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 8px 18px;
        margin-bottom: 5px;
        opacity: 0;
        visibility: hidden;
        transform: translateX(-10px);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .member-sidebar.expanded .menu-section-title {
        opacity: 1;
        visibility: visible;
        transform: translateX(0);
    }

    /* Sidebar Toggle Button */
    .sidebar-toggle-btn {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    color: #495057;
    border: 1px solid #eaeaea;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: absolute;
    top: 20px; /* Position relative to sidebar top */
    right: -12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    z-index: 1040;
    font-size: 0.7rem;
    padding: 0;
}

    .sidebar-toggle-btn:hover {
        background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
        color: #ffffff;
        transform: scale(1.1) rotate(5deg);
        box-shadow: 0 6px 20px rgba(25, 118, 210, 0.3);
    }

    .sidebar-toggle-btn:active {
        transform: scale(0.95);
    }

    .member-sidebar.expanded .sidebar-toggle-btn i {
        transform: rotate(180deg);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Tooltip for collapsed sidebar */
    .sidebar-tooltip {
        position: absolute;
        left: 60px;
        background: #333;
        color: white;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        opacity: 0;
        transition: opacity 0.2s ease;
        pointer-events: none;
        white-space: nowrap;
        z-index: 1040;
        transform: translateY(-50%);
    }

    /* Overlay for mobile */
    .sidebar-overlay {
        z-index: 989;
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

    /* Notification styles */
    .dropdown-item.clickable-notification:hover {
        background-color: rgba(13, 110, 253, 0.1);
        transform: translateX(2px);
        transition: all 0.2s ease;
    }

    .notification-badge {
        font-size: 0.6em !important;
        animation: pulse 2s infinite !important;
        display: inline-flex !important;
        visibility: visible !important;
    }

    @keyframes pulse {
        0% { transform: translate(-50%, -50%) scale(1); }
        50% { transform: translate(-50%, -50%) scale(1.1); }
        100% { transform: translate(-50%, -50%) scale(1); }
    }
    
    /* Ensure desktop badges are always visible when they have content */
    .nav-link .badge:not(:empty) {
        display: inline-flex !important;
        visibility: visible !important;
    }

    .notification-dropdown-scroll {
        max-height: 300px;
        overflow-y: auto;
    }

    .notification-dropdown-scroll::-webkit-scrollbar {
        width: 4px;
    }

    .notification-dropdown-scroll::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .notification-dropdown-scroll::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }

    .notification-dropdown-scroll::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    .dropdown-menu {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        border: none;
    }

    .min-width-0 {
        min-width: 0;
    }

    .text-truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Main Content Adjustment */
    .main-content {
    margin-top: var(--header-height); /* Add top margin for navbar */
    margin-left: var(--sidebar-collapsed); /* Default collapsed margin */
    padding: 20px;
    transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    min-height: calc(100vh - var(--header-height));
}

    .main-content.sidebar-expanded {
        margin-left: var(--sidebar-width);
    }

    .main-content.sidebar-hidden {
        margin-left: 0;
    }

    /* Mobile Menu Button Styles */
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
        color: #1976d2 !important;
        transform: scale(1.05);
    }

    .mobile-menu-btn:focus {
        box-shadow: 0 0 0 0.2rem rgba(25, 118, 210, 0.25) !important;
    }

    .mobile-menu-btn i {
        font-size: 1.1rem !important;
        color: inherit !important;
    }

    /* Navbar */
    .navbar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    min-height: var(--header-height);
    padding: 0.5rem 1rem;
    z-index: 1025;
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease;
    background-color: #ffffff;
    border-bottom: 1px solid #e3e3e3;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

    .navbar.navbar-hidden {
        transform: translateY(-100%);
        opacity: 0;
        pointer-events: none;
    }

    .navbar .container-fluid {
        display: flex;
    align-items: center;
    justify-content: space-between;
    min-height: calc(var(--header-height) - 1rem); /* Account for navbar padding */
    padding-left: var(--sidebar-collapsed); /* Align with collapsed sidebar */
    transition: padding-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.member-sidebar.expanded ~ * .navbar .container-fluid {
    padding-left: var(--sidebar-width);
}


    .navbar-nav {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .nav-item {
        display: flex;
        align-items: center;
    }

    .nav-link {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem 0.75rem;
        min-height: 40px;
        min-width: 40px;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .nav-link:hover {
        background-color: #f8f9fa;
        transform: translateY(-1px);
    }

    .nav-link i {
        font-size: 1.1rem;
        line-height: 1;
    }

    .navbar-brand {
        display: flex;
        align-items: center;
        min-height: 40px;
    }

    /* Hide navbar icons when sidebar is collapsed - but keep badges visible */
    .navbar .nav-item.hide-on-minimize {
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }

    .member-sidebar:not(.expanded) ~ .main-content .navbar .nav-item.hide-on-minimize {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
    }
    
    /* Exception: Always show badges even when nav items are hidden */
    .member-sidebar:not(.expanded) ~ .main-content .navbar .nav-item.hide-on-minimize .badge {
        opacity: 1 !important;
        visibility: visible !important;
        display: inline-flex !important;
    }

    .member-sidebar.expanded ~ .main-content .navbar .nav-item.hide-on-minimize {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }

    /* Responsive */
    @media (max-width: 991.98px) {
        body {
            padding-top: 70px;
        }

        .member-sidebar {
            transform: translateX(-100%);
            width: 0;
            top: 70px;
            height: calc(100vh - 70px);
        }

        .member-sidebar.expanded {
            transform: translateX(0);
            width: var(--sidebar-width);
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.3);
        }

        .main-content {
            margin-left: 0;
            padding-bottom: 80px;
        }

        .main-content.sidebar-expanded {
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

        .sidebar-toggle-btn {
            display: none;
        }
        
        /* Ensure mobile badges work properly */
        .mobile-nav-badge {
            display: flex !important;
        }
    }
    
    /* Desktop-specific badge rules */
    @media (min-width: 992px) {
        .navbar .nav-link .badge {
            display: inline-flex !important;
        }
        
        /* Hide mobile navigation on desktop */
        .mobile-top-nav {
            display: none !important;
        }
        
        /* Force navbar badges visible on desktop */
        #notif-badge, #unread-count {
            display: inline-flex !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        /* Ensure desktop badges are always visible when they have content */
        .nav-link .position-absolute.badge:not(:empty) {
            display: inline-flex !important;
            visibility: visible !important;
        }
    }

    @media (max-width: 767.98px) {
        .member-sidebar.expanded {
            width: 100%;
            max-width: 300px;
            box-shadow: 0 0 40px rgba(0, 0, 0, 0.4);
        }

        .main-content {
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
            width: 24px;
            font-size: 1.2rem;
        }

        .menu-text {
            font-size: 1rem;
            font-weight: 500;
            margin-left: 14px;
        }
    }

    @media (max-width: 575.98px) {
        .member-sidebar.expanded {
            width: 100%;
            max-width: 300px;
        }

        .main-content {
            padding: 10px 10px 90px 10px;
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
            width: 28px;
            font-size: 1.3rem;
        }

        .profile-image {
            width: 50px;
            height: 50px;
            min-width: 50px;
        }

        .user-profile {
            padding: 25px 20px;
            margin: 10px;
            border-radius: 15px;
        }

        
        .profile-name {
            font-size: 1.1rem;
        }

        .profile-role {
            font-size: 0.9rem;
        }

        .mobile-menu-btn {
            padding: 10px 15px !important;
        }

        .mobile-menu-btn i {
            font-size: 1.2rem !important;
        }
    }

    /* Additional Animations and Effects */
    @keyframes slideInFromLeft {
        from {
            transform: translateX(-100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes fadeInScale {
        from {
            opacity: 0;
            transform: scale(0.9);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .member-sidebar.expanded {
        animation: slideInFromLeft 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .menu-item {
        animation: fadeInScale 0.2s ease-out;
    }

    .menu-item:nth-child(1) { animation-delay: 0.1s; }
    .menu-item:nth-child(2) { animation-delay: 0.15s; }
    .menu-item:nth-child(3) { animation-delay: 0.2s; }
    .menu-item:nth-child(4) { animation-delay: 0.25s; }
    .menu-item:nth-child(5) { animation-delay: 0.3s; }

    /* Smooth scrollbar styling */
    .sidebar-menu::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar-menu::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.05);
        border-radius: 10px;
    }

    .sidebar-menu::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .sidebar-menu::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #1565c0 0%, #0d47a1 100%);
        transform: scale(1.1);
    }

    /* Focus states for accessibility */
    .menu-item:focus {
        outline: 2px solid #1976d2;
        outline-offset: 2px;
        border-radius: 12px;
    }

    .sidebar-toggle-btn:focus {
        outline: 2px solid #1976d2;
        outline-offset: 2px;
    }

    .mobile-menu-btn:focus {
        outline: 2px solid #1976d2 !important;
        outline-offset: 2px !important;
    }
</style>

<body>

    <!-- Global Configuration -->
    <script type="module">
        window.pusherKey = "{{ env('PUSHER_APP_KEY') }}";
        window.pusherCluster = "{{ env('PUSHER_APP_CLUSTER') }}";
        window.authId = {{ Auth::id() }};
    </script>
    <!-- Your main JS file -->
    <script type="module" src="{{ asset('js/app.js') }}" defer></script>

    @vite('resources/js/app.jsx')

    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <!-- Mobile Menu Button -->
            <button class="btn btn-link d-md-none mobile-menu-btn" type="button" id="mobileMenuBtn" aria-label="Toggle navigation menu">
                <i class="fas fa-bars"></i>
            </button>
            
            <a class="navbar-brand" href="#"></a>

            <!-- Right Side Icons -->
            <ul class="navbar-nav d-flex align-items-center">

                <!-- Messages Icon -->
                <li class="nav-item me-3 position-relative">
                    <a class="nav-link position-relative" href="{{ route('chat.index') }}">
                        <i class="fas fa-envelope fa-lg"></i>
                        <span id="unread-count" class="position-absolute top-0 start-100 translate-middle badge bg-danger" style="display: none;"></span>
                    </a>
                </li>

                <li class="nav-item dropdown me-3 position-relative">
                    <a class="nav-link dropdown-toggle position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell fa-lg"></i>
                        <span id="notif-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge" style="display: {{ Auth::user()->unreadNotifications->isNotEmpty() ? 'inline-flex' : 'none' }};">
                            {{ Auth::user()->unreadNotifications->count() }}
                        </span>
                    </a>
                    
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown" style="width: 350px; max-height: 400px; overflow-y: auto;">
                        <div class="dropdown-header d-flex justify-content-between align-items-center px-3 py-2">
                            <h6 class="mb-0 fw-bold">
                                <i class="fas fa-bell me-2"></i>Notifications
                            </h6>
                            @if(Auth::user()->unreadNotifications->isNotEmpty())
                                <button id="mark-all-read-btn" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-check-double me-1"></i>Mark All Read
                                </button>
                            @endif
                        </div>
                        
                        <div class="dropdown-divider my-0"></div>
                        
                        <div id="notification-dropdown-content" class="notification-dropdown-scroll">
                            {{-- Content will be loaded dynamically --}}
                            <div class="text-center py-4">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <div class="small text-muted mt-2">Loading notifications...</div>
                            </div>
                        </div>
                        
                        <div class="dropdown-divider my-0"></div>
                        <div class="dropdown-item text-center py-2">
                            <a href="{{ route('notifications') }}" class="btn btn-sm btn-primary w-100">
                                <i class="fas fa-list me-1"></i>View All Notifications
                            </a>
                        </div>
                    </div>
                </li>

                <!-- User Profile Dropdown -->
                <li class="nav-item dropdown">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        <img src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : 'https://via.placeholder.com/40' }}"
                        class="rounded-circle me-2"
                        width="40"
                        height="40"
                        alt="User Avatar">

                        <span>{{ Auth::user()->first_name ?? 'Guest' }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        @auth
                            <a class="dropdown-item" href="profile">Profile</a>
                          
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        @else
                            <a class="dropdown-item" href="{{ route('login') }}">Login</a>
                        @endauth
                    </div>
                </li>

            </ul>
        </div>
    </nav>

  <!-- Overlay for mobile -->
<div class="sidebar-overlay"></div>

<!-- Member Sidebar - Start in collapsed mode by default (admin style) -->
<div class="member-sidebar">
    <!-- User Profile Section -->



    <!-- Navigation Items -->
    <div class="sidebar-menu">
        <!-- Main Section -->
        <div class="menu-section">
            <div class="menu-section-title">Main</div>
            <a href="/daily-verse" class="menu-item {{ request()->is('daily-verse') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i>
                <span class="menu-text">Dashboard</span>
            </a>
       <a href="/chatbot" class="menu-item {{ request()->is('chatbot') ? 'active' : '' }}">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
                <span class="menu-text">Chatbot</span>
            </a>


            <a href="/notifications" class="menu-item {{ request()->is('notifications') ? 'active' : '' }}">
                <i class="fas fa-bell"></i>
                <span class="menu-text">Notifications</span>
                @if(Auth::user()->unreadNotifications->isNotEmpty())
                    <span id="sidebar-notif-badge" class="badge bg-danger rounded-pill">{{ Auth::user()->unreadNotifications->count() }}</span>
                @endif
            </a>

            <a href="/chat" class="menu-item {{ request()->is('chat') || request()->is('chat/*') ? 'active' : '' }}">
                <i class="fas fa-envelope"></i>
                <span class="menu-text">Messages</span>
                <span id="sidebar-msg-badge" class="badge bg-primary rounded-pill" style="display: none;"></span>
            </a>
        </div>

        <!-- Content Section -->
        <div class="menu-section">
            <div class="menu-section-title">Content</div>
            <a href="/member/events" class="menu-item {{ request()->is('member/events*') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i>
                <span class="menu-text">Events</span>
            </a>

            <a href="/sermons" class="menu-item {{ request()->is('sermons*') && !request()->is('sermons/favorites') ? 'active' : '' }}">
                <i class="fas fa-bible"></i>
                <span class="menu-text">Sermons</span>
            </a>

             <a href="/livestream" class="menu-item {{ request()->is('livestream*') ? 'active' : '' }}">
                <i class="fas fa-video"></i>
                <span class="menu-text">Live Stream</span>
            </a>

            <a href="/announcements" class="menu-item {{ request()->is('member/announcements*') ? 'active' : '' }}">
                <i class="fas fa-bullhorn"></i>
                <span class="menu-text">Announcements</span>
            </a>

            <a href="{{ route('member.donations.index') }}" class="menu-item {{ request()->is('donations*') ? 'active' : '' }}">
                <i class="fas fa-hand-holding-heart"></i>
                <span class="menu-text">Donations</span>
            </a>
        </div>

        <!-- Account Section -->
        <div class="menu-section">
            <div class="menu-section-title">Account</div>
            <a href="/profile" class="menu-item {{ request()->is('profile*') ? 'active' : '' }}">
                <i class="fas fa-user"></i>
                <span class="menu-text">Profile</span>
            </a>
        </div>
    </div>

    <!-- Toggle button -->
    <button class="btn btn-sm sidebar-toggle-btn">
        <i class="fas fa-chevron-right"></i>
    </button>
</div>

<!-- Mobile Top Navigation Bar (Facebook Style) -->
<div class="mobile-top-nav" id="mobileTopNav">
    <div class="mobile-nav-container">
        <!-- Dashboard -->
        <a href="/daily-verse" class="mobile-nav-item {{ request()->is('chatbot') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="/chatbot" class="mobile-nav-item {{ request()->is('chatbot') ? 'active' : '' }}">
            <i class="fa-solid fa-wand-magic-sparkles"></i>
            <span>Chatbot</span>
        </a>
        
        <!-- Notifications -->
        <a href="/notifications" class="mobile-nav-item {{ request()->is('notifications') ? 'active' : '' }}">
            <i class="fas fa-bell"></i>
            <span>Alerts</span>
            @if(Auth::user()->unreadNotifications->isNotEmpty())
                <span id="mobile-notif-badge" class="mobile-nav-badge">{{ Auth::user()->unreadNotifications->count() }}</span>
            @endif
        </a>
        
        <!-- Messages -->
        <a href="/chat" class="mobile-nav-item {{ request()->is('chat*') ? 'active' : '' }}">
            <i class="fas fa-envelope"></i>
            <span>Messages</span>
            <span id="mobile-msg-badge" class="mobile-nav-badge" style="display: none;"></span>
        </a>
        
        <!-- Events -->
        <a href="/member/events" class="mobile-nav-item {{ request()->is('member/events*') ? 'active' : '' }}">
            <i class="fas fa-calendar-alt"></i>
            <span>Events</span>
        </a>
        
        <!-- Sermons -->
        <a href="/sermons" class="mobile-nav-item {{ request()->is('sermons*') ? 'active' : '' }}">
            <i class="fas fa-bible"></i>
            <span>Sermons</span>
        </a>
        
        <!-- Live Stream -->
        <a href="/livestream" class="mobile-nav-item {{ request()->is('livestream*') ? 'active' : '' }}">
            <i class="fas fa-video"></i>
            <span>Live</span>
        </a>
        
        <!-- Menu Dropdown -->
        <div class="mobile-nav-item" id="mobileMenuDropdownBtn">
            <i class="fas fa-bars"></i>
            <span>Menu</span>
            
            <!-- Dropdown Menu -->
            <div class="mobile-menu-dropdown" id="mobileMenuDropdown">
                <a href="/profile" class="mobile-dropdown-item">
                    <i class="fas fa-user"></i>
                    Profile
                </a>
                
          
                
                <a href="/announcements" class="mobile-dropdown-item">
                    <i class="fas fa-bullhorn"></i>
                    Announcements
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

<!-- Main content wrapping div -->
<div class="main-content">
    <!-- Your main content here -->
    @yield('content')
</div>

    <!-- JavaScript for Real-time Messaging & Notifications -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
    const notificationDropdown = document.getElementById('notificationDropdown');
    const dropdownContent = document.getElementById('notification-dropdown-content');
    const markAllReadBtn = document.getElementById('mark-all-read-btn');
    
    // Check if elements exist before adding event listeners
    if (notificationDropdown) {
        // Initialize Bootstrap dropdown manually if needed
        if (typeof bootstrap !== 'undefined') {
            const dropdownInstance = new bootstrap.Dropdown(notificationDropdown);
            console.log('Bootstrap dropdown initialized');
        }
        
        // Load notifications when dropdown is opened
        notificationDropdown.addEventListener('show.bs.dropdown', function() {
            console.log('Notification dropdown opened');
            loadNotificationDropdownContent();
        });
        
        // Also load on click as fallback
        notificationDropdown.addEventListener('click', function(e) {
            console.log('Notification dropdown clicked');
            // Small delay to ensure Bootstrap dropdown is initialized
            setTimeout(() => {
                loadNotificationDropdownContent();
            }, 100);
        });
        
        // Load initial content
        setTimeout(() => {
            loadNotificationDropdownContent();
        }, 500);
    }
    
    // Mark all as read functionality
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            markAllNotificationsAsRead();
        });
    }
    
    async function loadNotificationDropdownContent() {
        if (!dropdownContent) {
            console.error('Dropdown content element not found');
            return;
        }
        
        try {
            console.log('Loading notification content...');
            const response = await fetch('{{ route("notifications.recent") }}?limit=8');
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            const notifications = data.notifications || [];

            let dropdownHtml = '';
            
            if (notifications.length === 0) {
                dropdownHtml = `
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-bell-slash fa-2x mb-2"></i><br>
                        <span class="fw-bold">All caught up!</span><br>
                        <small>No new notifications</small>
                    </div>
                `;
            } else {
                notifications.forEach(notification => {
                    const icon = getNotificationIcon(notification.data.type);
                    const time = timeAgo(notification.created_at);
                    const title = notification.data.title || notification.data.message || 'New notification';
                    const description = notification.data.description || notification.data.message || '';
                    
                    dropdownHtml += `
                        <div class="dropdown-item clickable-notification py-3 border-bottom" 
                             data-notification-id="${notification.id}"
                             data-notification-type="${notification.data.type || 'general'}"
                             data-event-id="${notification.data.event_id || ''}"
                             data-sermon-id="${notification.data.sermon_id || ''}"
                             data-announcement-id="${notification.data.announcement_id || ''}"
                             data-sender-id="${notification.data.sender_id || ''}"
                             style="cursor: pointer;">
                            <div class="d-flex align-items-start">
                                <div class="me-3 mt-1">
                                    <div class="notification-icon-bg bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 32px; height: 32px;">
                                        <i class="fas fa-${icon} small"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 min-width-0">
                                    <div class="fw-semibold text-dark mb-1 text-truncate" style="font-size: 0.85rem;">
                                        ${title}
                                    </div>
                                    ${description ? `<div class="text-muted small mb-1 text-truncate">${description}</div>` : ''}
                                    <div class="text-muted" style="font-size: 0.7rem;">
                                        <i class="fas fa-clock me-1"></i>${time}
                                    </div>
                                </div>
                                <div class="ms-2">
                                    <div class="bg-primary rounded-circle" style="width: 8px; height: 8px;"></div>
                                </div>
                            </div>
                        </div>
                    `;
                });
            }

            dropdownContent.innerHTML = dropdownHtml;
            
            // Bind click events to new notifications
            bindNotificationClicks();
            
            console.log('Notification content loaded successfully');
            
        } catch (error) {
            console.error('Error loading notifications:', error);
            dropdownContent.innerHTML = `
                <div class="text-center text-danger py-4">
                    <i class="fas fa-exclamation-triangle"></i><br>
                    Error loading notifications<br>
                    <small>${error.message}</small>
                </div>
            `;
        }
    }
    
    function bindNotificationClicks() {
        document.querySelectorAll('.clickable-notification').forEach(notification => {
            notification.addEventListener('click', function() {
                const notificationId = this.dataset.notificationId;
                
                // Close dropdown
                bootstrap.Dropdown.getInstance(notificationDropdown).hide();
                
                // Navigate using the redirect route
                window.location.href = `{{ url('/notifications') }}/${notificationId}/redirect`;
            });
        });
    }
    
    async function markAllNotificationsAsRead() {
        try {
            markAllReadBtn.disabled = true;
            markAllReadBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Marking...';
            
            const response = await fetch('{{ route("notifications.markAllAsRead") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                // Update badge
                const badge = document.getElementById('notif-badge');
                if (badge) {
                    badge.style.display = 'none';
                }
                
                // Update other badges
                const sidebarBadge = document.getElementById('sidebar-notif-badge');
                if (sidebarBadge) {
                    sidebarBadge.style.display = 'none';
                }
                
                const mobileBadge = document.getElementById('mobile-notif-badge');
                if (mobileBadge) {
                    mobileBadge.style.display = 'none';
                }
                
                // Reload dropdown content
                loadNotificationDropdownContent();
                
                // Hide the mark all read button
                markAllReadBtn.style.display = 'none';
                
                // Show success toast
                showToast('All notifications marked as read', 'success');
            } else {
                showToast('Error marking notifications as read', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Error marking notifications as read', 'error');
        } finally {
            markAllReadBtn.disabled = false;
            markAllReadBtn.innerHTML = '<i class="fas fa-check-double me-1"></i>Mark All Read';
        }
    }
    
    function getNotificationIcon(type) {
        const icons = {
            'event': 'calendar-alt',
            'sermon': 'microphone-alt',
            'announcement': 'bullhorn',
            'chat': 'comments',
            'livestream': 'video',
            'donation': 'heart',
            'ministry': 'users'
        };
        return icons[type] || 'envelope';
    }
    
    function timeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffInSeconds = Math.floor((now - date) / 1000);
        
        if (diffInSeconds < 60) return 'just now';
        if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`;
        if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`;
        return `${Math.floor(diffInSeconds / 86400)}d ago`;
    }
    
    function showToast(message, type = 'info') {
        const toastContainer = document.querySelector('.toast-container') || createToastContainer();
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'primary'} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        toastContainer.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        toast.addEventListener('hidden.bs.toast', () => toast.remove());
    }
    
    function createToastContainer() {
        const container = document.createElement('div');
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
        return container;
    }
    
    // Polling disabled - using real-time WebSocket notifications
});
         setTimeout(() => {
        const alert = document.getElementById('success-alert');
        if (alert) {
            alert.classList.remove('show');
            alert.classList.add('animate__fadeOutUp');

            setTimeout(() => {
                alert.remove();
            }, 800); // Wait for animation to finish
        }
    }, 5000);
        document.addEventListener('DOMContentLoaded', function () {
            let authId = document.querySelector("meta[name='user-id']").content || null;

            function fetchUnreadCount() {
                if (!authId) return; // Prevent fetching if not logged in

                fetch('/chat/unread-count')
                    .then(res => res.json())
                    .then(data => {
                        let countBadge = document.getElementById('unread-count');
                        let mobileCountBadge = document.getElementById('mobile-msg-badge');
                        let sidebarMsgBadge = document.getElementById('sidebar-msg-badge');
                        

                        
                        if (data.unread > 0) {
                            // Desktop navbar badge
                            if (countBadge) {
                                countBadge.innerText = data.unread;
                                countBadge.style.display = 'inline-flex';
                                countBadge.style.visibility = 'visible';
                            }
                            
                            // Mobile navigation badge
                            if (mobileCountBadge) {
                                mobileCountBadge.innerText = data.unread;
                                mobileCountBadge.style.display = 'flex';
                            }
                            
                            // Sidebar badge
                            if (sidebarMsgBadge) {
                                sidebarMsgBadge.innerText = data.unread;
                                sidebarMsgBadge.style.display = 'inline';
                            }
                        } else {
                            if (countBadge) countBadge.style.display = 'none';
                            if (mobileCountBadge) mobileCountBadge.style.display = 'none';
                            if (sidebarMsgBadge) sidebarMsgBadge.style.display = 'none';
                        }
                    })
                    .catch(error => console.error("Error fetching unread messages:", error));
            }

            function updateNotificationCount() {
                if (!authId) return;

                fetch("{{ route('notifications.count') }}")
                    .then(response => response.json())
                    .then(data => {
                        let notifBadge = document.getElementById('notif-badge');
                        let mobileNotifBadge = document.getElementById('mobile-notif-badge');
                        let sidebarNotifBadge = document.getElementById('sidebar-notif-badge');
                        
                        if (data.count > 0) {
                            // Desktop navbar badge
                            if (notifBadge) {
                                notifBadge.innerText = data.count;
                                notifBadge.style.display = 'inline-flex';
                                notifBadge.style.visibility = 'visible';
                            }
                            
                            // Mobile navigation badge
                            if (mobileNotifBadge) {
                                mobileNotifBadge.innerText = data.count;
                                mobileNotifBadge.style.display = 'flex';
                            }
                            
                            // Sidebar badge
                            if (sidebarNotifBadge) {
                                sidebarNotifBadge.innerText = data.count;
                                sidebarNotifBadge.style.display = 'inline';
                            }
                        } else {
                            if (notifBadge) notifBadge.style.display = 'none';
                            if (mobileNotifBadge) mobileNotifBadge.style.display = 'none';
                            if (sidebarNotifBadge) sidebarNotifBadge.style.display = 'none';
                        }
                    })
                    .catch(error => console.error("Error fetching notifications:", error));
            }


            
            // Initial load only - real-time updates via WebSocket
            fetchUnreadCount();
            updateNotificationCount();

            // Real-time updates using Pusher & Laravel Echo
            if (authId) {
                window.Echo = new Echo({
                    broadcaster: 'pusher',
                    key: '{{ config("broadcasting.connections.pusher.key") }}',
                    cluster: '{{ config("broadcasting.connections.pusher.options.cluster") }}',
                    forceTLS: true
                });

                window.Echo.private(`chat.${authId}`)
                    .listen('MessageSent', (e) => {
                        fetchUnreadCount(); // Update unread messages in real-time
                    });

                window.Echo.private(`notifications.${authId}`)
                    .listen('NotificationSent', (e) => {
                        updateNotificationCount(); // Update notifications in real-time
                    });
            }
        });

        // Sidebar and Mobile Navigation Management - Admin Dashboard Style
        document.addEventListener('DOMContentLoaded', function() {
            // Get DOM elements
            const sidebar = document.querySelector('.member-sidebar');
            const sidebarToggleBtn = document.querySelector('.sidebar-toggle-btn');
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const mainContent = document.querySelector('.main-content');
            const sidebarOverlay = document.querySelector('.sidebar-overlay');
            const mobileTopNav = document.getElementById('mobileTopNav');
            const mobileMenuDropdownBtn = document.getElementById('mobileMenuDropdownBtn');
            const mobileMenuDropdown = document.getElementById('mobileMenuDropdown');
            const navbar = document.querySelector('.navbar');
            const navbarContainer = document.querySelector('.navbar .container-fluid');

            // Function to check if we're on mobile
            function isMobile() {
                return window.innerWidth <= 991.98;
            }

            // Function to update navbar container padding based on sidebar state
            function updateNavbarAlignment() {
                if (!navbarContainer) return;

                if (isMobile()) {
                    // On mobile, always use default padding
                    navbarContainer.style.paddingLeft = '1rem';
                } else {
                    // On desktop, adjust padding based on sidebar state
                    if (sidebar.classList.contains('expanded')) {
                        navbarContainer.style.paddingLeft = 'var(--sidebar-width)';
                    } else {
                        navbarContainer.style.paddingLeft = 'var(--sidebar-collapsed)';
                    }
                }
            }

            // Function to show/hide navbar based on sidebar state
            function toggleNavbar() {
                if (isMobile() && !sidebar.classList.contains('expanded')) {
                    navbar.classList.add('navbar-hidden');
                    document.body.style.paddingTop = '70px';
                } else {
                    navbar.classList.remove('navbar-hidden');
                    document.body.style.paddingTop = '70px';
                }
            }

            // Function to show/hide mobile top navigation
            function toggleMobileNav() {
                if (isMobile() && !sidebar.classList.contains('expanded')) {
                    mobileTopNav.classList.add('show');
                } else {
                    mobileTopNav.classList.remove('show');
                }
            }

            // Function to toggle sidebar state (admin dashboard style)
            function toggleSidebar() {
                if (isMobile()) {
                    // On mobile, toggle between hidden and expanded
                    if (sidebar.classList.contains('expanded')) {
                        sidebar.classList.remove('expanded');
                        sidebar.classList.add('hidden');
                        sidebarOverlay.classList.remove('active');
                        mainContent.classList.remove('sidebar-expanded');
                        mainContent.classList.add('sidebar-hidden');
                    } else {
                        sidebar.classList.remove('hidden');
                        sidebar.classList.add('expanded');
                        sidebarOverlay.classList.add('active');
                        mainContent.classList.remove('sidebar-hidden');
                        mainContent.classList.add('sidebar-expanded');
                    }
                } else {
                    // On desktop, toggle between collapsed and expanded (like admin dashboard)
                    if (sidebar.classList.contains('expanded')) {
                        sidebar.classList.remove('expanded');
                        mainContent.classList.remove('sidebar-expanded');
                    } else {
                        sidebar.classList.add('expanded');
                        mainContent.classList.add('sidebar-expanded');
                    }
                }

                // Update navbar alignment
                updateNavbarAlignment();
                toggleMobileNav();
                toggleNavbar();

                // Update toggle icon
                const icon = sidebarToggleBtn.querySelector('i');
                if (sidebar.classList.contains('expanded')) {
                    icon.classList.remove('fa-chevron-right');
                    icon.classList.add('fa-chevron-left');
                } else {
                    icon.classList.remove('fa-chevron-left');
                    icon.classList.add('fa-chevron-right');
                }
            }

            // Initialize sidebar state based on screen size
            function initializeSidebar() {
                if (isMobile()) {
                    // On mobile, start with sidebar hidden
                    sidebar.classList.add('hidden');
                    sidebar.classList.remove('expanded');
                    mainContent.classList.add('sidebar-hidden');
                    mainContent.classList.remove('sidebar-expanded');
                    mobileTopNav.classList.add('show');
                    navbar.classList.add('navbar-hidden');
                } else {
                    // On desktop, start collapsed (admin dashboard style)
                    sidebar.classList.remove('hidden', 'expanded');
                    mainContent.classList.remove('sidebar-expanded', 'sidebar-hidden');
                    mobileTopNav.classList.remove('show');
                    navbar.classList.remove('navbar-hidden');
                }
                sidebarOverlay.classList.remove('active');
                // Update navbar alignment
                updateNavbarAlignment();
            }

            // Event Listeners
            
            // Sidebar toggle button click
            if (sidebarToggleBtn) {
                sidebarToggleBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    toggleSidebar();
                });
            }

            // Mobile menu button click
            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    toggleSidebar();
                });
            }

            // Overlay click to close sidebar on mobile
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    if (isMobile() && sidebar.classList.contains('expanded')) {
                        toggleSidebar();
                    }
                });
            }

            // Mobile dropdown menu toggle
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
                document.addEventListener('click', function(e) {
                    if (!mobileMenuDropdownBtn.contains(e.target)) {
                        mobileMenuDropdown.classList.remove('show');
                    }
                });
            }

            // Handle window resize
            window.addEventListener('resize', function() {
                // Debounce resize events
                clearTimeout(window.resizeTimeout);
                window.resizeTimeout = setTimeout(function() {
                    initializeSidebar();
                    updateNavbarAlignment();
                }, 100);
            });

            // Handle escape key to close sidebar on mobile
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && isMobile() && sidebar.classList.contains('expanded')) {
                    toggleSidebar();
                }
            });

            // Add tooltips for collapsed sidebar items (desktop only)
            function addSidebarTooltips() {
                if (!isMobile() && !sidebar.classList.contains('expanded')) {
                    const menuItems = sidebar.querySelectorAll('.menu-item');
                    menuItems.forEach(item => {
                        const text = item.querySelector('.menu-text')?.textContent;
                        if (text) {
                            // Create tooltip element
                            let tooltip = item.querySelector('.sidebar-tooltip');
                            if (!tooltip) {
                                tooltip = document.createElement('div');
                                tooltip.className = 'sidebar-tooltip';
                                tooltip.textContent = text;
                                item.appendChild(tooltip);
                            }

                            // Show tooltip on hover
                            item.addEventListener('mouseenter', function() {
                                if (!sidebar.classList.contains('expanded')) {
                                    const rect = item.getBoundingClientRect();
                                    tooltip.style.top = rect.top + (rect.height / 2) + 'px';
                                    tooltip.style.opacity = '1';
                                }
                            });

                            item.addEventListener('mouseleave', function() {
                                tooltip.style.opacity = '0';
                            });
                        }
                    });
                }
            }

            // Remove tooltips when sidebar is expanded
            function removeTooltips() {
                const tooltips = sidebar.querySelectorAll('.sidebar-tooltip');
                tooltips.forEach(tooltip => tooltip.remove());
            }

            // Update tooltips based on sidebar state
            function updateTooltips() {
                removeTooltips();
                setTimeout(addSidebarTooltips, 300); // Wait for transition
            }

            // Add mutation observer to watch for sidebar class changes
            const sidebarObserver = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === 'class') {
                        updateTooltips();
                    }
                });
            });

            sidebarObserver.observe(sidebar, { attributes: true });

            // Call updateNavbarAlignment on initial load
            updateNavbarAlignment();

            // Optional: Add smooth transition for navbar container padding
            if (navbarContainer) {
                navbarContainer.style.transition = 'padding-left 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
            }

            // Initialize everything
            initializeSidebar();
            updateTooltips();

            // Auto-collapse sidebar on mobile when navigating
            const sidebarLinks = sidebar.querySelectorAll('.menu-item');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (isMobile() && sidebar.classList.contains('expanded')) {
                        setTimeout(() => {
                            toggleSidebar();
                        }, 150); // Small delay for better UX
                    }
                });
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

            // Smooth scrolling for better UX
            function enableSmoothScrolling() {
                const style = document.createElement('style');
                style.textContent = `
                    html {
                        scroll-behavior: smooth;
                    }
                `;
                document.head.appendChild(style);
            }

            enableSmoothScrolling();

            // Performance optimization: Use passive event listeners where possible
            const passiveEvents = ['scroll', 'touchstart', 'touchmove'];
            passiveEvents.forEach(eventType => {
                document.addEventListener(eventType, function() {
                    // Handle passive events if needed
                }, { passive: true });
            });

            // Add loading states for better UX
            function addLoadingState(element) {
                const originalContent = element.innerHTML;
                element.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                element.disabled = true;

                return function removeLoadingState() {
                    element.innerHTML = originalContent;
                    element.disabled = false;
                };
            }

            // Add focus management for accessibility
            function manageFocus() {
                // Trap focus in sidebar when open on mobile
                if (isMobile() && sidebar.classList.contains('expanded')) {
                    const focusableElements = sidebar.querySelectorAll(
                        'a, button, input, select, textarea, [tabindex]:not([tabindex="-1"])'
                    );
                    
                    if (focusableElements.length > 0) {
                        focusableElements[0].focus();
                    }
                }
            }

            // Update focus management when sidebar state changes
            sidebar.addEventListener('transitionend', function() {
                if (sidebar.classList.contains('expanded')) {
                    manageFocus();
                }
            });

            // Add keyboard navigation support
            document.addEventListener('keydown', function(e) {
                // Handle arrow keys for sidebar navigation
                if (sidebar.classList.contains('expanded') || !isMobile()) {
                    const menuItems = Array.from(sidebar.querySelectorAll('.menu-item'));
                    const currentIndex = menuItems.findIndex(item => 
                        document.activeElement === item
                    );

                    if (currentIndex !== -1) {
                        let nextIndex;
                        
                        switch(e.key) {
                            case 'ArrowDown':
                                e.preventDefault();
                                nextIndex = (currentIndex + 1) % menuItems.length;
                                menuItems[nextIndex].focus();
                                break;
                            case 'ArrowUp':
                                e.preventDefault();
                                nextIndex = currentIndex === 0 ? 
                                    menuItems.length - 1 : currentIndex - 1;
                                menuItems[nextIndex].focus();
                                break;
                            case 'Enter':
                            case ' ':
                                e.preventDefault();
                                menuItems[currentIndex].click();
                                break;
                        }
                    }
                }
            });


        });
    </script>

    <!-- Bootstrap JS Bundle (already loaded above) -->
    
    <!-- Additional Scripts -->
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-database.js"></script>
    <script src="/js/firebase-config.js"></script>
    
    @stack('scripts')

</body>
</html>
               