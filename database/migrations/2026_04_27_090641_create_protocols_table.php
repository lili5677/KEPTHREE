<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('protocols', function (Blueprint $table) {
            $table->id();
            $table->string('protocol_number', 100)->nullable()->unique()
                  ->comment('Format [KODE-INSTITUSI]/[TAHUN]/[NOMOR-URUT]');
            $table->string('title');
            $table->string('sponsor')->nullable();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete()
                  ->comment('Peneliti pengaju');
            $table->foreignId('secretary_id')->nullable()->constrained('users')->nullOnDelete()
                  ->comment('Sekretaris yang ditugaskan; nullable sampai ditugaskan');
            $table->enum('status', [
                'new_proposal',
                'assigned_to_secretary',
                'in_process',
                'revision_required',
                'on_review_expedited',
                'on_review_full_board',
                'revised',
                'approved',
                'approved_with_recommendation',
                'disapproved',
                'issued',
                'done',
            ])->default('new_proposal');
            $table->enum('review_type', ['exempted', 'expedited', 'full_board'])->nullable()
                  ->comment('Ditetapkan sekretaris saat verifikasi');
            $table->text('revision_notes')->nullable()
                  ->comment('Catatan kekurangan dokumen dari sekretaris ke peneliti (PB-10)');
            $table->timestamp('verified_at')->nullable()
                  ->comment('Timestamp sekretaris selesai verifikasi (PB-10)');
            $table->date('panel_meeting_date')->nullable()
                  ->comment('Tanggal rapat panel full board (PB-13)');
            $table->text('panel_meeting_notes')->nullable()
                  ->comment('Catatan hasil rapat panel (PB-13)');
            $table->timestamp('submitted_at')->nullable()
                  ->comment('Waktu peneliti submit ajuan');
            $table->timestamps();

            $table->index('status', 'idx_protocols_status');
            $table->index('user_id', 'idx_protocols_user_id');
            $table->index('secretary_id', 'idx_protocols_secretary');
            $table->index('review_type', 'idx_protocols_review_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('protocols');
    }
};