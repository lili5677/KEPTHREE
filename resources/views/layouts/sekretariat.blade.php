<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem KEP – Sekretariat')</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="shortcut icon" href="{{ asset('kepicon.ico') }}">

    <!-- FONTS SAMA DENGAN PENELITI -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- BOOTSTRAP ICONS (DIPERLUKAN UNTUK ICON KOTAK) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        @import url('https://fonts.bunny.net/css?family=instrument-sans:400,500,600');

        /* ===== RESET & BASE - SAMA DENGAN PENELITI ===== */
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --navy-deep   : #0A1931;
            --navy-mid    : #1A3D63;
            --blue-accent : #4A7FA7;
            --blue-light  : #B3CFE5;
            --blue-pale   : #dbedf7;
            --surface     : #F6FAFD;
            --white       : #FFFFFF;
            --text-primary: #0A1931;
            --text-muted  : #5b7a96;
            --border      : #d0e3f0;
            --danger      : #dc3545;
            --success     : #198754;
            --warning     : #d97706;

            --sidebar-w           : 260px;
            --sidebar-collapsed-w : 52px;
            --radius      : 12px;
            --radius-sm   : 8px;
            --shadow-sm   : 0 1px 4px rgba(10,25,49,.08);
            --shadow-md   : 0 4px 16px rgba(10,25,49,.10);
            --shadow-lg   : 0 8px 32px rgba(10,25,49,.13);
            --transition  : .22s cubic-bezier(.4,0,.2,1);
        }

        html, body {
            height: 100%;
            font-family: 'Instrument Sans', 'Segoe UI', system-ui, sans-serif;
            font-size: 15px;
            color: var(--text-primary);
            background: var(--surface);
            -webkit-font-smoothing: antialiased;
        }

        a { text-decoration: none; color: inherit; }
        img { max-width: 100%; }

        /* ===== LAYOUT SHELL ===== */
        .kep-layout {
            display: flex;
            min-height: 100vh;
        }

        /* ===== SIDEBAR SEKRETARIAT ===== */
        #sidebar-sekretariat {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: var(--navy-mid);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 200;
            transition: width var(--transition), transform var(--transition);
            overflow: hidden;
        }

        /* ===== BRAND ===== */
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: .85rem;
            padding: 0 1rem;
            border-bottom: 1px solid rgba(255,255,255,.1);
            height: 70px;
            min-height: 70px;
            overflow: hidden;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .brand-shield {
            width: 38px;
            height: 38px;
            background: #d0e3f0;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1rem;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(0,0,0,.25);
            overflow: hidden;
        }

        .brand-shield img {
            width: 30px;
            height: 30px;
            object-fit: contain;
        }

        .brand-text {
            flex: 1;
            min-width: 0;
            overflow: hidden;
        }

        .brand-text .name {
            font-size: .93rem;
            font-weight: 600;
            color: #fff;
            line-height: 1.2;
            white-space: nowrap;
        }

        .brand-text .role {
            font-size: .72rem;
            color: var(--blue-light);
            opacity: .85;
            white-space: nowrap;
        }

        /* ===== NAVIGATION ===== */
        .sidebar-nav {
            flex: 1;
            padding: 1rem .6rem;
            display: flex;
            flex-direction: column;
            gap: 2px;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-nav::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,.15);
            border-radius: 4px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: .8rem;
            padding: .65rem .85rem;
            border-radius: var(--radius-sm);
            color: rgba(255,255,255,.72);
            font-size: .875rem;
            font-weight: 500;
            cursor: pointer;
            white-space: nowrap;
            overflow: hidden;
            transition: background var(--transition), color var(--transition);
            position: relative;
        }

        .nav-item i {
            font-size: 1.05rem;
            width: 22px;
            text-align: center;
            flex-shrink: 0;
            transition: color var(--transition);
        }

        .nav-item .nav-label {
            transition: opacity var(--transition), width var(--transition);
            white-space: nowrap;
        }

        .nav-item:hover {
            background: rgba(255,255,255,.1);
            color: #fff;
        }

        .nav-item.active {
            background: var(--blue-accent);
            color: #fff;
            box-shadow: 0 2px 8px rgba(74,127,167,.4);
        }

        .nav-item.active i {
            color: #fff;
        }

        /* ===== FOOTER ===== */
        .sidebar-footer {
            padding: 1rem .75rem;
            border-top: 1px solid rgba(255,255,255,.1);
            white-space: nowrap;
            overflow: hidden;
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .5rem .5rem;
            border-radius: var(--radius-sm);
        }

        .user-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--blue-accent);
            color: #fff;
            font-weight: 600;
            font-size: .82rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .user-info .u-name {
            font-size: .82rem;
            font-weight: 600;
            color: #fff;
        }

        .user-info .u-email {
            font-size: .72rem;
            color: var(--blue-light);
            opacity: .8;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            width: 100%;
            margin-top: .5rem;
            padding: .5rem .75rem;
            border-radius: var(--radius-sm);
            background: rgba(220,53,69,.18);
            border: 1px solid rgba(220,53,69,.3);
            color: #f8c0c0;
            font-size: .82rem;
            font-weight: 500;
            cursor: pointer;
            transition: background var(--transition), border-color var(--transition);
            font-family: 'Instrument Sans', sans-serif;
        }

        .logout-btn:hover {
            background: rgba(220,53,69,.32);
            border-color: rgba(220,53,69,.5);
            color: #fff;
        }

        /* ===== MAIN CONTENT ===== */
        #main {
            margin-left: var(--sidebar-w);
            flex: 1;
            min-height: 100vh;
            padding: 2rem 2.5rem;
            transition: margin-left var(--transition);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            #sidebar-sekretariat {
                transform: translateX(-100%);
                width: var(--sidebar-w) !important;
                transition: transform var(--transition), width var(--transition);
            }
            #sidebar-sekretariat.mobile-open {
                transform: translateX(0);
            }
            #main {
                margin-left: 0 !important;
                padding: 1rem;
                padding-top: calc(1rem + 60px);
            }
            .topbar {
                display: flex;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                z-index: 100;
            }
        }

        /* ============================================================
           OVERRIDE - WARNA AKTIF MENJADI PURPLE (KHAS SEKRETARIAT)
           ============================================================ */
        #sidebar-sekretariat .nav-item.active {
            background: #7C3AED !important;
            color: #fff !important;
            box-shadow: 0 2px 8px rgba(124, 58, 237, 0.4) !important;
        }

        #sidebar-sekretariat .nav-item.active i {
            color: #fff !important;
        }

        #sidebar-sekretariat .user-avatar {
            background: #8B5CF6 !important;
        }

        #sidebar-sekretariat .brand-text .role {
            color: #C4B5FD !important;
            opacity: 1 !important;
        }

        /* HIDE search bar */
        .sidebar-search {
            display: none !important;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50">

<div class="kep-layout">

    <!-- ===== SIDEBAR SEKRETARIAT ===== -->
    <aside id="sidebar-sekretariat">

        <!-- Brand -->
        <div class="sidebar-brand">
            <div class="brand-shield" style="background: #d0e3f0; border-radius: 10px; width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,.25); overflow: hidden;">
                <img src="{{ asset('favicon-32x32.png') }}" alt="Logo KEPTHREE" style="width: 30px; height: 30px; object-fit: contain;">
            </div>
            <div class="brand-text">
                <div class="name">KEPTHREE</div>
                <div class="role">Sekretariat</div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="sidebar-nav">
            <a href="{{ route('sekretariat.dashboard') }}"
               class="nav-item {{ request()->routeIs('sekretariat.dashboard') ? 'active' : '' }}"
               data-tooltip="Dashboard">
                <i class="bi bi-grid-1x2"></i>
                <span class="nav-label">Dashboard</span>
            </a>

            <a href="{{ route('sekretariat.verifikasi.index') }}"
               class="nav-item {{ request()->routeIs('sekretariat.verifikasi.*') ? 'active' : '' }}"
               data-tooltip="Verifikasi Dokumen">
                <i class="bi bi-file-earmark-text"></i>
                <span class="nav-label">Verifikasi Dokumen</span>
            </a>

            <a href="#"
               class="nav-item {{ request()->routeIs('sekretariat.review.*') ? 'active' : '' }}"
               data-tooltip="Assignment Reviewer">
                <i class="bi bi-people"></i>
                <span class="nav-label">Assignment Reviewer</span>
            </a>

            <a href="{{ route('sekretariat.decision.index') }}"
               class="nav-item {{ request()->routeIs('sekretariat.decision.*') ? 'active' : '' }}"
               data-tooltip="Secretary Decision">
                <i class="bi bi-check2-circle"></i>
                <span class="nav-label">Secretary Decision</span>
            </a>

            <a href="#"
               class="nav-item {{ request()->routeIs('sekretariat.riwayat') ? 'active' : '' }}"
               data-tooltip="Riwayat Proposal">
                <i class="bi bi-clock-history"></i>
                <span class="nav-label">Riwayat Proposal</span>
            </a>
        </nav>

        <!-- Footer -->
        <div class="sidebar-footer">
            <div class="user-card">
                <div class="user-avatar">
                    {{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 1)) }}
                </div>
                <div class="user-info">
                    <div class="u-name">{{ auth()->user()->name ?? 'Sekretariat' }}</div>
                    <div class="u-email">{{ auth()->user()->email ?? 'sekretariat@kep.ac.id' }}</div>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Keluar</span>
                </button>
            </form>
        </div>

    </aside>

    <!-- ===== MAIN CONTENT ===== -->
    <main id="main">
        @yield('content')
    </main>

</div>

@stack('scripts')
</body>
</html>