<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ske_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('protocol_id')->constrained('protocols')->onDelete('cascade');

            // Nomor & ketua — diisi admin
            $table->string('nomor_surat')->unique();
            $table->foreignId('ketua_id')->constrained('users')->onDelete('cascade');

            // Tanggal terbit (dipakai di body surat)
            $table->date('tanggal_terbit');

            // File hasil generate (sebelum & sesudah TTD)
            $table->string('file_path')->nullable();          // draft / belum ttd
            $table->string('signed_file_path')->nullable();   // sudah ttd ketua

            // Status alur SKE
            $table->enum('status', [
                'draft',               // baru dibuat admin, belum dikirim ke peneliti
                'menunggu_konfirmasi',  // sudah dikirim ke peneliti, menunggu cek
                'revisi',               // peneliti minta revisi, catatan terisi
                'menunggu_ttd',         // tidak ada revisi / revisi selesai, terkirim ke ketua
                'sudah_ttd',            // ketua sudah ttd, kembali ke admin
                'terbit',               // admin terbitkan ke peneliti (final)
            ])->default('draft');

            // Catatan revisi dari peneliti
            $table->text('catatan_revisi')->nullable();

            // Riwayat siapa & kapan tiap tahap terjadi (opsional, untuk audit ringan)
            $table->timestamp('dikirim_ke_peneliti_at')->nullable();
            $table->timestamp('direvisi_at')->nullable();
            $table->timestamp('dikirim_ke_ketua_at')->nullable();
            $table->timestamp('ditandatangani_at')->nullable();
            $table->timestamp('diterbitkan_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ske_documents');
    }
};