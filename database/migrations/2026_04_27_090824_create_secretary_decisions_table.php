<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('secretary_decisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('protocol_id')->unique()->constrained('protocols')->cascadeOnDelete()
                  ->comment('Satu keputusan per protokol');
            $table->foreignId('secretary_id')->constrained('users')->restrictOnDelete();
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_approved_with_recommendation')->default(false);
            $table->boolean('is_resubmission')->default(false);
            $table->boolean('is_disapproved')->default(false)
                  ->comment('Hanya untuk full_board review');
            $table->text('exempted_reason')->nullable()
                  ->comment('Alasan jika review_type = exempted (PB-11)');
            $table->text('notes')->nullable();
            $table->timestamp('decided_at')->nullable()->useCurrent();

            $table->index('protocol_id', 'idx_secdec_protocol_id');
            $table->index('secretary_id', 'idx_secdec_secretary_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('secretary_decisions');
    }
};