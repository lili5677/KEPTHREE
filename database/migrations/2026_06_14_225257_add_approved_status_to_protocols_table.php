<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Ubah kolom status menjadi VARCHAR sementara
        Schema::table('protocols', function (Blueprint $table) {
            $table->string('status_temp')->nullable();
        });

        DB::statement('UPDATE protocols SET status_temp = status');

        Schema::table('protocols', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // Buat ulang dengan enum lengkap
        Schema::table('protocols', function (Blueprint $table) {
            $table->enum('status', [
                'new_proposal',
                'waiting_verification',
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
            ])->default('new_proposal');
        });

        DB::statement('UPDATE protocols SET status = status_temp');

        Schema::table('protocols', function (Blueprint $table) {
            $table->dropColumn('status_temp');
        });
    }

    public function down()
    {
        // Rollback ke enum sebelumnya
        Schema::table('protocols', function (Blueprint $table) {
            $table->string('status_temp')->nullable();
        });

        DB::statement('UPDATE protocols SET status_temp = status');

        Schema::table('protocols', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('protocols', function (Blueprint $table) {
            $table->enum('status', [
                'new_proposal',
                'waiting_verification',
                'under_review',
                'revision_required',
                'rejected'
            ])->default('new_proposal');
        });

        DB::statement('UPDATE protocols SET status = status_temp');

        Schema::table('protocols', function (Blueprint $table) {
            $table->dropColumn('status_temp');
        });
    }
};