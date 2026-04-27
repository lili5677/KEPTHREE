<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('protocol_id')->constrained('protocols')->cascadeOnDelete();
            $table->enum('document_type', [
                'formulir_telaah_etik',
                'ringkasan_protokol',
                'surat_pengantar',
                'proposal',
                'icf',
                'kuesioner',
                'iklan',
                'brosur',
                'daftar_tim',
                'anggaran',
                'lainnya',
            ]);
            $table->string('file_name');
            $table->string('file_path', 500)->comment('Path relatif di storage server');
            $table->unsignedInteger('file_size')->comment('Ukuran file dalam bytes; maks 2MB');
            $table->boolean('is_revision')->default(false)
                  ->comment('true jika dokumen ini adalah revisi (PB-17)');
            $table->foreignId('uploaded_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('uploaded_at')->nullable()->useCurrent();

            $table->index('protocol_id', 'idx_documents_protocol_id');
            $table->index('document_type', 'idx_documents_type');
            $table->index('is_revision', 'idx_documents_is_revision');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};