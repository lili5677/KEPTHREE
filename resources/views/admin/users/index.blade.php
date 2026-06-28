@extends('layouts.admin')

@section('title', 'Manajemen User')

@push('styles')
<style>
    .top-bar {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 20px;
    }

    .filter-bar {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .filter-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-muted);
    }

    .filter-select,
    .search-input {
        padding: 9px 13px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 13px;
        font-family: var(--font);
        background: #ffffff;
        outline: none;
    }

    .search-input {
        width: 320px;
    }

    .filter-select:focus,
    .search-input:focus {
        border-color: var(--accent);
    }

    .table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead th {
        text-align: left;
        font-size: 11px;
        color: var(--text-muted);
        font-weight: 700;
        text-transform: uppercase;
        padding: 12px 16px;
        border-bottom: 1px solid var(--border);
        white-space: nowrap;
    }

    tbody td {
        padding: 14px 16px;
        border-bottom: 1px solid var(--border);
        font-size: 13px;
        vertical-align: middle;
        white-space: nowrap;
    }

    tbody tr:hover {
        background: #f9fafb;
    }

    .user-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .user-avatar {
        width: 32px;
        height: 32px;
        border-radius: 999px;
        background: #e0e7ff;
        color: #4f46e5;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 700;
        flex-shrink: 0;
    }

    .user-name {
        font-weight: 600;
        color: var(--text-primary);
    }

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .badge-admin { background: #ede9fe; color: #5b21b6; }
    .badge-ketua { background: #dbeafe; color: #1d4ed8; }
    .badge-reviewer { background: #dbeafe; color: #1d4ed8; }
    .badge-sekretariat { background: #dbeafe; color: #1d4ed8; }
    .badge-peneliti { background: #fef3c7; color: #92400e; }

    .badge-aktif {
        background: #dcfce7;
        color: #15803d;
    }

    .badge-nonaktif {
        background: #fee2e2;
        color: #b91c1c;
    }

    .action-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-action {
        border: none;
        background: none;
        padding: 0;
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        font-family: var(--font);
    }

    .btn-edit {
        color: var(--accent);
    }

    .btn-danger {
        color: #ef4444;
    }

    .btn-success {
        color: #16a34a;
    }

    .btn-warning {
        color: #d97706;
    }

    .action-divider {
        color: var(--border);
    }

    .empty-state {
        text-align: center;
        padding: 40px 0;
        color: var(--text-muted);
    }

    .pagination-wrapper {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 20px;
        padding: 14px 16px;
        border-top: 1px solid var(--border);
        font-size: 13px;
        color: var(--text-muted);
    }

    .pagination-pages {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .pg-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 32px;
        padding: 0 8px;
        border: 1.5px solid var(--border);
        border-radius: 7px;
        font-size: 13px;
        font-weight: 600;
        font-family: var(--font);
        text-decoration: none;
        color: var(--text-primary);
        background: #fff;
        transition: all 0.12s;
        cursor: pointer;
    }

    .pg-btn:hover {
        background: #f3f4f6;
    }

    .pg-btn.active {
        background: var(--accent);
        border-color: var(--accent);
        color: #fff;
    }

    .pg-btn.disabled {
        color: #c9cdd8;
        cursor: not-allowed;
        pointer-events: none;
    }

         .modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: rgba(15, 23, 42, 0.55);
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-card {
            width: 100%;
            max-width: 500px;
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.25);
            overflow: hidden;
            position: relative;
        }

        .modal-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            padding: 20px 22px;
            border-bottom: 1px solid var(--border);
            background: #ffffff;
        }

        .modal-title {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .modal-subtitle {
            margin: 4px 0 0;
            font-size: 13px;
            color: var(--text-muted);
        }

        .modal-close {
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 8px;
            background: #f3f4f6;
            color: var(--text-muted);
            font-size: 20px;
            cursor: pointer;
        }

        .modal-body {
            padding: 20px 22px;
            background: #ffffff;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 16px 22px;
            border-top: 1px solid var(--border);
            background: #ffffff;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 14px;
        }

        .form-group label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .form-control {
            width: 100%;
            height: 40px;
            padding: 0 12px;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            font-size: 13.5px;
            font-family: var(--font);
            color: var(--text-primary);
            background: #ffffff;
            outline: none;
        }

        .form-control:focus {
            border-color: var(--accent);
        }
        
    @media (max-width: 768px) {
        .top-bar {
            flex-direction: column;
            align-items: stretch;
        }

        .filter-bar {
            flex-direction: column;
            align-items: stretch;
            width: 100%;
        }

        .filter-select,
        .search-input {
            width: 100%;
        }

        .btn.btn-primary {
            width: 100%;
            justify-content: center;
        }

        table {
            min-width: 640px;
        }

        .action-group {
            flex-wrap: wrap;
        }
    }

    @media (max-width: 480px) {
        .page-title {
            font-size: 20px;
        }

        .page-subtitle {
            font-size: 12px;
        }

        .card {
            padding: 16px;
        }

        thead th,
        tbody td {
            padding: 12px;
        }
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title">Manajemen User</h1>
    <p class="page-subtitle">Kelola akun pengguna sistem</p>
</div>

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-error">
        {{ session('error') }}
    </div>
@endif

<div class="card">
    <div class="top-bar">
        <form method="GET" action="{{ route('admin.users.index') }}" id="filterForm">
            <div class="filter-bar">
                <span class="filter-label">Filter Role</span>

                <select name="role" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                    <option value="semua" {{ request('role', 'semua') === 'semua' ? 'selected' : '' }}>
                        Semua Role
                    </option>

                    @foreach ($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                            {{ ucfirst($role->name) }}
                        </option>
                    @endforeach
                </select>

                <span class="filter-label">Cari User</span>

                <input
                    type="text"
                    name="search"
                    class="search-input"
                    placeholder="Cari berdasarkan nama atau email..."
                    value="{{ request('search') }}"
                >
            </div>
        </form>

        <button type="button" class="btn btn-primary" onclick="openModal('modalTambah')">
            + Tambah User Baru
        </button>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($users as $user)
                    @php
                        $userRole = $user->getRoleNames()->first() ?? '-';

                        $roleClass = match ($userRole) {
                            'admin' => 'badge-admin',
                            'ketua' => 'badge-ketua',
                            'reviewer' => 'badge-reviewer',
                            'sekretariat' => 'badge-sekretariat',
                            'peneliti' => 'badge-peneliti',
                            default => 'badge-peneliti',
                        };
                    @endphp

                    <tr>
                        <td>
                            <div class="user-cell">
                                <div class="user-avatar">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <span class="user-name">{{ $user->name }}</span>
                            </div>
                        </td>

                        <td>{{ $user->email }}</td>

                        <td>
                            <span class="badge {{ $roleClass }}">
                                {{ ucfirst($userRole) }}
                            </span>
                        </td>

                        <td>
                            @if ($user->status === 'aktif')
                                <span class="badge badge-aktif">Aktif</span>
                            @else
                                <span class="badge badge-nonaktif">Nonaktif</span>
                            @endif
                        </td>

                        <td>
                            <div class="action-group">
                                <button
                                    type="button"
                                    class="btn-action btn-edit"
                                    onclick="openEditModal(
                                        {{ $user->id }},
                                        '{{ addslashes($user->name) }}',
                                        '{{ $user->email }}',
                                        '{{ $userRole }}'
                                    )"
                                >
                                    Edit
                                </button>

                                <span class="action-divider">|</span>
                                    <button
                                        type="button"
                                        class="btn-action btn-warning"
                                        data-user-id="{{ $user->id }}"
                                        data-user-name="{{ e($user->name) }}"
                                        onclick="openResetPasswordModalFromButton(this)"
                                    >
                                        Reset Password
                                    </button>

                                    <span class="action-divider">|</span>

                                @if ($user->status === 'aktif')
                                    <form
                                        method="POST"
                                        action="{{ route('admin.users.deactivate', $user) }}"
                                        onsubmit="return confirm('Nonaktifkan akun {{ addslashes($user->name) }}?')"
                                    >
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn-action btn-danger">
                                            Nonaktifkan
                                        </button>
                                    </form>
                                @else
                                    <form
                                        method="POST"
                                        action="{{ route('admin.users.activate', $user) }}"
                                        onsubmit="return confirm('Aktifkan kembali akun {{ addslashes($user->name) }}?')"
                                    >
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn-action btn-success">
                                            Aktifkan
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                Belum ada data user.
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($users->hasPages())
        @php
            $users->appends(request()->query());
        @endphp

        <div class="pagination-wrapper">
            <span>
                Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }}
                dari {{ $users->total() }} user
            </span>

            <div class="pagination-pages">
                {{-- Previous --}}
                @if ($users->onFirstPage())
                    <span class="pg-btn disabled">Previous</span>
                @else
                    <a href="{{ $users->previousPageUrl() }}" class="pg-btn">Previous</a>
                @endif

                {{-- Page numbers --}}
                @foreach ($users->getUrlRange(max(1, $users->currentPage() - 2), min($users->lastPage(), $users->currentPage() + 2)) as $page => $url)
                    @if ($page == $users->currentPage())
                        <span class="pg-btn active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="pg-btn">{{ $page }}</a>
                    @endif
                @endforeach

                {{-- Next --}}
                @if ($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}" class="pg-btn">Next</a>
                @else
                    <span class="pg-btn disabled">Next</span>
                @endif
            </div>
        </div>
    @endif
</div>

@include('admin.users.modal_tambah')
@include('admin.users.modal_edit')
@include('admin.users.modal_reset_password')
@endsection

@push('scripts')
<script>
    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    document.querySelectorAll('.modal-overlay').forEach(function (overlay) {
        overlay.addEventListener('click', function (event) {
            if (event.target === overlay) {
                closeModal(overlay.id);
            }
        });
    });

    function openEditModal(id, name, email, role) {
        document.getElementById('editName').value = name;
        document.getElementById('editEmail').value = email;
        document.getElementById('editRole').value = role;

        document.getElementById('formEdit').action = "{{ url('/admin/users') }}/" + id;

        openModal('modalEdit');
    }

    function openResetPasswordModalFromButton(button) {
        const id = button.dataset.userId;
        const name = button.dataset.userName;

        openResetPasswordModal(id, name);
    }

    function openResetPasswordModal(id, name) {
        document.getElementById('resetUserId').value = id;
        document.getElementById('resetUserNameInput').value = name;
        document.getElementById('resetUserName').textContent = name;

        document.getElementById('resetPassword').value = '';
        document.getElementById('resetPasswordConfirmation').value = '';

        document.getElementById('formResetPassword').action =
            "{{ url('/admin/users') }}/" + id + "/reset-password";

        openModal('modalResetPassword');
    }

    const searchInput = document.querySelector('.search-input');

    if (searchInput) {
        let typingTimer;

        searchInput.addEventListener('input', function () {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(function () {
                document.getElementById('filterForm').submit();
            }, 600);
        });
    }

    @if ($errors->resetPassword->any())
        openModal('modalResetPassword');

        @if (old('reset_user_id'))
            document.getElementById('resetUserId').value = "{{ old('reset_user_id') }}";
            document.getElementById('resetUserNameInput').value = "{{ old('reset_user_name') }}";
            document.getElementById('resetUserName').textContent = "{{ old('reset_user_name', 'pengguna ini') }}";
            document.getElementById('formResetPassword').action =
                "{{ url('/admin/users') }}/{{ old('reset_user_id') }}/reset-password";
        @endif
    @elseif ($errors->any())
        openModal('modalTambah');
    @endif
</script>
@endpush