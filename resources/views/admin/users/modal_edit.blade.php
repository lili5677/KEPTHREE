<div class="modal-overlay" id="modalEdit">
    <div class="modal-box">
        <div class="modal-header">
            <h2 class="modal-title">Edit Data User</h2>
            <button type="button" class="modal-close" onclick="closeModal('modalEdit')">×</button>
        </div>

        <form method="POST" id="formEdit" action="">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Nama Lengkap <span>*</span></label>
                <input type="text" name="name" id="editName" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">Email <span>*</span></label>
                <input type="email" name="email" id="editEmail" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">Role <span>*</span></label>
                <select name="role" id="editRole" class="form-control" required>
                    <option value="admin">Admin</option>
                    <option value="ketua">Ketua</option>
                    <option value="reviewer">Reviewer</option>
                    <option value="sekretariat">Sekretariat</option>
                    <option value="peneliti">Peneliti</option>
                </select>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeModal('modalEdit')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>