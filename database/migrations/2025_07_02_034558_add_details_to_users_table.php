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
        Schema::table('users', function (Blueprint $table) {
            // Tambahkan kolom setelah kolom 'password'
            $table->after('password', function (Blueprint $table) {
                $table->foreignId('job_title_id')->nullable()->constrained()->onDelete('set null');
                $table->foreignId('organization_id')->nullable()->constrained()->onDelete('set null');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus constraint dulu sebelum menghapus kolom
            $table->dropForeign(['job_title_id']);
            $table->dropForeign(['organization_id']);
            $table->dropColumn(['job_title_id', 'organization_id']);
        });
    }
};