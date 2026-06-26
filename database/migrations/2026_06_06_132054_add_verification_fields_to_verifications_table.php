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
        Schema::table('verifications', function (Blueprint $table) {
            // Tambah kolom verified_at jika belum ada
            if (!Schema::hasColumn('verifications', 'verified_at')) {
                $table->timestamp('verified_at')->nullable();
            }
            
            // Tambah kolom notes jika belum ada
            if (!Schema::hasColumn('verifications', 'notes')) {
                $table->text('notes')->nullable();
            }
            
            // Tambah kolom status jika belum ada
            if (!Schema::hasColumn('verifications', 'status')) {
                $table->enum('status', ['lengkap', 'tidak_lengkap'])->nullable();
            }
            
            // Tambah kolom review_type jika belum ada
            if (!Schema::hasColumn('verifications', 'review_type')) {
                $table->enum('review_type', ['exempted', 'expedited', 'full_board'])->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('verifications', function (Blueprint $table) {
            $table->dropColumn(['verified_at', 'notes', 'status', 'review_type']);
        });
    }
};