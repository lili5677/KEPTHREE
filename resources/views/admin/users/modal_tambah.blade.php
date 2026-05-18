<div class="modal-overlay" id="modalTambah">
    <div class="modal-box">
        <div class="modal-header">
            <h2 class="modal-title">Tambah User Baru</h2>
            <button type="button" class="modal-close" onclick="closeModal('modalTambah')">×</button>
        </div>

        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Nama Lengkap <span>*</span></label>
                <input type="text" name="name" class="form-control" placeholder="Dr. Nama Lengkap" value="{{ old('name') }}" required>

                @error('name')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Email <span>*</span></label>
                <input type="email" name="email" class="form-control" placeholder="email@example.com" value="{{ old('email') }}" required>

                @error('email')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Role <span>*</span></label>

                <select name="role" class="form-control" required>
                    <option value="" disabled {{ old('role') ? '' : 'selected' }}>-- Pilih Role --</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="ketua" {{ old('role') === 'ketua' ? 'selected' : '' }}>Ketua</option>
                    <option value="reviewer" {{ old('role') === 'reviewer' ? 'selected' : '' }}>Reviewer</option>
                    <option value="sekretariat" {{ old('role') === 'sekretariat' ? 'selected' : '' }}>Sekretariat</option>
                    <option value="peneliti" {{ old('role') === 'peneliti' ? 'selected' : '' }}>Peneliti</option>
                </select>

                @error('role')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-hint">
                Password akan dibuat otomatis dan ditampilkan setelah akun berhasil dibuat.
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeModal('modalTambah')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>