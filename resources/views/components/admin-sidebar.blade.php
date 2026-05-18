<aside class="sidebar">

    {{-- Brand --}}
    <div class="sidebar-brand">
        <div class="sidebar-brand-logo sidebar-brand-content">
            <img src="{{ asset('favicon-32x32.png') }}" alt="Logo KEPTHREE">
        </div>

        <div class="sidebar-brand-text sidebar-brand-content">
            <div class="sidebar-brand-title">KEPTHREE</div>
            <div class="sidebar-brand-sub">Admin</div>
        </div>

        <button type="button"
                class="sidebar-collapse-btn sidebar-brand-content"
                id="adminSidebarCollapseBtn"
                title="Ciutkan sidebar">
            <i class="bi bi-layout-sidebar-reverse"></i>
        </button>

        <button type="button"
                class="sidebar-expand-btn"
                id="adminSidebarExpandBtn"
                title="Buka sidebar">
            <i class="bi bi-layout-sidebar"></i>
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar-nav">

        {{-- Dashboard --}}
        <a href="{{ route('admin.dashboard') }}"
           class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
           data-tooltip="Dashboard">
            <svg viewBox="0 0 24 24">
                <rect x="3" y="3" width="7" height="7" rx="1"/>
                <rect x="14" y="3" width="7" height="7" rx="1"/>
                <rect x="3" y="14" width="7" height="7" rx="1"/>
                <rect x="14" y="14" width="7" height="7" rx="1"/>
            </svg>
            <span class="sidebar-link-text">Dashboard</span>
        </a>

        {{-- Assign Sekretariat --}}
        <a href="#"
           data-tooltip="Assign Sekretariat">
            <svg viewBox="0 0 24 24">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
            </svg>
            <span class="sidebar-link-text">Assign Sekretariat</span>
        </a>

        {{-- Manajemen User --}}
        <a href="{{ route('admin.users.index') }}"
           class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
           data-tooltip="Manajemen User">
            <svg viewBox="0 0 24 24">
                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
            </svg>
            <span class="sidebar-link-text">Manajemen User</span>
        </a>

        {{-- Manajemen Template --}}
        <a href="{{ route('admin.template.index') }}"
           class="{{ request()->routeIs('admin.template.*') ? 'active' : '' }}"
           data-tooltip="Manajemen Template">
            <svg viewBox="0 0 24 24">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
            </svg>
            <span class="sidebar-link-text">Manajemen Template</span>
        </a>

        {{-- Semua Dokumen --}}
        <a href="#"
           data-tooltip="Semua Dokumen">
            <svg viewBox="0 0 24 24">
                <path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/>
            </svg>
            <span class="sidebar-link-text">Semua Dokumen</span>
        </a>

        {{-- Ethical Clearance --}}
        <a href="#"
           data-tooltip="Ethical Clearance">
            <svg viewBox="0 0 24 24">
                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="sidebar-link-text">Ethical Clearance</span>
        </a>

        {{-- Log Aktivitas --}}
        <a href="#"
           data-tooltip="Log Aktivitas">
            <svg viewBox="0 0 24 24">
                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
            </svg>
            <span class="sidebar-link-text">Log Aktivitas</span>
        </a>

    </nav>

    {{-- Footer: User Info + Logout --}}
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-avatar">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>

            <div class="sidebar-user-info">
                <div class="sidebar-user-name">{{ auth()->user()->name ?? 'Admin System' }}</div>
                <div class="sidebar-user-email">{{ auth()->user()->email ?? 'admin@gmail.com' }}</div>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout" data-tooltip="Logout">
                <svg viewBox="0 0 24 24">
                    <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
                <span class="sidebar-link-text">Logout</span>
            </button>
        </form>
    </div>

</aside>