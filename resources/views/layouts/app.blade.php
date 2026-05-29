<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Spon++') }} - Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --bg: #f8fafc;
            --text: #2d3748;
            --card: #ffffff;
            --border: #e2e8f0;
            --navbar: #ffffff;
            --sidebar-link: #4a5568;
            --sidebar-hover: rgba(118, 75, 162, 0.1);
            --text-secondary: #64748b;
        }

        [data-theme="dark"] {
            --bg: #0f172a;
            --text: #f1f5f9;
            --card: #1e293b;
            --border: #334155;
            --navbar: #1e293b;
            --sidebar-link: #a0aec0;
            --sidebar-hover: rgba(118, 75, 162, 0.2);
            --text-muted: #94a3b8;
            --text-secondary: #94a3b8;
            --placeholder: #64748b;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            scrollbar-gutter: stable;
            transition: background-color 0.3s, color 0.3s;
        }

        /* Ensure all text elements respect the color variable in dark mode */
        [data-theme="dark"] p, 
        [data-theme="dark"] span, 
        [data-theme="dark"] td, 
        [data-theme="dark"] th, 
        [data-theme="dark"] h1, 
        [data-theme="dark"] h2, 
        [data-theme="dark"] h3, 
        [data-theme="dark"] h4, 
        [data-theme="dark"] h5, 
        [data-theme="dark"] h6, 
        [data-theme="dark"] li,
        [data-theme="dark"] .card-title,
        [data-theme="dark"] .card-subtitle,
        [data-theme="dark"] .navbar-welcome {
            color: var(--text);
        }

        [data-theme="dark"] label {
            color: var(--text);
        }

        [data-theme="dark"] .text-muted, 
        [data-theme="dark"] small, 
        [data-theme="dark"] .text-secondary, 
        [data-theme="dark"] .breadcrumb-item, 
        [data-theme="dark"] .class-description, 
        [data-theme="dark"] .profile-label, 
        [data-theme="dark"] .info-label,
        [data-theme="dark"] .breadcrumb-current,
        [data-theme="dark"] .breadcrumb-sep {
            color: var(--text-secondary) !important;
        }

        [data-theme="dark"] ::placeholder {
            color: var(--placeholder) !important;
        }

        /* Input fields themselves */
        [data-theme="dark"] input, 
        [data-theme="dark"] textarea, 
        [data-theme="dark"] select {
            color: var(--text);
            background-color: var(--card);
            border-color: var(--border);
        }
        /* Fix Bug 2: Hide TinyMCE stray warnings that bleed into layout */
        .tox-notifications-container, .tox-throbber {
            display: none !important;
        }
        .navbar {
            background: var(--navbar);
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            padding: 15px 0;
            transition: background-color 0.3s;
        }
        .navbar-brand {
            font-weight: 800;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 1.5rem;
        }
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
            transition: background-color 0.3s, border-color 0.3s;
        }
        .sidebar-link {
            padding: 12px 20px;
            border-radius: 10px;
            color: var(--sidebar-link);
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.3s;
            margin-bottom: 5px;
            gap: 12px;
        }
        .sidebar-link:hover, .sidebar-link.active {
            background: var(--sidebar-hover);
            color: #764ba2;
            font-weight: 600;
        }
        .btn-logout {
            color: #e53e3e;
            background: none;
            border: none;
            padding: 0;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-logout:hover {
            color: #c53030;
        }

        /* Alert messages — always dark text regardless of theme */
        .alert-error, .alert-danger, .alert-success, .alert-warning {
            color: #1e293b !important;
            font-weight: 500;
        }

        [data-theme="dark"] .alert-error,
        [data-theme="dark"] .alert-danger,
        [data-theme="dark"] .alert-success,
        [data-theme="dark"] .alert-warning {
            color: #1e293b !important;
        }

        .alert-error, .alert-danger {
            background-color: #fee2e2 !important;
            border: 1px solid #fca5a5 !important;
            color: #991b1b !important;
            border-radius: 8px !important;
            padding: 10px 16px !important;
        }

        [data-theme="dark"] .alert-error,
        [data-theme="dark"] .alert-danger {
            background-color: #3f0000 !important;
            border-color: #7f1d1d !important;
            color: #fca5a5 !important;
        }

        .class-code-badge {
            background-color: #1e293b;
            color: #f1f5f9;
            border: 1px solid #334155;
            border-radius: 6px;
            padding: 2px 10px;
            font-size: 0.8rem;
            font-family: monospace;
            display: inline-block;
        }

        [data-theme="dark"] .class-code-badge {
            background-color: #0f172a;
            color: #f1f5f9;
            border-color: #475569;
        }

        /* Material Content & Tags Dark Mode */
        [data-theme="dark"] .material-content,
        [data-theme="dark"] .material-content * {
            color: var(--text) !important;
        }

        [data-theme="dark"] .bg-light.material-content {
            background-color: var(--sidebar-hover) !important;
        }

        [data-theme="dark"] .material-tag {
            color: var(--text) !important;
            background-color: var(--card) !important;
            border: 1px solid var(--border) !important;
        }

        /* Toast Alert Styles */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }
        .toast-custom {
            min-width: 320px;
            max-width: 450px;
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 12px;
            color: white;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideIn 0.3s ease-out;
            transition: all 0.3s ease;
            position: relative;
            touch-action: pan-x;
        }
        .toast-hide {
            opacity: 0;
            transform: translateX(100%);
        }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .toast-success { background: #10b981; }
        .toast-error { background: #ef4444; }
        .toast-warning { background: #f59e0b; }
        .toast-info { background: #3b82f6; }
        
        .toast-dismiss {
            background: none;
            border: none;
            color: white;
            padding: 4px;
            cursor: pointer;
            opacity: 0.7;
            transition: opacity 0.2s;
            margin-left: auto;
            line-height: 1;
        }
        .toast-dismiss:hover { opacity: 1; }

        /* Custom Confirm Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            backdrop-filter: blur(4px);
        }
        .modal-box {
            background: var(--card);
            color: var(--text);
            padding: 30px;
            border-radius: 20px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
            text-align: center;
        }
        .modal-icon {
            width: 60px;
            height: 60px;
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .modal-box h3 {
            margin-bottom: 15px;
            font-weight: 700;
            color: var(--text);
        }
        .modal-box p {
            color: var(--sidebar-link);
            margin-bottom: 25px;
        }
        .modal-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
        }
        .modal-actions button {
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            border: none;
            transition: all 0.2s;
        }
        #modal-cancel { background: var(--bg); color: var(--text); }
        #modal-cancel:hover { background: var(--sidebar-hover); }
        #modal-confirm { background: #ef4444; color: white; }
        #modal-confirm:hover { background: #dc2626; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3); }

        .form-control, .form-select {
            background-color: var(--card);
            border-color: var(--border);
            color: var(--text);
        }
        .form-control:focus, .form-select:focus {
            background-color: var(--card);
            color: var(--text);
            border-color: #764ba2;
            box-shadow: 0 0 0 0.25rem rgba(118, 75, 162, 0.25);
        }
        .text-muted {
            color: var(--text-secondary) !important;
        }
        .text-dark {
            color: var(--text) !important;
        }
        .btn-light {
            background-color: var(--bg);
            border-color: var(--border);
            color: var(--text);
        }
        .btn-light:hover {
            background-color: var(--sidebar-hover);
        }
        .table {
            --bs-table-bg: transparent;
            --bs-table-color: var(--text);
            --bs-table-border-color: var(--border);
        }
        .breadcrumb-separator,
        .breadcrumb-item + .breadcrumb-item::before {
            color: #64748b !important; /* slate-500 — visible on both light and dark bg */
        }
        [data-theme="dark"] .breadcrumb-separator,
        [data-theme="dark"] .breadcrumb-item + .breadcrumb-item::before {
            color: #94a3b8 !important; /* slate-400 — slightly brighter for dark bg */
        }
        .btn-outline-dark {
            --bs-btn-color: var(--text);
            --bs-btn-border-color: var(--text);
            --bs-btn-hover-color: var(--card);
            --bs-btn-hover-bg: var(--text);
            --bs-btn-hover-border-color: var(--text);
            --bs-btn-active-color: var(--card);
            --bs-btn-active-bg: var(--text);
        }
        .modal-content {
            background-color: var(--card);
            color: var(--text);
        }
        .btn-close {
            filter: var(--btn-close-filter, none);
        }
        [data-theme="dark"] .btn-close {
            --btn-close-filter: invert(1) grayscale(100%) brightness(200%);
        }
    </style>
    @stack('styles')
    <script>
        // Dark Mode Initialization
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();

        function toggleTheme() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateThemeIcon(newTheme);
            window.dispatchEvent(new Event('themeChanged'));
        }

        function updateThemeIcon(theme) {
            const iconContainer = document.getElementById('theme-icon');
            if (iconContainer) {
                iconContainer.setAttribute('data-lucide', theme === 'dark' ? 'sun' : 'moon');
                lucide.createIcons();
            }
        }
        function dismissToast(el) {
            if (!el) return;
            el.classList.add('toast-hide');
            setTimeout(() => el.remove(), 300);
        }

        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            if (!container) return;
            
            const toast = document.createElement('div');
            toast.className = `toast-custom toast-${type}`;
            
            let icon = 'info';
            if(type === 'success') icon = 'circle-check';
            if(type === 'error') icon = 'circle-x';
            if(type === 'warning') icon = 'triangle-alert';

            toast.innerHTML = `
                <i data-lucide="${icon}" style="width:20px;height:20px;flex-shrink:0"></i>
                <div style="flex-grow:1">${message}</div>
                <button class="toast-dismiss" onclick="dismissToast(this.parentElement)">
                    <i data-lucide="x" style="width:18px;height:18px"></i>
                </button>
            `;
            
            container.appendChild(toast);
            lucide.createIcons({ props: { "stroke-width": 2.5 } });

            // Swipe to dismiss
            let startX;
            toast.addEventListener('touchstart', e => startX = e.touches[0].clientX, {passive: true});
            toast.addEventListener('touchend', e => {
                const delta = e.changedTouches[0].clientX - startX;
                if (Math.abs(delta) > 80) dismissToast(toast);
            }, {passive: true});

            // Auto-dismiss
            const timer = setTimeout(() => dismissToast(toast), 4000);
            toast.querySelector('.toast-dismiss').addEventListener('click', () => clearTimeout(timer));
        }

        function showConfirmModal({ title, message, onConfirm }) {
            document.getElementById('modal-title').textContent = title;
            document.getElementById('modal-message').textContent = message;
            const modal = document.getElementById('confirm-modal');
            modal.style.display = 'flex';
            
            const confirmBtn = document.getElementById('modal-confirm');
            confirmBtn.onclick = function() {
                closeConfirmModal();
                onConfirm();
            };
        }

        function closeConfirmModal() {
            document.getElementById('confirm-modal').style.display = 'none';
        }

            @if(auth()->check())
            (function() {
                let idleTimer;
                const IDLE_LIMIT = 5 * 60 * 1000; // 5 minutes

                function resetTimer() {
                    clearTimeout(idleTimer);
                    idleTimer = setTimeout(() => {
                        window.location.href = "{{ route('logout.idle') }}";
                    }, IDLE_LIMIT);
                }

                // More comprehensive list of events to detect activity
                ['mousedown', 'mousemove', 'keydown', 'scroll', 'touchstart', 'click'].forEach(event => {
                    document.addEventListener(event, resetTimer, { passive: true });
                });
                resetTimer();
            })();
            @endif

        // Global Input Normalization & Restrictions
        document.addEventListener('input', function (e) {
            const target = e.target;
            
            // 1. Join Class Code - Auto Uppercase
            if (target.name === 'join_code' || target.id === 'join_code') {
                target.value = target.value.toUpperCase();
            }

            if (target.id === 'username' || target.name === 'username') {
                target.value = target.value.toLowerCase().replace(/[^a-z0-9_]/g, '');
            }
            
            // Allow more characters for email and general login identifier
            if (target.id === 'email' || target.name === 'email' || target.id === 'login' || target.name === 'login' || target.id === 'login_identifier' || target.name === 'identifier') {
                target.value = target.value.toLowerCase();
                // No character whitelist here, allow standard email/username chars
            }

            // 3. Name Field - Alphabets & Spaces Only
            if (target.id === 'name' || target.name === 'name') {
                target.value = target.value.replace(/[^a-zA-Z\s]/g, '');
            }
        });
    </script>
</head>
<body>
    <div id="confirm-modal" class="modal-overlay">
        <div class="modal-box shadow-lg">
            <div class="modal-icon">
                <i data-lucide="triangle-alert" style="width:32px;height:32px"></i>
            </div>
            <h3 id="modal-title">Confirm</h3>
            <p id="modal-message">Are you sure?</p>
            <div class="modal-actions">
                <button id="modal-cancel" onclick="closeConfirmModal()">Cancel</button>
                <button id="modal-confirm" class="btn-danger">Yes, Confirm</button>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('confirm-modal').addEventListener('click', function(e) {
            if (e.target === this) closeConfirmModal();
        });
    </script>

    <div class="toast-container" id="toastContainer"></div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Initial Lucide Icons
            lucide.createIcons();

            // Initialize theme icon
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            updateThemeIcon(currentTheme);

            @if(session('success'))
                showToast("{{ session('success') }}", 'success');
            @endif
            @if(session('error'))
                showToast("{{ session('error') }}", 'error');
            @endif
            @if(session('warning'))
                showToast("{{ session('warning') }}", 'warning');
            @endif
            @if(session('info'))
                showToast("{{ session('info') }}", 'info');
            @endif
            @if($errors->any())
                @foreach($errors->all() as $error)
                    showToast("{{ $error }}", 'error');
                @endforeach
            @endif
        });
    </script>
    <nav class="navbar sticky-top mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">Spon++</a>
            <div class="d-flex align-items-center">
                <!-- Theme Toggle -->
                <button type="button" class="btn btn-link text-muted me-3 p-0" id="theme-toggle-btn" onclick="toggleTheme()" title="Toggle Dark/Light Mode">
                    <i id="theme-icon" data-lucide="moon" style="width:20px;height:20px"></i>
                </button>

                <span class="me-3 text-muted small">Welcome, <strong>{{ auth()->user()->name }}</strong></span>
                <button type="button" class="btn-logout small" onclick="showConfirmModal({
                    title: 'Logout',
                    message: 'Are you sure you want to logout?',
                    onConfirm: () => document.getElementById('logout-form').submit()
                })">
                    <i data-lucide="log-out" style="width:16px;height:16px"></i> Logout
                </button>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                    @csrf
                </form>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="card p-3 mb-4">
                    <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i data-lucide="layout-dashboard" style="width:20px;height:20px"></i> Dashboard
                    </a>
                    <a href="{{ route('classrooms.index') }}" class="sidebar-link {{ request()->routeIs('classrooms.*') ? 'active' : '' }}">
                        <i data-lucide="book-open" style="width:20px;height:20px"></i> My Classrooms
                    </a>
                    <a href="{{ route('profile.show') }}" class="sidebar-link {{ request()->routeIs('profile.show') ? 'active' : '' }}">
                        <i data-lucide="user" style="width:20px;height:20px"></i> My Profile
                    </a>
                </div>
            </div>
            <div class="col-md-9">
                @yield('content')
            </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
