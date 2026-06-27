<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Ketua') — KEPTHREE</title>

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="shortcut icon" href="{{ asset('kepicon.ico') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link href="{{ asset('css/peneliti.css') }}" rel="stylesheet">

    <style>
        /* =========================================================
           KETUA SIDEBAR THEME - PURPLE
        ========================================================= */

        #sidebar {
            background: linear-gradient(180deg, #24113F 0%, #3B1E6D 52%, #1A0D33 100%) !important;
            border-right: 1px solid rgba(255, 255, 255, .08);
        }

        #sidebar .sidebar-brand {
            border-bottom: 1px solid rgba(255, 255, 255, .12);
        }

        #sidebar .brand-shield {
            background: #FFFFFF !important;
            box-shadow: 0 8px 22px rgba(0, 0, 0, .24);
        }

        #sidebar .brand-text .name {
            color: #FFFFFF !important;
        }

        #sidebar .brand-text .role {
            color: #DDD6FE !important;
        }

        #sidebar .nav-section-label {
            color: #C4B5FD !important;
        }

        #sidebar .nav-item {
            color: #EDE9FE !important;
        }

        #sidebar .nav-item i {
            color: #DDD6FE !important;
        }

        #sidebar .nav-item:hover {
            background: rgba(255, 255, 255, .10) !important;
            color: #FFFFFF !important;
        }

        #sidebar .nav-item:hover i {
            color: #FFFFFF !important;
        }

        #sidebar .nav-item.active {
            background: linear-gradient(135deg, #7C3AED 0%, #6D28D9 100%) !important;
            color: #FFFFFF !important;
            box-shadow: 0 10px 24px rgba(0, 0, 0, .24);
        }

        #sidebar .nav-item.active i {
            color: #FFFFFF !important;
        }

        #sidebar .sidebar-toggle-btn,
        #sidebar .sidebar-expand-btn {
            color: #EDE9FE !important;
            background: rgba(255, 255, 255, .10) !important;
        }

        #sidebar .sidebar-toggle-btn:hover,
        #sidebar .sidebar-expand-btn:hover {
            background: rgba(255, 255, 255, .18) !important;
            color: #FFFFFF !important;
        }

        #sidebar .sidebar-footer {
            border-top: 1px solid rgba(255, 255, 255, .12);
        }

        #sidebar .user-card {
            background: rgba(255, 255, 255, .10) !important;
            border: 1px solid rgba(255, 255, 255, .10);
        }

        #sidebar .user-avatar {
            background: #DDD6FE !important;
            color: #24113F !important;
        }

        #sidebar .user-info .u-name {
            color: #FFFFFF !important;
        }

        #sidebar .user-info .u-email {
            color: #DDD6FE !important;
        }

        #sidebar .logout-btn {
            background: rgba(255, 255, 255, .10) !important;
            color: #EDE9FE !important;
            border: 1px solid rgba(255, 255, 255, .10);
        }

        #sidebar .logout-btn:hover {
            background: #B91C1C !important;
            color: #FFFFFF !important;
        }

        #sidebar .logout-btn i {
            color: inherit !important;
        }

        .topbar .sidebar-toggle-btn {
            color: #3B1E6D !important;
        }
    </style>

    {{-- SweetAlert2 --}}
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('styles')
</head>
<body>

<div class="kep-layout">

    {{-- TOPBAR MOBILE --}}
    <div class="topbar" id="mobileTopbar">
        <button class="sidebar-toggle-btn" id="mobileToggle"
                style="color:var(--navy-mid);font-size:1.3rem;">
            <i class="bi bi-list"></i>
        </button>

        <div style="display:flex;align-items:center;gap:.6rem;">
            <div style="width:32px;height:32px;background:#ede9fe;border-radius:10px;
                        display:flex;align-items:center;justify-content:center;
                        box-shadow:0 2px 8px rgba(0,0,0,.15);overflow:hidden;flex-shrink:0;">
                <img src="{{ asset('favicon-32x32.png') }}"
                     alt="Logo KEPTHREE"
                     style="width:26px;height:26px;object-fit:contain;">
            </div>

            <div style="display:flex;flex-direction:column;line-height:1.2;">
                <span style="font-weight:600;font-size:.9rem;color:var(--navy-deep);">KEPTHREE</span>
                <span style="font-size:.7rem;color:var(--text-muted);font-weight:500;">Ketua</span>
            </div>
        </div>
    </div>

    {{-- SIDEBAR --}}
    <nav id="sidebar">

        {{-- BRAND --}}
        <div class="sidebar-brand">
            <div class="brand-shield sidebar-brand-content">
                <img src="{{ asset('favicon-32x32.png') }}"
                     alt="Logo KEPTHREE"
                     style="width:30px;height:30px;object-fit:contain;">
            </div>

            <div class="brand-text sidebar-brand-content">
                <div class="name">KEPTHREE</div>
                <div class="role">Ketua</div>
            </div>

            <button class="sidebar-toggle-btn sidebar-brand-content"
                    id="sidebarCollapseBtn"
                    title="Ciutkan sidebar">
                <i class="bi bi-layout-sidebar-reverse"></i>
            </button>

            <button class="sidebar-expand-btn"
                    id="sidebarExpandBtn"
                    title="Buka sidebar">
                <i class="bi bi-layout-sidebar"></i>
            </button>
        </div>

        {{-- NAVIGATION --}}
        <nav class="sidebar-nav">
            <div class="nav-section-label">Menu Utama</div>

            <a href="{{ route('ketua.dashboard') }}"
               class="nav-item {{ request()->routeIs('ketua.dashboard') ? 'active' : '' }}"
               data-tooltip="Dashboard">
                <i class="bi bi-grid-1x2"></i>
                <span class="nav-label">Dashboard</span>
            </a>

            <a href="{{ route('ketua.ske.index') }}"
               class="nav-item {{ request()->routeIs('ketua.ske.*') ? 'active' : '' }}"
               data-tooltip="Tanda Tangan SKE">
                <i class="bi bi-pen"></i>
                <span class="nav-label">Tanda Tangan SKE</span>
            </a>

            <a href="{{ route('ketua.riwayat') }}"
               class="nav-item {{ request()->routeIs('ketua.riwayat*') ? 'active' : '' }}"
               data-tooltip="Riwayat TTD">
                <i class="bi bi-clock-history"></i>
                <span class="nav-label">Riwayat TTD</span>
            </a>
        </nav>

        {{-- FOOTER --}}
        <div class="sidebar-footer">
            <div class="user-card">
                <div class="user-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>

                <div class="user-info">
                    <div class="u-name">{{ auth()->user()->name }}</div>
                    <div class="u-email">{{ auth()->user()->email }}</div>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Keluar</span>
                </button>
            </form>
        </div>

    </nav>

    {{-- MAIN CONTENT --}}
    <main id="main">
        @yield('content')
    </main>

