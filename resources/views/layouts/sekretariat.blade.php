<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard Sekretariat') — KEPTHREE</title>

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="shortcut icon" href="{{ asset('kepicon.ico') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link href="{{ asset('css/peneliti.css') }}" rel="stylesheet">

    <style>
      /* =========================================================
        SEKRETARIAT SIDEBAR THEME - RED
        ========================================================= */

        #sidebar {
            background: linear-gradient(180deg, #450A0A 0%, #7F1D1D 52%, #1F0A0A 100%) !important;
            border-right: 1px solid rgba(255, 255, 255, .08);
        }

        #sidebar .sidebar-brand {
            border-bottom: 1px solid rgba(255, 255, 255, .12);
        }

        #sidebar .brand-shield {
            background: rgb(255 255 255) !important;
            box-shadow: 0 8px 22px rgba(0, 0, 0, .22);
        }

        #sidebar .brand-text .name {
            color: #FFFFFF !important;
        }

        #sidebar .brand-text .role {
            color: #FECACA !important;
        }

        #sidebar .nav-section-label {
            color: #FCA5A5 !important;
        }

        #sidebar .nav-item {
            color: #FEE2E2 !important;
        }

        #sidebar .nav-item i {
            color: #FECACA !important;
        }

        #sidebar .nav-item:hover {
            background: rgba(255, 255, 255, .10) !important;
            color: #FFFFFF !important;
        }

        #sidebar .nav-item:hover i {
            color: #FFFFFF !important;
        }

        #sidebar .nav-item.active {
            background: linear-gradient(135deg, #DC2626 0%, #B91C1C 100%) !important;
            color: #FFFFFF !important;
            box-shadow: 0 10px 24px rgba(0, 0, 0, .22);
        }

        #sidebar .nav-item.active i {
            color: #FFFFFF !important;
        }

        #sidebar .sidebar-toggle-btn,
        #sidebar .sidebar-expand-btn {
            color: #FEE2E2 !important;
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
            background: #FECACA !important;
            color: #450A0A !important;
        }

        #sidebar .user-info .u-name {
            color: #FFFFFF !important;
        }

        #sidebar .user-info .u-email {
            color: #FECACA !important;
        }

        #sidebar .logout-btn {
            background: rgba(255, 255, 255, .10) !important;
            color: #FEE2E2 !important;
            border: 1px solid rgba(255, 255, 255, .10);
        }

        #sidebar .logout-btn:hover {
            background: #991B1B !important;
            color: #FFFFFF !important;
        }

        #sidebar .logout-btn i {
            color: inherit !important;
        }

        .topbar .sidebar-toggle-btn {
            color: #7F1D1D !important;
        }
    </style>

    {{-- SweetAlert2 --}}
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('styles')
</head>

<body>
@php
    $authUser = auth()->user();

    $manajemenReviewerUrl = '#';

    if (\Illuminate\Support\Facades\Route::has('sekretariat.review.index')) {
        $manajemenReviewerUrl = route('sekretariat.review.index');
    } elseif (\Illuminate\Support\Facades\Route::has('sekretariat.assign-reviewer.index')) {
        $manajemenReviewerUrl = route('sekretariat.assign-reviewer.index');
    } elseif (\Illuminate\Support\Facades\Route::has('sekretariat.assignment.index')) {
        $manajemenReviewerUrl = route('sekretariat.assignment.index');
    }

    $riwayatProposalUrl = '#';

    if (\Illuminate\Support\Facades\Route::has('sekretariat.riwayat.index')) {
        $riwayatProposalUrl = route('sekretariat.riwayat.index');
    } elseif (\Illuminate\Support\Facades\Route::has('sekretariat.riwayat')) {
        $riwayatProposalUrl = route('sekretariat.riwayat');
    }
@endphp

<div class="kep-layout">

    {{-- TOPBAR MOBILE --}}
    <div class="topbar" id="mobileTopbar">
        <button class="sidebar-toggle-btn" id="mobileToggle"
                style="color:var(--navy-mid);font-size:1.3rem;">
            <i class="bi bi-list"></i>
        </button>

        <div style="display:flex;align-items:center;gap:.6rem;">
            <div style="width:32px;height:32px;background:#d0e3f0;border-radius:10px;
                        display:flex;align-items:center;justify-content:center;
                        box-shadow:0 2px 8px rgba(0,0,0,.15);overflow:hidden;flex-shrink:0;">
                <img src="{{ asset('favicon-32x32.png') }}"
                     alt="Logo KEPTHREE"
                     style="width:26px;height:26px;object-fit:contain;">
            </div>

            <div style="display:flex;flex-direction:column;line-height:1.2;">
                <span style="font-weight:600;font-size:.9rem;color:var(--navy-deep);">KEPTHREE</span>
                <span style="font-size:.7rem;color:var(--text-muted);font-weight:500;">Sekretariat</span>
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
                <div class="role">Sekretariat</div>
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

            <a href="{{ $manajemenReviewerUrl }}"
               class="nav-item {{ request()->routeIs('sekretariat.review.*') || request()->routeIs('sekretariat.assign-reviewer.*') || request()->routeIs('sekretariat.assignment.*') ? 'active' : '' }}"
               data-tooltip="Manajemen Reviewer">
                <i class="bi bi-people"></i>
                <span class="nav-label">Manajemen Reviewer</span>
            </a>

            <a href="{{ route('sekretariat.decision.index') }}"
               class="nav-item {{ request()->routeIs('sekretariat.decision.*') ? 'active' : '' }}"
               data-tooltip="Secretary Decision">
                <i class="bi bi-check2-circle"></i>
                <span class="nav-label">Secretary Decision</span>
            </a>

            <a href="{{ $riwayatProposalUrl }}"
               class="nav-item {{ request()->routeIs('sekretariat.riwayat*') ? 'active' : '' }}"
               data-tooltip="Riwayat Proposal">
                <i class="bi bi-clock-history"></i>
                <span class="nav-label">Riwayat Proposal</span>
            </a>
        </nav>

        {{-- FOOTER --}}
        <div class="sidebar-footer">
            <div class="user-card">
                <div class="user-avatar">
                    {{ strtoupper(substr($authUser->name ?? 'S', 0, 1)) }}
                </div>

                <div class="user-info">
                    <div class="u-name">{{ $authUser->name ?? 'Sekretariat' }}</div>
                    <div class="u-email">{{ $authUser->email ?? 'sekretariat@kep.ac.id' }}</div>
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
const COLLAPSE_KEY = 'kep_sekretariat_sidebar_collapsed';

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
        confirmButtonColor: '#B91C1C',
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