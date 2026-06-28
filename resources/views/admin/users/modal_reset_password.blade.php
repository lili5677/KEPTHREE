<div class="modal-overlay" id="modalResetPassword">
    <div class="modal-card">
        <div class="modal-header">
            <div>
                <h2 class="modal-title">Reset Password</h2>
                <p class="modal-subtitle">
                    Reset password untuk <strong id="resetUserName">pengguna ini</strong>.
                </p>
            </div>

            <button type="button" class="modal-close" onclick="closeModal('modalResetPassword')">
                ×
            </button>
        </div>

        <form method="POST" id="formResetPassword">
            @csrf
            @method('PATCH')

            <input type="hidden" name="reset_user_id" id="resetUserId">
            <input type="hidden" name="reset_user_name" id="resetUserNameInput">

            <div class="modal-body">
                @if ($errors->resetPassword->any())
                    <div class="alert alert-error" style="margin-bottom: 14px;">
                        @foreach ($errors->resetPassword->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <div class="form-group">
                    <label>Password Baru</label>
                    <input
                        type="password"
                        name="password"
                        id="resetPassword"
                        class="form-control"
                        placeholder="Masukkan password baru"
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Konfirmasi Password Baru</label>
                    <input
                        type="password"
                        name="password_confirmation"
                        id="resetPasswordConfirmation"
                        class="form-control"
                        placeholder="Ulangi password baru"
                        required
                    >
                </div>

                <p style="font-size: 12.5px; color: var(--text-muted); line-height: 1.5; margin-top: 10px;">
                    Setelah password direset, pengguna harus login menggunakan password baru.
                </p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalResetPassword')">
                    Batal
                </button>

                <button type="submit" class="btn btn-primary">
                    Simpan Password
                </button>
            </div>
        </form>
    </div>
</div>