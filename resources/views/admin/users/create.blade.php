<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah User - KEP</title>
</head>
<body>
    <h2>Tambah User Baru</h2>

    {{-- Tampilkan error --}}
    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li style="color:red">{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf

        <div>
            <label>Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name') }}" required>
        </div>

        <div>
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required>
        </div>

        <div>
            <label>Role</label>
            <select name="role" required>
                <option value="">-- Pilih Role --</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                        {{ ucfirst($role->name) }}
                    </option>
                @endforeach
            </select>
        </div>

        <br>
        <button type="submit">Simpan</button>
        <a href="{{ route('admin.users.index') }}">Batal</a>
    </form>
</body>
</html>