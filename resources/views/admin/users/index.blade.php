<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen User - KEP</title>
</head>
<body>
    <h2>Manajemen User</h2>

    {{-- Pesan sukses --}}
    @if (session('success'))
        <p style="color:green">{{ session('success') }}</p>
    @endif

    <a href="{{ route('admin.users.create') }}">+ Tambah User</a>

    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->getRoleNames()->first() ?? '-' }}</td>
                <td>{{ $user->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <br>
    <a href="/admin/dashboard">← Kembali ke Dashboard</a>
</body>
</html>