<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('template_category', ['formulir_telaah_etik', 'ringkasan_protokol'])
                  ->default('formulir_telaah_etik')
                  ->comment('Jenis template wajib; hanya 1 aktif per kategori (PB-06, PB-07)');
            $table->text('description')->nullable();
            $table->string('file_name');
            $table->string('file_path', 500);
            $table->enum('file_type', ['docx', 'pdf'])->default('docx');
            $table->boolean('is_active')->default(true)
                  ->comment('Hanya 1 template aktif per kategori; upload baru menggantikan lama');
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['template_category', 'is_active'], 'uq_templates_active_category');
            $table->index('is_active', 'idx_templates_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};