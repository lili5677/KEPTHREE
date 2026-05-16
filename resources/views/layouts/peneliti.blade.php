<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Sistem KEP</title>

    <link rel="icon" type="image/png" sizes="32x32"  href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16"  href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180"      href="{{ asset('apple-touch-icon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/peneliti.css') }}" rel="stylesheet">

    {{-- SweetAlert2 CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('styles')
</head>
<body>

<div class="kep-layout">

    {{-- ═══ TOPBAR (mobile) ═══ --}}
    <div class="topbar" id="mobileTopbar">
        <button class="sidebar-toggle-btn" id="mobileToggle"
                style="color:var(--navy-mid);font-size:1.3rem;">
            <i class="bi bi-list"></i>
        </button>
        <div style="display:flex;align-items:center;gap:.6rem;">
            <div style="width:28px;height:28px;background:var(--blue-accent);border-radius:8px;
                        display:flex;align-items:center;justify-content:center;color:#fff;font-size:.85rem;">
                <i class="bi bi-shield-check"></i>
            </div>
            <span style="font-weight:600;font-size:.9rem;color:var(--navy-deep);">Sistem KEP</span>
        </div>
    </div>

    {{-- ═══ SIDEBAR ═══ --}}
    <nav id="sidebar">

        {{-- Brand --}}
        <div class="sidebar-brand">
            {{-- Brand content: visible only when expanded --}}
            <div class="brand-shield sidebar-brand-content">
                <i class="bi bi-shield-check"></i>
            </div>
            <div class="brand-text sidebar-brand-content">
                <div class="name">Sistem KEP</div>
                <div class="role">Peneliti</div>
            </div>
            {{-- Collapse btn: visible when expanded --}}
            <button class="sidebar-toggle-btn sidebar-brand-content" id="sidebarCollapseBtn" title="Ciutkan sidebar">
                <i class="bi bi-layout-sidebar-reverse"></i>
            </button>
            {{-- Expand btn: visible only when collapsed --}}
            <button class="sidebar-expand-btn" id="sidebarExpandBtn" title="Buka sidebar">
                <i class="bi bi-layout-sidebar"></i>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="sidebar-nav">
            <div class="nav-section-label">Menu Utama</div>

            <a href="{{ route('peneliti.dashboard') }}"
               class="nav-item {{ request()->routeIs('peneliti.dashboard') ? 'active' : '' }}"
               data-tooltip="Dashboard">
                <i class="bi bi-grid-1x2"></i>
                <span class="nav-label">Dashboard</span>
            </a>

            <a href="{{ route('peneliti.pengajuan.create') }}"
               class="nav-item {{ request()->routeIs('peneliti.pengajuan.*') ? 'active' : '' }}"
               data-tooltip="Pengajuan Baru">
                <i class="bi bi-plus-square"></i>
                <span class="nav-label">Pengajuan Baru</span>
            </a>

            <a href="{{ route('peneliti.riwayat') }}"
               class="nav-item {{ request()->routeIs('peneliti.riwayat') ? 'active' : '' }}"
               data-tooltip="Riwayat Pengajuan">
                <i class="bi bi-clock-history"></i>
                <span class="nav-label">Riwayat Pengajuan</span>
            </a>

            <div class="nav-section-label">Sumber Daya</div>

            <a href="{{ route('peneliti.template') }}"
               class="nav-item {{ request()->routeIs('peneliti.template*') ? 'active' : '' }}"
               data-tooltip="Download Template">
                <i class="bi bi-download"></i>
                <span class="nav-label">Download Template</span>
            </a>
        </nav>

        {{-- Footer --}}
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

    {{-- ═══ MAIN ═══ --}}
    <main id="main">
        @yield('content')
    </main>

</div>

{{-- Sidebar overlay (mobile) --}}
<div id="sidebarOverlay"
     style="display:none;position:fixed;inset:0;background:rgba(10,25,49,.45);
            z-index:150;backdrop-filter:blur(2px);"
     onclick="closeMobileSidebar()"></div>

<script>
/* ── Sidebar collapse / expand ── */
const sidebar      = document.getElementById('sidebar');
const bodyEl       = document.body;
const collapseBtn  = document.getElementById('sidebarCollapseBtn');
const expandBtn    = document.getElementById('sidebarExpandBtn');
const COLLAPSE_KEY = 'kep_sidebar_collapsed';

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

// Restore saved state on load
(function () {
    if (localStorage.getItem(COLLAPSE_KEY) === '1') {
        setSidebarState(true);
    }
})();

collapseBtn.addEventListener('click', function (e) {
    e.stopPropagation();
    setSidebarState(true);
});

expandBtn.addEventListener('click', function (e) {
    e.stopPropagation();
    setSidebarState(false);
});

/* ── Mobile sidebar ── */
function openMobileSidebar() {
    sidebar.classList.add('mobile-open');
    document.getElementById('sidebarOverlay').style.display = 'block';
}
function closeMobileSidebar() {
    sidebar.classList.remove('mobile-open');
    document.getElementById('sidebarOverlay').style.display = 'none';
}

const mobileToggle = document.getElementById('mobileToggle');
if (mobileToggle) mobileToggle.addEventListener('click', openMobileSidebar);

/* ── Auto-dismiss flash alerts ── */
document.querySelectorAll('.kep-alert.flash-alert').forEach(function (el) {
    setTimeout(function () {
        el.style.transition = 'opacity .5s, transform .5s';
        el.style.opacity    = '0';
        el.style.transform  = 'translateY(-4px)';
        setTimeout(function () { el.remove(); }, 500);
    }, 6000);
});
</script>

{{-- SweetAlert untuk session messages --}}
@if(session('success'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'success',
        title: '✅ Berhasil!',
        text: "{{ session('success') }}",
        confirmButtonText: 'Oke',
        confirmButtonColor: '#4A7FA7',
        timer: 10000,
        timerProgressBar: true
    });
});
</script>
@endif

@if(session('error'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'error',
        title: '❌ Terjadi Kesalahan',
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