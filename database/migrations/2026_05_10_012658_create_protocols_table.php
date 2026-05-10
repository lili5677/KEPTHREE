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
        Schema::create('protocols', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('sekretaris_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('ketua_penandatangan_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('program_studi');
            $table->string('sumber_pendanaan');
            $table->integer('durasi_penelitian');
            $table->text('ringkasan_penelitian');
            $table->enum('status', ['new_proposal', 'waiting_verification', 'under_review', 'revision_required', 'approved', 'rejected'])->default('new_proposal');
            $table->string('nomor_registrasi')->nullable();
            $table->boolean('is_confirmed_peneliti')->default(false);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('protocols');
    }
};
