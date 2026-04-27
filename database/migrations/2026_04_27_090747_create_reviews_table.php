<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('protocol_id')->constrained('protocols')->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->restrictOnDelete()
                  ->comment('Hanya role reviewer');
            $table->text('feedback')->nullable();
            $table->enum('recommendation', [
                'approved',
                'approved_with_recommendation',
                'resubmission',
                'disapproved',
            ])->nullable();
            $table->date('deadline')->comment('Deadline ditetapkan sekretaris');
            $table->boolean('is_locked')->default(false)
                  ->comment('true = feedback terkunci setelah disubmit (PB-14)');
            $table->timestamp('submitted_at')->nullable()
                  ->comment('NULL = reviewer belum selesai');
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->index('protocol_id', 'idx_reviews_protocol_id');
            $table->index('reviewer_id', 'idx_reviews_reviewer_id');
            $table->index(['protocol_id', 'reviewer_id'], 'idx_reviews_protocol_reviewer');
            $table->index('submitted_at', 'idx_reviews_submitted');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};