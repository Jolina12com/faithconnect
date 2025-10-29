@extends('admin.dashboard')

@section('content')
<div class="broadcast-wrapper">
    <div class="broadcast-container">
        <!-- Header Section -->
        <div class="broadcast-header">
            <div class="header-content">
                <div class="header-icon">
                    <i class="fas fa-broadcast-tower"></i>
                </div>
                <div class="header-text">
                    <h1 class="header-title">Live Stream</h1>
                    <p class="header-subtitle">Start broadcasting to your audience</p>
                </div>
            </div>
        </div>

        <!-- Main Broadcast Card -->
        <div class="broadcast-card">
            <!-- React mounting point -->
            <div id="broadcaster-app" class="broadcaster-content">
                <!-- React will mount here -->
            </div>
        </div>
    </div>
</div>

<!-- Hidden input for broadcaster ID -->
<input type="hidden" id="broadcaster-id" value="{{ auth()->id() }}">

<!-- Load React and the compiled app.jsx -->
@vite('resources/js/app.jsx')

<style>
:root {
    --primary: #3b82f6;
    --primary-dark: #2563eb;
    --primary-light: #60a5fa;
    --danger: #ef4444;
    --danger-dark: #dc2626;
    --success: #10b981;
    --warning: #f59e0b;
    --dark: #1f2937;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-800: #1f2937;
    --white: #ffffff;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    --radius-sm: 6px;
    --radius: 8px;
    --radius-lg: 12px;
    --radius-xl: 16px;
}

/* Wrapper & Container */
.broadcast-wrapper {
    min-height: calc(100vh - 120px);
    padding: 1rem;
    background: var(--gray-50);
}

.broadcast-container {
    max-width: 1200px;
    margin: 0 auto;
}

/* Header */
.broadcast-header {
    margin-bottom: 1.5rem;
}

.header-content {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.header-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    font-size: 1.5rem;
    flex-shrink: 0;
    box-shadow: var(--shadow-md);
}

.header-text {
    flex: 1;
    min-width: 0;
}

.header-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--gray-800);
    margin: 0 0 0.25rem 0;
    line-height: 1.2;
}

.header-subtitle {
    font-size: 0.95rem;
    color: var(--gray-500);
    margin: 0;
}

/* Main Card */
.broadcast-card {
    background: var(--white);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-lg);
    overflow: hidden;
    border: 1px solid var(--gray-200);
}

.broadcaster-content {
    padding: 1.5rem;
}

/* Video Styles */
.broadcaster-content video {
    width: 100%;
    max-width: 100%;
    aspect-ratio: 16/9;
    background: #000;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-xl);
    border: 2px solid var(--gray-800);
    margin-bottom: 1.5rem;
    object-fit: cover;
}

/* Controls Container */
.controls {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-top: 1.5rem;
}

/* Button Styles */
.controls button,
.btn-broadcast {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    font-size: 0.95rem;
    font-weight: 600;
    border-radius: var(--radius);
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: var(--shadow-sm);
    white-space: nowrap;
    min-height: 44px;
    flex: 0 1 auto;
}

.controls button:active,
.btn-broadcast:active {
    transform: translateY(1px);
}

/* Primary Button */
.controls button,
.btn-primary {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: var(--white);
}

.controls button:hover,
.btn-primary:hover {
    background: linear-gradient(135deg, var(--primary-dark), #1e40af);
    box-shadow: var(--shadow-md);
    transform: translateY(-1px);
}

.controls button:disabled,
.btn-primary:disabled {
    background: var(--gray-300);
    color: var(--gray-500);
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

/* Danger Button */
.controls button.danger,
.btn-danger {
    background: linear-gradient(135deg, var(--danger), var(--danger-dark));
    color: var(--white);
}

.controls button.danger:hover,
.btn-danger:hover {
    background: linear-gradient(135deg, var(--danger-dark), #b91c1c);
    box-shadow: var(--shadow-md);
}

/* Success Button */
.btn-success {
    background: linear-gradient(135deg, var(--success), #059669);
    color: var(--white);
}

.btn-success:hover {
    background: linear-gradient(135deg, #059669, #047857);
}

/* Secondary Button */
.btn-secondary {
    background: var(--gray-100);
    color: var(--gray-700);
    border: 1px solid var(--gray-300);
}

.btn-secondary:hover {
    background: var(--gray-200);
    border-color: var(--gray-400);
}

/* Status Badge */
.status {
    margin-top: 1.5rem;
    padding: 1rem 1.25rem;
    border-radius: var(--radius);
    background: var(--gray-100);
    border-left: 4px solid var(--primary);
    font-weight: 600;
    font-size: 0.95rem;
    color: var(--gray-700);
    display: flex;
    align-items: center;
    gap: 0.75rem;
    word-break: break-word;
    width: 100%;
    box-sizing: border-box;
}

.status::before {
    content: '';
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--primary);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.5;
        transform: scale(1.2);
    }
}

.status.live {
    background: rgba(239, 68, 68, 0.1);
    border-left-color: var(--danger);
    color: var(--danger-dark);
}

.status.live::before {
    background: var(--danger);
}

.status.success {
    background: rgba(16, 185, 129, 0.1);
    border-left-color: var(--success);
    color: #047857;
}

.status.success::before {
    background: var(--success);
}

.status.warning {
    background: rgba(245, 158, 11, 0.1);
    border-left-color: var(--warning);
    color: #d97706;
}

.status.warning::before {
    background: var(--warning);
}

/* Stream Info Grid */
.stream-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 1.5rem;
}

.info-card {
    background: var(--gray-50);
    padding: 1rem;
    border-radius: var(--radius);
    border: 1px solid var(--gray-200);
}

.info-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--gray-500);
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.info-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--gray-800);
}

