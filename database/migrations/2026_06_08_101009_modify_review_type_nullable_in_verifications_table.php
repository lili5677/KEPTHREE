<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('protocols', function (Blueprint $table) {
            $table->enum('status', [
                'new_proposal',
                'assigned_to_secretary',
                'ready_for_reviewer_assignment',
                'on_review',
                'revision_required',
                'revised',
                'waiting_secretary_decision',
                'approved',
                'approved_with_recommendation',
                'disapproved',
                'issued'
            ])->default('new_proposal')->change();
        });
    }

    public function down()
    {
        Schema::table('protocols', function (Blueprint $table) {
            $table->enum('status', [
                'new_proposal',
                'waiting_verification',
                'under_review',
                'revision_required',
                'rejected'
            ])->default('new_proposal')->change();
        });
    }
};