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
        Schema::create('peserta_laris', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_bib')->unique(); // Add BIB number
            $table->string('nama_lengkap');
            $table->string('kategori_lari', 100);
            $table->string('email')->unique();
            $table->string('telepon', 20);
            $table->string('qr_token', 64)->unique();
            $table->string('qr_code_path')->nullable();
            $table->enum('status', ['terdaftar', 'konfirmasi', 'hadir', 'tidak_hadir'])->default('terdaftar');
            $table->timestamp('waktu_checkin')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['kategori_lari', 'status']);
            $table->index('nomor_bib');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peserta_laris');
    }
};