/* Input Groups */
.input-group {
    margin-bottom: 1.5rem;
}

.input-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 0.5rem;
}

.input-field {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid var(--gray-300);
    border-radius: var(--radius);
    font-size: 0.95rem;
    transition: all 0.2s ease;
    background: var(--white);
}

.input-field:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Responsive Breakpoints */

/* Tablet (768px and below) */
@media (max-width: 768px) {
    .broadcast-wrapper {
        padding: 0;
        background: #000;
    }

    .broadcast-header {
        display: none;
    }

    .broadcast-card {
        border-radius: 0;
        border: none;
        height: 100vh;
    }

    .broadcaster-content {
        padding: 0;
        height: 100vh;
    }

    .broadcaster-content video {
        height: 100vh;
        object-fit: cover;
        border-radius: 0;
    }
}

/* Mobile (576px and below) */
@media (max-width: 576px) {
    .broadcast-wrapper {
        padding: 0;
        min-height: 100vh;
        background: #000;
    }

    .broadcast-header {
        display: none;
    }

    .broadcast-card {
        border-radius: 0;
        border: none;
        box-shadow: none;
        height: 100vh;
    }

    .broadcaster-content {
        padding: 0;
        height: 100vh;
    }

    .broadcaster-content video {
        border-radius: 0;
        margin-bottom: 0;
        height: 100vh;
        object-fit: cover;
    }

    .stream-info {
        display: none;
    }
}

/* Super Small Screens (400px and below) */
@media (max-width: 400px) {
    .broadcast-wrapper {
        padding: 0.25rem;
    }

    .header-title {
        font-size: 1.125rem;
    }

    .broadcaster-content {
        padding: 0.625rem;
    }

    .controls {
        gap: 0.5rem;
    }

    .controls button,
    .btn-broadcast {
        padding: 0.625rem 0.75rem;
        font-size: 0.8125rem;
        min-height: 42px;
    }
}

/* Desktop - Super Minimized (below 400px width) */
@media (min-width: 769px) and (max-width: 400px) {
    .broadcast-container {
        max-width: 100%;
    }
    
    .controls {
        flex-direction: column;
    }
    
    .controls button {
        width: 100%;
    }
}

/* Landscape Mobile */
@media (max-height: 500px) and (orientation: landscape) {
    .broadcast-wrapper {
        min-height: auto;
        padding: 0.5rem;
    }

    .broadcast-header {
        margin-bottom: 0.75rem;
    }

    .broadcaster-content video {
        aspect-ratio: 21/9;
        margin-bottom: 0.75rem;
    }

    .controls {
        margin-top: 0.75rem;
    }

    .status,
    .stream-info {
        margin-top: 0.75rem;
    }
}

/* Print Styles */
@media print {
    .controls,
    .status {
        display: none;
    }
}

/* High DPI Screens */
@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
    .broadcaster-content video {
        image-rendering: -webkit-optimize-contrast;
    }
}

/* Loading State */
.loading-spinner {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top-color: var(--white);
    animation: spinner 0.6s linear infinite;
}

@keyframes spinner {
    to { transform: rotate(360deg); }
}

/* Accessibility */
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* Focus Styles for Accessibility */
.controls button:focus-visible,
.input-field:focus-visible {
    outline: 2px solid var(--primary);
    outline-offset: 2px;
}

/* Dark Mode Support (Optional) */
@media (prefers-color-scheme: dark) {
    :root {
        --gray-50: #111827;
        --gray-100: #1f2937;
        --white: #1f2937;
        --gray-800: #f9fafb;
    }
}
</style>
@endsection