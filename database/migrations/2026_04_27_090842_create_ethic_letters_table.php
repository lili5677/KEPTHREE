<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ethic_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('protocol_id')->unique()->constrained('protocols')->cascadeOnDelete()
                  ->comment('Satu SKE per protokol');
            $table->text('draft_content')->nullable();
            $table->string('letter_number', 150)->nullable()->unique()
                  ->comment('Format [KODE]/[TAHUN]/[URUT]; diisi admin');
            $table->foreignId('chairman_id')->nullable()->constrained('users')->nullOnDelete()
                  ->comment('Ketua yang dipilih admin untuk TTD');
            $table->timestamp('researcher_confirmed_at')->nullable()
                  ->comment('Timestamp konfirmasi peneliti (PB-21)');
            $table->text('correction_notes')->nullable()
                  ->comment('Catatan koreksi dari peneliti (PB-21)');
            $table->timestamp('signed_at')->nullable()
                  ->comment('Timestamp TTD Ketua; dokumen terkunci setelahnya');
            $table->timestamp('sent_at')->nullable()
                  ->comment('Timestamp Admin kirim SKE ke peneliti');
            $table->timestamp('published_at')->nullable()
                  ->comment('Timestamp Admin publish SKE');
            $table->string('file_path', 500)->nullable()
                  ->comment('Path PDF SKE final setelah publish');
            $table->enum('status', [
                'draft',
                'numbered',
                'waiting_user',
                'waiting_signature',
                'signed',
                'issued',
            ])->default('draft');
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete()
                  ->comment('Sekretaris yang buat draft');
            $table->timestamps();

            $table->index('protocol_id', 'idx_ethicletters_protocol_id');
            $table->index('status', 'idx_ethicletters_status');
            $table->index('chairman_id', 'idx_ethicletters_chairman');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ethic_letters');
    }
};