</div>

{{-- SIDEBAR OVERLAY MOBILE --}}
<div id="sidebarOverlay"
     style="display:none;position:fixed;inset:0;background:rgba(10,25,49,.45);
            z-index:150;backdrop-filter:blur(2px);
            opacity:0;transition:opacity .22s cubic-bezier(.4,0,.2,1);"
     onclick="closeMobileSidebar()"></div>

<script>
const sidebar      = document.getElementById('sidebar');
const bodyEl       = document.body;
const collapseBtn  = document.getElementById('sidebarCollapseBtn');
const expandBtn    = document.getElementById('sidebarExpandBtn');
const COLLAPSE_KEY = 'kep_ketua_sidebar_collapsed';

function isMobile() {
    return window.innerWidth <= 768;
}

function setSidebarState(collapsed) {
    if (collapsed) {
        sidebar.classList.add('collapsed');
        bodyEl.classList.add('sidebar-collapsed');
    } else {
        sidebar.classList.remove('collapsed');
        bodyEl.classList.remove('sidebar-collapsed');
    }

    localStorage.setItem(COLLAPSE_KEY, collapsed ? '1' : '0');
}

(function () {
    if (!isMobile() && localStorage.getItem(COLLAPSE_KEY) === '1') {
        setSidebarState(true);
    }
})();

collapseBtn.addEventListener('click', function (e) {
    e.stopPropagation();

    if (isMobile()) {
        closeMobileSidebar();
    } else {
        setSidebarState(true);
    }
});

expandBtn.addEventListener('click', function (e) {
    e.stopPropagation();
    setSidebarState(false);
});

function openMobileSidebar() {
    const overlay = document.getElementById('sidebarOverlay');

    overlay.style.display = 'block';
    overlay.getBoundingClientRect();
    overlay.style.opacity = '1';

    sidebar.classList.add('mobile-open');
}

function closeMobileSidebar() {
    const overlay = document.getElementById('sidebarOverlay');

    overlay.style.opacity = '0';
    sidebar.classList.remove('mobile-open');

    setTimeout(function () {
        overlay.style.display = 'none';
    }, 220);
}

const mobileToggle = document.getElementById('mobileToggle');

if (mobileToggle) {
    mobileToggle.addEventListener('click', openMobileSidebar);
}

let lastMobile = isMobile();

window.addEventListener('resize', function () {
    const nowMobile = isMobile();

    if (nowMobile === lastMobile) return;

    lastMobile = nowMobile;

    if (!nowMobile) {
        closeMobileSidebar();

        if (localStorage.getItem(COLLAPSE_KEY) === '1') {
            setSidebarState(true);
        } else {
            setSidebarState(false);
        }
    } else {
        closeMobileSidebar();
    }
});

document.querySelectorAll('.kep-alert.flash-alert').forEach(function (el) {
    setTimeout(function () {
        el.style.transition = 'opacity .5s, transform .5s';
        el.style.opacity = '0';
        el.style.transform = 'translateY(-4px)';

        setTimeout(function () {
            el.remove();
        }, 500);
    }, 6000);
});
</script>

@if(session('success'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: "{{ session('success') }}",
        confirmButtonText: 'Oke',
        confirmButtonColor: '#7C3AED',
        timer: 10000,
        timerProgressBar: true
    });
});
</script>
@endif

@if(session('error'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
        icon: 'error',
        title: 'Terjadi Kesalahan',
        text: "{{ session('error') }}",
        confirmButtonText: 'Oke',
        confirmButtonColor: '#dc3545'
    });
});
</script>
@endif

@stack('scripts')
</body>
</html>