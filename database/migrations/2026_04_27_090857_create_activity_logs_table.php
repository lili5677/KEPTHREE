<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('protocol_id')->constrained('protocols')->cascadeOnDelete();
            $table->enum('activity_type', [
                'protocol_submitted',
                'secretary_assigned',
                'document_verification',
                'revision_requested',
                'review_type_set',
                'exempted_approved',
                'reviewer_assigned',
                'reviewer_feedback_submitted',
                'secretary_decision_made',
                'revision_uploaded',
                'ske_draft_created',
                'ske_numbered',
                'ske_sent_to_researcher',
                'researcher_confirmed_ske',
                'researcher_correction_submitted',
                'ske_signed',
                'ske_sent',
                'ske_published',
                'ske_downloaded',
                'status_changed',
                'user_created',
                'user_edited',
                'user_deactivated',
                'user_reactivated',
                'template_uploaded',
                'template_replaced',
                'other',
            ]);
            $table->string('old_status', 100)->nullable();
            $table->string('new_status', 100)->nullable();
            $table->foreignId('performed_by')->constrained('users')->restrictOnDelete()
                  ->comment('User yang melakukan aktivitas');
            $table->text('notes')->nullable();
            $table->timestamp('performed_at')->useCurrent();

            $table->index('protocol_id', 'idx_actlog_protocol_id');
            $table->index('performed_at', 'idx_actlog_performed_at');
            $table->index(['protocol_id', 'performed_at'], 'idx_actlog_proto_time');
            $table->index('activity_type', 'idx_actlog_activity_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};