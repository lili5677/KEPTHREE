<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Sistem KEP</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --sidebar-w: 220px;
            --sidebar-bg: #1a1d2e;
            --sidebar-text: #c8ccd8;
            --sidebar-active-bg: #6366f1;
            --sidebar-hover-bg: rgba(99,102,241,0.12);
            --sidebar-icon: #7b80a0;
            --main-bg: #f5f6fa;
            --card-bg: #ffffff;
            --text-primary: #1e2130;
            --text-muted: #8a8fa8;
            --border: #e8eaf2;
            --accent: #6366f1;
            --accent-hover: #4f52d9;
            --radius: 10px;
            --font: 'DM Sans', sans-serif;
        }

        html {
            width: 100%;
            overflow-x: hidden;
        }

        body {
            font-family: var(--font);
            background: var(--main-bg);
            color: var(--text-primary);
            display: flex;
            min-height: 100vh;
            width: 100%;
            overflow-x: hidden;
        }

        .sidebar {
            width: var(--sidebar-w);
            background: var(--sidebar-bg);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 22px 20px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.07);
        }

        .sidebar-brand-icon {
            width: 34px;
            height: 34px;
            background: var(--accent);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .sidebar-brand-icon svg {
            width: 18px;
            height: 18px;
        }

        .sidebar-brand-title {
            font-size: 14px;
            font-weight: 700;
            color: #fff;
        }

        .sidebar-brand-sub {
            font-size: 11px;
            color: var(--sidebar-icon);
        }

        .sidebar-nav {
            flex: 1;
            padding: 12px 0;
            overflow-y: auto;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 10px 20px;
            text-decoration: none;
            color: var(--sidebar-text);
            font-size: 13.5px;
            font-weight: 500;
            transition: all 0.15s;
            position: relative;
        }

        .sidebar-nav a:hover {
            background: var(--sidebar-hover-bg);
            color: #fff;
        }

        .sidebar-nav a:hover svg {
            stroke: #fff;
        }

        .sidebar-nav a.active {
            background: var(--sidebar-active-bg);
            color: #fff;
        }

        .sidebar-nav a.active svg {
            stroke: #fff;
        }

        .sidebar-nav a.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: rgba(255,255,255,0.55);
            border-radius: 0 2px 2px 0;
        }

        .sidebar-nav svg {
            width: 17px;
            height: 17px;
            stroke: var(--sidebar-icon);
            stroke-width: 1.8;
            fill: none;
            flex-shrink: 0;
            transition: stroke 0.15s;
        }

        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid rgba(255,255,255,0.07);
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
        }

        .sidebar-avatar {
            width: 34px;
            height: 34px;
            background: rgba(99,102,241,0.25);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: var(--accent);
            flex-shrink: 0;
        }

        .sidebar-user-name {
            font-size: 13px;
            font-weight: 600;
            color: #fff;
            line-height: 1.3;
        }

        .sidebar-user-email {
            font-size: 11px;
            color: var(--sidebar-icon);
            word-break: break-word;
        }

        .btn-logout {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 9px;
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            font-family: var(--font);
            cursor: pointer;
            text-decoration: none;
            transition: background 0.15s;
        }

        .btn-logout:hover {
            background: #dc2626;
        }

        .btn-logout svg {
            width: 15px;
            height: 15px;
            stroke: white;
            stroke-width: 2;
            fill: none;
        }

        .main-content {
            margin-left: var(--sidebar-w);
            flex: 1;
            min-height: 100vh;
            padding: 30px 32px;
            width: calc(100% - var(--sidebar-w));
        }

        .page-header {
            margin-bottom: 24px;
        }

        .page-title {
            font-size: 22px;
            font-weight: 700;
        }

        .page-subtitle {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 3px;
        }

        .card {
            background: var(--card-bg);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            padding: 24px;
            width: 100%;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13.5px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 9px 18px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 600;
            font-family: var(--font);
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all 0.15s;
        }

        .btn-primary {
            background: var(--accent);
            color: white;
        }

        .btn-primary:hover {
            background: var(--accent-hover);
        }

        .btn-primary svg {
            width: 15px;
            height: 15px;
            stroke: white;
            stroke-width: 2.2;
            fill: none;
        }

        .btn-secondary {
            padding: 9px 18px;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 600;
            font-family: var(--font);
            cursor: pointer;
            background: white;
            color: var(--text-primary);
            transition: all 0.15s;
        }

        .btn-secondary:hover {
            background: #f9fafb;
        }

        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.45);
            z-index: 999;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-box {
            background: white;
            border-radius: 14px;
            width: 480px;
            max-width: 100%;
            padding: 28px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
            gap: 12px;
        }

        .modal-title {
            font-size: 17px;
            font-weight: 700;
        }

        .modal-close {
            width: 30px;
            height: 30px;
            border: none;
            background: #f3f4f6;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: #6b7280;
            flex-shrink: 0;
        }

        .modal-close:hover {
            background: #e5e7eb;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .form-label span {
            color: #ef4444;
        }

        .form-control {
            width: 100%;
            padding: 10px 13px;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            font-family: var(--font);
            color: var(--text-primary);
            background: #fafafa;
            transition: border-color 0.15s;
            outline: none;
        }

        .form-control:focus {
            border-color: var(--accent);
            background: white;
        }

        .form-error {
            font-size: 12px;
            color: #ef4444;
            margin-top: 5px;
        }

        .form-hint {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 5px;
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 6px;
            padding: 6px 10px;
        }

        .modal-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 24px;
        }

        .sidebar-brand {
            position: relative;
        }

        .sidebar-brand-logo {
            width: 34px;
            height: 34px;
            background: #d0e3f0;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
        }

        .sidebar-brand-logo img {
            width: 26px;
            height: 26px;
            object-fit: contain;
            display: block;
        }

        .sidebar-brand-text {
            min-width: 0;
            flex: 1;
        }

        .sidebar-collapse-btn,
        .sidebar-expand-btn {
            width: 28px;
            height: 28px;
            border: none;
            border-radius: 7px;
            background: transparent;
            color: var(--sidebar-icon);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.15s, color 0.15s;
        }

        .sidebar-collapse-btn:hover,
        .sidebar-expand-btn:hover {
            background: rgba(255,255,255,0.08);
            color: #fff;
        }

        .sidebar-expand-btn {
            display: none;
        }

        .sidebar-link-text {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-user-info {
            min-width: 0;
        }

        /* Collapsed desktop */
        body.admin-sidebar-collapsed {
            --sidebar-w: 68px;
        }

        body.admin-sidebar-collapsed .sidebar-brand {
            justify-content: center;
            padding: 18px 0;
            gap: 0;
        }

        /* Saat sidebar ciut, semua brand content hilang: logo, teks, tombol collapse */
        body.admin-sidebar-collapsed .sidebar-brand-content {
            display: none;
        }

        /* Yang muncul hanya tombol expand */
        body.admin-sidebar-collapsed .sidebar-expand-btn {
            display: flex;
        }

        body.admin-sidebar-collapsed .sidebar-nav {
            padding: 12px 8px;
        }

        body.admin-sidebar-collapsed .sidebar-nav a {
            width: 52px;
            height: 46px;
            justify-content: center;
            padding: 0;
            gap: 0;
            border-radius: 12px;
        }

        body.admin-sidebar-collapsed .sidebar-nav a.active::before {
            display: none;
        }

        body.admin-sidebar-collapsed .sidebar-link-text,
        body.admin-sidebar-collapsed .sidebar-user-info {
            display: none;
        }

        body.admin-sidebar-collapsed .sidebar-footer {
            padding: 12px 8px;
        }

        body.admin-sidebar-collapsed .sidebar-user {
            justify-content: center;
        }

        body.admin-sidebar-collapsed .btn-logout {
            width: 52px;
            height: 44px;
            padding: 0;
            justify-content: center;
        }
        @media (max-width: 768px) {
        :root {
            --sidebar-w: 68px;
        }

        body {
            display: flex;
        }

        .sidebar {
            position: fixed;
            width: var(--sidebar-w);
            min-height: 100vh;
        }

        .sidebar-brand {
            justify-content: center;
            padding: 16px 0;
        }

        .sidebar-brand-content,
        .sidebar-collapse-btn,
        .sidebar-link-text,
        .sidebar-user-info {
            display: none !important;
        }

        .sidebar-expand-btn {
            display: flex !important;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            overflow-y: auto;
            padding: 10px 8px;
            gap: 6px;
            white-space: normal;
        }

        .sidebar-nav a {
            width: 52px;
            height: 48px;
            padding: 0;
            border-radius: 12px;
            justify-content: center;
            gap: 0;
            flex-shrink: 0;
        }

        .sidebar-nav svg {
            width: 20px;
            height: 20px;
        }

        .sidebar-nav a.active::before {
            display: none;
        }

        .sidebar-footer {
            position: relative;
            width: 100%;
            padding: 10px 8px;
        }

        .sidebar-user {
            justify-content: center;
            margin-bottom: 10px;
        }

        .sidebar-avatar {
            width: 38px;
            height: 38px;
        }

        .btn-logout {
            width: 52px;
            height: 44px;
            padding: 0;
            justify-content: center;
            border-radius: 12px;
        }

        .btn-logout svg {
            width: 18px;
            height: 18px;
        }

        .main-content {
            margin-left: var(--sidebar-w);
            width: calc(100% - var(--sidebar-w));
            min-height: 100vh;
            padding: 20px 16px;
        }

        .card {
            padding: 18px;
        }

        .modal-box {
            width: 100%;
            padding: 22px;
        }

        .modal-actions {
            flex-direction: column-reverse;
        }

        .modal-actions .btn,
        .modal-actions .btn-secondary {
            width: 100%;
            justify-content: center;
        }
    }
    </style>

    @stack('styles')
</head>
<body>

    @include('components.admin-sidebar')

    <main class="main-content">
        @yield('content')
    </main>
    <script>
    (function () {
        const collapseBtn = document.getElementById('adminSidebarCollapseBtn');
        const expandBtn   = document.getElementById('adminSidebarExpandBtn');
        const KEY         = 'kep_admin_sidebar_collapsed';

        function isSmallScreen() {
            return window.innerWidth <= 768;
        }

        function setCollapsed(collapsed, save = true) {
            document.body.classList.toggle('admin-sidebar-collapsed', collapsed);

            if (save && !isSmallScreen()) {
                localStorage.setItem(KEY, collapsed ? '1' : '0');
            }
        }

        function syncSidebarState() {
            if (isSmallScreen()) {
                setCollapsed(true, false);
                return;
            }

            setCollapsed(localStorage.getItem(KEY) === '1', false);
        }

        if (collapseBtn) {
            collapseBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                setCollapsed(true);
            });
        }

        if (expandBtn) {
            expandBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                setCollapsed(false);
            });
        }

        syncSidebarState();
        window.addEventListener('resize', syncSidebarState);
    })();
    </script>
    @stack('scripts')
</body>
</html>